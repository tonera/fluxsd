<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\TaskImage;
use App\Services\ImageService;
use App\Services\StabilityService;
use App\Support\Alogd;
use App\Support\GlobalCode;
use App\Support\Helper;
use App\Support\ReverbClient;
use App\Support\UnitRequest;
use App\Support\TuseMessage;
use App\Support\UnitImage;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SdProgress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $task;

    public $tries = 2;

    public $maxExceptions = 3;

    public $failOnTimeout = true;

    public $timeout = 240;
    protected $hasMessage = true;

    /**
     * Create a new job instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $startTime = time();
        Alogd::write(GlobalCode::SD, '开始执行SD-Job任务');
        if(!$this->task){
            Alogd::write(GlobalCode::SD, "Task is not found.");
            $this->fail();
        } 
        $task = $this->task;
        $exParams = json_decode($task->task_pkg, true);
        $execTime = Helper::getExecTime($task);
        $exTime = ($execTime > 0 && $execTime < 500) ? $execTime."秒" : '未知';

        if($task->work_status == GlobalCode::TASK_SUCCESS){
            $taskImage = TaskImage::where('task_id', $task->task_id)->first();
            $tm = new TuseMessage(['status' => 'success']);
            $message = $tm->package('standing', 0, $taskImage);
            Alogd::write(GlobalCode::SD, "任务已完成状态,直接返回消息:{$task->task_id}");
            ReverbClient::sendMessage($message);
            return;
        }

        try{
            $data = $task->toArray();
            
            $req = new UnitRequest($data);
            $req->initPost();
   
            $extData = [
                'style_preset' => $req->lora_name,
                'image_strength' => $req->denoising_strength,
            ];
            $input = $req->package($extData);
        
            
            if($req->generate_type == 'txt2img'){
                Alogd::write(GlobalCode::SD, '文生图:开始调用接口');
                if(isset($exParams['lora_scale'])){
                    $input['lora_scale'] = $exParams['lora_scale'];
                }
                $formatRes = StabilityService::txt2img('', $input);
                
            }else{
                if($req->initImgBtyes){
                    $content = $req->initImgBtyes;
                }else{
                    $client = new Client();
                    
                    try{
                        $content = $client->get($input['access_url'].$input['init_img_path'])->getBody()->getContents();
                    }catch(Exception $e){
                        Alogd::write(GlobalCode::SD, 'Om-AI创作图生图失败，原图片无法获取到');
                        $tm = new TuseMessage(['status' => 'failed' , 'msg' => $e->getMessage()]);
                        $message = $tm->package('failed', 0, null, $task);
                        ReverbClient::sendMessage($message);
                        $this->fail($e->getMessage());
                        return;
                    }
                }
                
                $img = new UnitImage();
                $img->loadImage($content);
                $sdContent = $img->resizeSd(config('aspects.sdratio'),'jpeg',80);
                $input['init_image'] = $sdContent ;
                $formatRes = StabilityService::img2img('', $input);
            }
            if($formatRes['code'] === GlobalCode::SUCCESS){
                Alogd::write(GlobalCode::SD, 'SD-AI创作接口返回成功,图片大小'.strlen($formatRes['data']['base64']));
                $imageBytes = base64_decode($formatRes['data']['base64']);
                $fileType = $task->act == 'RBG'?'png':'jpeg';
                $objects = Helper::getObjectName('user_res', $fileType,'storage');

                ImageService::storageImages($imageBytes, $objects, $task);

                $taskImage = ImageService::saveTaskImage($task, $objects);
                
                $execTime = Helper::getExecTime($task);
                $tm = new TuseMessage(['status' => 'success']);
                $message = $tm->package('standing', $execTime, $taskImage);
                ReverbClient::sendMessage($message);

                $task->work_status = GlobalCode::TASK_SUCCESS;
                $task->cover_url = $taskImage->show_url;
                $task->save();
                
                Helper::setExecTime($task);
            }else{
                Alogd::write(GlobalCode::SD, '调用生成SD-AI绘画处理返回错误:code='.$formatRes['code'].' msg='.$formatRes['msg']);
                $tm = new TuseMessage(['status' => 'failed' , 'msg' => $formatRes['msg']]);
                $message = $tm->package('failed', 0, null, $task);
                ReverbClient::sendMessage($message);
            }
        }catch(Exception $e){
            //send failed
            Alogd::write(GlobalCode::SD, 'SD绘画失败:msg ='.$e->getMessage());
            $tm = new TuseMessage(['status' => 'failed' , 'msg' => $e->getMessage()]);
            $message = $tm->package('failed', 0, null, $task);
            ReverbClient::sendMessage($message);
            $this->fail($e->getMessage());
        }
    }
}