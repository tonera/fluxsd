<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\TaskImage;
use App\Services\Common;
use App\Services\ImageService;
use App\Support\Alogd;
use App\Support\GlobalCode;
use App\Support\Helper;
use App\Support\ReverbClient;
use App\Support\TuseMessage;
use App\Support\UnitStorage;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SdVideoProgress implements ShouldQueue
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
        Alogd::write("SdVideoProgress", 'Start SDVD-Job');
        if(!$this->task){
            Alogd::write("SdVideoProgress", "Task is not found.");
            $this->fail();
        } 
        $task = $this->task;
        $exParams = json_decode($task->task_pkg, true);
        $request_id = $exParams['request_id']??null;
        if(!$request_id){
            $tm = new TuseMessage(['status' => 'failed' , 'msg' => __('Task has been failed') ]);
            $message = $tm->package('failed', 0, null, $task);
            ReverbClient::sendMessage($message);
            $this->fail();
            return;
        }
        if(strlen($request_id) < 60){
            Alogd::write("SdVideoProgress", "Failed: Request_id is less than 64.");
            $tm = new TuseMessage(['status' => 'failed' , 'msg' => __('Generate video failed') ]);
            $message = $tm->package('failed', 0, null, $task);
            ReverbClient::sendMessage($message);
            $this->fail();
            return;
        }

        $execTime = Helper::getExecTime($task);
        $exTime = ($execTime > 0 && $execTime < 500) ? $execTime."s" : 'UnKnown';
        //repeat message
        if($task->work_status == GlobalCode::TASK_SUCCESS){
            $taskImage = TaskImage::where('task_id', $task->task_id)->first();
            $tm = new TuseMessage(['status' => 'success']);
            $message = $tm->package('standing', 0, $taskImage);
            Alogd::write("SdVideoProgress", "Task has done,returned:{$task->task_id}");
            ReverbClient::sendMessage($message);
            return;
        }

        $objects = Helper::getObjectName('user_res', 'mp4');
        $tempFile = tempnam(sys_get_temp_dir(), 'fluxsdvd_');
        $fp = fopen($tempFile, 'w+');

        //start get video
        try{
            $downloadUrl = "https://api.stability.ai/v2beta/image-to-video/result/{$request_id}";
            Alogd::write("SdVideoProgress", "Download from{$downloadUrl}");
            $client = new Client();
            $response = $client->get($downloadUrl, [
                'headers' => [
                    'accept' => "video/*",
                    'authorization' => "Bearer ".Common::getConfigKeyValue('engine.sd.token'),
                ],
                'connect_timeout' => 3.14,
                'timeout' => 0,
                // 'debug' => true,
            ]);

            $packageSize = 81920;
            while(!$response->getBody()->eof()){
                $result = $response->getBody()->read($packageSize);
                //{"id":"a993963a739fc3f207a7201c9d7762921d82a277c18f1701a7ece2c6a8fd491f","status":"in-progress"}
                if(strlen($result) < 4000){
                    $json = json_decode($result);
                    if(isset($json->status) && $json->status == 'in-progress'){
                        $this->release(10);
                        Alogd::write("SdVideoProgress", 'Vedio is generating, status = in-progress');
                        return;
                    }
                }
                if (fwrite($fp, $result, strlen($result)) === FALSE) {
                    Alogd::write("SdVideoProgress", 'Cannot write to file');
                    $this->fail("Cannot write to file");
                    return;
                }
            }
            //save to disk
            $activeConfig = Common::getOnlyActiveConfig('storage');
            $config = [
                'storage' => $activeConfig['indexKey'],
                'access_key' => $activeConfig['access_key']??'', 
                'access_secret' => $activeConfig['secret_key']??'', 
                'bucket' => $activeConfig['bucket']??'', 
                'endpoint' => $activeConfig['endpoint']??'', 
                'region' => $activeConfig['region']??'', 
            ];
            $storage = new UnitStorage($config);
            $storage->putFile($objects[0], $tempFile);
            //for video, thumb is the same big
            $objects[1] = $objects[0];

            $taskImage = ImageService::saveTaskImage($task, $objects);
            $execTime = Helper::getExecTime($task);
            $tm = new TuseMessage(['status' => 'success']);
            $message = $tm->package('standing', $execTime, $taskImage);
            Alogd::write("SdVideoProgress", 'success, file='.$objects[0]);
            ReverbClient::sendMessage($message);

            $task->work_status = GlobalCode::TASK_SUCCESS;
            $task->cover_url = $taskImage->show_url;
            $task->save();
            
            Helper::setExecTime($task);
        }catch(Exception $e){
            $this->fail($e->getMessage());
        }
    }
}
