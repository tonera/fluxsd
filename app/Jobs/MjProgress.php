<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\TaskImage;
use App\Services\ImageService;
use App\Support\Alogd;
use App\Support\Helper;
use App\Support\GlobalCode;
use App\Support\ReverbClient;
use App\Support\TuseMessage;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;


class MjProgress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $task;

    public $tries = 2;

    public $maxExceptions = 3;

    public $failOnTimeout = true;
    public $timeout = 240;
    public $messageType = 'ephemeral';

    public function __construct(Task $task, string $messageType )
    {
        $this->task = $task;
        $this->messageType = $messageType;
    }

    public function handle()
    {
        $startTime = time();
        Alogd::write(GlobalCode::MJ, "Start MJ-Job task,messageType={$this->messageType}, task_id = {$this->task->task_id}");
        $task = $this->task;
        $sendTaskId = $task->task_id;
        $execTime = Helper::getExecTime($task);
        $exTime = ($execTime > 0 && $execTime < 500) ? $execTime."s" : 'Unknown';
        //if the msg is repeat
        if($task->work_status == GlobalCode::TASK_SUCCESS){
            $imageModel = TaskImage::where('task_id', $task->task_id)->first();
            $tm = new TuseMessage(['status' => 'success']);
            $message = $tm->package('standing', 0, $imageModel);
            Alogd::write("MjProgress", "Task has done,returned:{$task->task_id}");
            ReverbClient::sendMessage($message);
            return;
        }
        if($this->messageType == 'ephemeral'){
            $tm = new TuseMessage(['status' => 'progress']);
            $message = $tm->package($this->messageType, $execTime, null, $task);
            Alogd::write("MjProgress", "Task is in progress,returned:{$task->task_id}");
            $message['task_id'] = $sendTaskId;// for reroll 
            ReverbClient::sendMessage($message);
            return;
        }

        //progress
        $message = json_decode($task->task_pkg);
        if(empty($message->components) || count((array)$message->attachments) == 0){
            $task->work_status = GlobalCode::TASK_FAILED;
            $task->save();
            $tm = new TuseMessage(['status' => 'failed' , 'msg' => __('Generate failed') ]);
            $message = $tm->package('failed', 0, null, $task);
            ReverbClient::sendMessage($message);

            //缓存住
            $messageKey = Config::get('public.aires_key').$task->task_id;
            cache([$messageKey => json_encode($message)], now()->addMinutes(30));

            return;
        }

        $attachments = is_array($message->attachments)?$message->attachments:get_mangled_object_vars($message->attachments);
        $firstKey = array_key_first($attachments);
        $attachment = $attachments[$firstKey];
        $discordUrl = $attachment->proxy_url;
        $objects = Helper::getObjectName('user_res');

        try{
            $client = new Client();
            $imgData = $client->get($discordUrl)->getBody()->getContents();
            //saved to storage
            $image = ImageService::storageImages($imgData , $objects, $task);
        }catch(Exception $e){
            $tm = new TuseMessage(['status' => 'failed' , 'msg' => __('Failed to download the image') ]);
            $message = $tm->package('failed', 0, null, $task);
            ReverbClient::sendMessage($message);
            $this->fail(new Exception(__('Failed to download the image. stoped.')));
            return;
        }

        //save to db
        $data = [
            'task_id' => $task->task_id,
            'third_url' => $discordUrl,
            'show_url' => $objects[0],
            'thumb' => $objects[1],//小图片
            'is_merge' => 0,
            'user_id' => $task->user_id,
            'show_status' => 10,            
        ];
        //create task_image
        $imageModel = TaskImage::create($data);

        //task update
        $task->image_num = 1;
        $task->cover_url = $objects[0];
        $task->work_status = GlobalCode::TASK_SUCCESS;
        $task->save();

        //send
        $tm = new TuseMessage(['status' => 'success']);
        $message = $tm->package('standing', 0, $imageModel);
        Alogd::write("MjProgress", "Task has done,returned:{$task->task_id}");
        ReverbClient::sendMessage($message);

        //cached
        // $msgCacheContent = json_encode(['type'=> 'response','body' => $message]);
        // Alogd::write(GlobalCode::MJ, '缓存消息 {$messageKey},包大小:'.strlen($msgCacheContent));
        // $messageKey = Config::get('public.aires_key').$task->task_id;
        // cache([$messageKey => $msgCacheContent], now()->addMinutes(30));
        
    }
}
