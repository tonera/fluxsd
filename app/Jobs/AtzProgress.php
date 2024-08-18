<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\Common;
use App\Support\Alogd;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AtzProgress implements ShouldQueue
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

    public function handle(): void
    {
        $access_url = Common::getAccessUrl($this->task->storage);
        $api = '';
        switch($this->task->act){
            case 'MK':
                $api = '/v1/generate';
                $body = json_encode([
                    'prompt' => $this->task->prompt_en,
                    'negative_prompt' => $this->task->negative_prompt_en??'',
                    'denoising_strength' => $this->task->denoising_strength,
                    'cfg_scale' => $this->task->cfg_scale,
                    'seed' => $this->task->seed,
                    'steps' => $this->task->steps,
                    'width' => $this->task->width,
                    'height' => $this->task->height,
                    'init_img_path' => $this->task->init_img_path ? $access_url.$this->task->init_img_path : '', 
                    'model_hash_id' => $this->task->model_hash_id,
                    'lora_hash_id' => $this->task->lora_hash_id,
                    'negative_prompt' => $this->task->negative_prompt,
                    'image_num' => $this->task->image_num,
                    'reference_id' => $this->task->task_id,
                ], JSON_PRETTY_PRINT);
            break;
            case 'RBG':
                $api = '/v1/removebg';
                $body = json_encode([
                    'init_img_path' => base64_encode(file_get_contents($access_url.$this->task->init_img_path)),
                    // 'init_img_path' => $access_url.$this->task->init_img_path,
                    'reference_id' => $this->task->task_id,
                ], JSON_PRETTY_PRINT);
                break;
            case 'SR':
                $task_pkg = json_decode($this->task->task_pkg, true);
                $api = '/v1/upscale';
                $body = json_encode([
                    'init_img_path' => base64_encode(file_get_contents($access_url.$this->task->init_img_path)),
                    // 'init_img_path' => $access_url.$this->task->init_img_path,
                    'reference_id' => $this->task->task_id,
                    'upscale' => $task_pkg['upscale']??2,
                ], JSON_PRETTY_PRINT);
                break;
            case 'FS':
                $task_pkg = json_decode($this->task->task_pkg, true);
                $api = '/v1/faceswap';
                $body = json_encode([
                    'init_img_path' => base64_encode(file_get_contents($access_url.$this->task->init_img_path)),
                    // 'init_img_path' => $access_url.$this->task->init_img_path,
                    'image_file2' => base64_encode(file_get_contents($access_url.$this->task->image_file2)),
                    'face_enhance' => $task_pkg['face_enhance']??'yes',
                    'reference_id' => $this->task->task_id,
                ], JSON_PRETTY_PRINT);
                Alogd::write('AtzProgress:init url=', $access_url.$this->task->init_img_path);
                break;
            default:
                $this->fail(__('Wrong action'));
                return ;
        }
        

        $appConfig = Common::getConfigKeyValue();
        //获取atz引擎key
        if(!isset($appConfig['engine.atz.is_active']) || $appConfig['engine.atz.is_active'] != 1){
            $this->fail(__('Wrong engine'));
            return ;
        }

        $token = $appConfig['engine.atz.token']??'';
        $client = new Client();
        $options = [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Content-Type' => 'application/json'
            ],
            'body' => $body,
        ];

        try{
            Alogd::write("AtzProgress", "Post to ".env('TUSE_API').$api);
            $res = $client->Request('POST',  env('TUSE_API').$api, $options);
            $content = $res->getBody()->getContents();
            Alogd::write("AtzProgress", "response=".$content);
            print_r($content);
        }catch(Exception $e){
            // var_dump($e->getMessage(), $e->getCode());
            echo "res" . $e->getResponse()->getBody()->getContents() . "\n";
         
        }

   

        
    }
}
