<?php

namespace App\Console\Commands;
use App\Models\Task;
use App\Models\TaskImage;
use App\Services\Common;
use App\Services\ImageService;
use Illuminate\Support\Facades\Redis;
use App\Support\Alogd;
use App\Support\Helper;
use App\Support\GlobalCode;
use App\Support\ReverbClient;
use App\Support\TuseMessage;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;

class LocalEngineListener extends Command
{
    /**
     * subscribe atz.res
     * LocalEngineListener
     * @var string
     */
    protected $signature = 'LocalEngineListener';
    protected $description = 'listen and push msg to frontend';
    protected $redis;
    protected $debug = true;
    protected $taskImage = null;
    protected $waitTime = 2;
    protected $saveImages = [];

    /**
     * php artisan LocalEngineListener
     * php .\artisan php artisan reverb:start --host="0.0.0.0" --port=8080 --debug
     */
     public function handle(){
        App::setlocale(env('APP_LOCALE'));
        $i = 0;
        $this->redis = Redis::connection();
        while(true){
            if($i > 100000){
                return;
            }
            $this->taskImage = null;
            $this->saveImages = [];
            $resChannel = GlobalCode::CHANNEL_LIST['ATZ-RES'];
            $package = $this->redis->rpop($resChannel);
            
            
            // $package = '{"client_id": "WH-WK-20240124", "access_key": "spfp2345234dfgsdfg990", "job_type": "MK", "status": "success", "message_type": "user", "storage": "server", "msg": "\u6b63\u5728\u4e0b\u8f7d", "width": 1792, "height": 2304, "size": 258893, "id": 179, "big": "storage/202406/5e45186f2f6aaac293f873c1c5973e17.jpeg", "thumb": "storage/202406/5aff67a093df83e0c09d83114873483d.jpeg", "user_id": 1, "task_id": "240612881148080128", "images_big": [{"width": 1792, "height": 2304, "size": 258893, "path": "storage/202406/5e45186f2f6aaac293f873c1c5973e17.jpeg"}], "images_thumb": [{"width": 1792, "height": 2304, "size": 258893, "path": "storage/202406/5aff67a093df83e0c09d83114873483d.jpeg"}], "upload_time": 0.01, "generate_time": -1, "base64": null, "used_time": 12.99}';
            
            $message = json_decode($package, true);

            //heartbeat, no use
            if(isset($message['job_type']) && 'heartbeat' == $message['job_type']){
                Cache::put(GlobalCode::CLIENT_HEARTBEAT_KEY, time(), 3600);
                continue;
            }
            
            if(!$message){
                $i++;
                echo "LocalEngineListener: no message, wait:{$this->waitTime}秒\n";
                sleep($this->waitTime);
                continue;
            }
            $task_id = $message['task_id']??'';
            $message_status = $message['status']??'';
            Alogd::write("LocalEngineListener", "Get message from redis, task_id={$task_id} status={$message_status}", $this->debug);
            // Alogd::write("LocalEngineListener", $package, $this->debug);
            if(isset($message['task_owner']) && $message['task_owner'] == 'tuse'){
                $this->forward($message);
            }else{
                $this->process($message);
            }
            
            $i++;
        }
        echo "Total {$i} , break and release mem\n";
    }

    //part job res ,post to /v1/job/notify
    public function forward(array $message){
        Alogd::write("LocalEngineListener::forward", "Sync job task_id=".$message['task_id'], $this->debug);
        //todo
        unset($message['access_key']);
        $access_key = Common::getConfigKeyValue('engine.atz.token');
        if(!$access_key){
            Alogd::write("LocalEngineListener::forward", "Lost message, access_key can not be null", $this->debug);
            return;
        }
        $options = [
            'headers' => [
                'Authorization' => 'Bearer '. $access_key,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($message),
            'connect_timeout' => 3,
            'read_timeout' => 10,
            'timeout' => 3,
        ];
      
        try{
            $client = new Client();
            $res = $client->Request('POST', env('TUSE_API').'/v1/job/notify', $options);
            $content = $res->getBody()->getContents();
            $json = json_decode($content, true);
            if(isset($json['code']) && $json['code'] == GlobalCode::SUCCESS){
                Alogd::write("LocalEngineListener::forward", "Sync job success:task_id=".$message['task_id'], $this->debug);
            }else{
                Alogd::write("LocalEngineListener::forward", "Sync job failed:task_id=".$message['task_id'], $this->debug);
            }
        }catch(Exception $e){
            Alogd::write("LocalEngineListener::forward", "Sync job failed:message=". $e->getMessage(), $this->debug);
        }
        
    }

    public function process(array $message){
        $execTime = 0;
        $task = Task::where('task_id', $message['task_id'])->first();
        if(!$task){
            Alogd::write("LocalEngineListener", "\ntask:{$message['task_id']} not found, drop the msg", $this->debug);
            return;
        }
        try{
            $tm = new TuseMessage($message);
            $tm->validator();
            $execTime = Helper::getExecTime($task);
            // ReverbClient::sendMessage($tm->package('ephemeral', $execTime));
        }catch(Exception $e){
            $message['msg'] = $e->getMessage();
            Alogd::write("LocalEngineListener", "task:{$message['task_id']} failed".$e->getMessage(), $this->debug);
            ReverbClient::sendMessage($tm->package('failed', 0));
            return;
        }
      
        $taskImages = TaskImage::where('task_id', $task->task_id)->get();

        //return while task is compleated
        if($task->work_status == GlobalCode::TASK_SUCCESS){
            $sendMessage = $tm->package('standing', $execTime, $taskImages[0]??null);
            if(isset($message['user_id']) && $task->user_id == $message['user_id']){
                ReverbClient::sendMessage($sendMessage);
            }
            Alogd::write("LocalEngineListener", "Task has been processed, return.user_id=". $sendMessage['user_id']." task_id=".$sendMessage['task_id'], $this->debug);
            return;
        }

        if($message['status'] == 'success'){
            $bigList = [];
            $bigList = $taskImages->pluck('show_url')->toArray();
            $finishedImgNum = count($bigList);
            
            $this->saveImages = $this->getSaveImages($bigList , $message);
            // Alogd::write("LocalEngineListener", "images:".count($bigList).",wait to save:". count($this->saveImages), $this->debug);
            $finishedImgNum += count($this->saveImages);
    
            $this->saveTaskImages($this->saveImages , $task, $tm, $execTime);
            // if(empty($this->saveImages)){
            //     $tm->taskStatus = GlobalCode::TASK_SUCCESS;
            // }
            Helper::setExecTime($task);

            //4.update task status
            if($task->image_num == $finishedImgNum){
                $task->work_status = GlobalCode::TASK_SUCCESS;
                Alogd::write("LocalEngineListener", "All has Done,{$task->task_id} => {$task->work_status}", $this->debug);
                $task->save();
            }else{
                Alogd::write("LocalEngineListener", "progress:{$finishedImgNum} / {$task->image_num}", $this->debug);
            }
        }elseif($message['status'] == 'failed'){
            Alogd::write("LocalEngineListener", "send message status=".$message['status'], $this->debug);
            ReverbClient::sendMessage($tm->package('failed', $execTime));
        }else{
            Alogd::write("LocalEngineListener", "send message status=".$message['status'], $this->debug);
            ReverbClient::sendMessage($tm->package('ephemeral', $execTime));
        }

    }

    protected function getSaveImages($existImages, array $message){
        //{'width':w, 'height':h, 'size':fileSize,'path':value,}
        $saveImages = [];
        if(isset($message['images_big']) && count($message['images_big']) > 0){
            foreach ($message['images_big'] as $key => $value) {
                if(in_array($value['path'], $existImages)){
                    continue;
                }
                $thumb = $message['images_thumb'][$key]??null;
                // var_dump($thumb);
                $saveImages[] = [
                    'width' => $value['width'] ,
                    'height' => $value['height'],
                    'size' => $value['size'],
                    'show_url' => $value['path'],
                    'thumb' => $thumb? $thumb['path'] : '',
                ];
            }
        }else{
            if(!in_array($message['big'], $existImages)){
                $saveImages[] = [
                    'width' => $message['width'] ,
                    'height' => $message['height'],
                    'size' => $message['size'],
                    'show_url' => $message['big'],
                    'thumb' => $message['thumb'],
                ];
            }
        }
        return $saveImages;
    }

    protected function saveTaskImages(array $saveImages, Task $task, TuseMessage $tm, $execTime){
        $lastIndex = count($saveImages)-1;

        $status = $tm->message['status'];
        foreach($saveImages as $key => $val){
            $taskImage = ImageService::saveTaskImage($task, [$val['show_url'],$val['thumb']]);
            
            if($status == 'success' && $lastIndex == $key){
                $message = $tm->package('standing', $execTime , $taskImage);
                $msgCacheContent = json_encode($message);
                $messageKey = Config::get('public.aires_key').$task->task_id;
                Alogd::write("LocalEngineListener", "Done,send and cache msg {$messageKey},size=".strlen($msgCacheContent), $this->debug);
                cache([$messageKey => $msgCacheContent], now()->addMinutes(30));
            }else{
                $message = $tm->package('ephemeral', 3 , $taskImage);
            }
            
            ReverbClient::sendMessage($message);
            Alogd::write("LocalEngineListener", "push to ReverbClient :image id={$taskImage->id}", $this->debug);
        }
    }


}
