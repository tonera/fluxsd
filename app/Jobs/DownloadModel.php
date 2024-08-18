<?php

namespace App\Jobs;

use App\Models\AiModel;
use App\Models\DownloadLog;
use App\Services\Common;
use App\Support\Helper;
use App\Support\ResumeDownload;
use App\Support\UnitStorage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Throwable;

class DownloadModel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $downloadLog;
    //retry 10 times in 24h
    public $timeout = 3600*24;//need pcntl or --timeout=30 
    public $tries = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(DownloadLog $downloadLog)
    {
        $this->downloadLog = $downloadLog;
    }

    public function middleware(): array
    {
        // return [new WithoutOverlapping($this->downloadLog->model_hash_id)];
        return [(new WithoutOverlapping($this->downloadLog->model_hash_id))->releaseAfter(120)];
    }

    // public function retryUntil(): DateTime
    // {
    //     return now()->addHours(24);
    // }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        $model = AiModel::where('hash', $this->downloadLog->model_hash_id)->first();
        $file = Common::getModelPath($model->type) .DIRECTORY_SEPARATOR.$model->sd_name;
        $cacheKey = Common::getCacheKey('model_hash_id', $this->downloadLog->model_hash_id);
        Cache::put($cacheKey, 0, 15);

        // echo "download url：". $this->downloadLog->download_url. " file {$file } cache key={$cacheKey}\n";

        try{
            // $res = ResumeDownload::download($file , $this->downloadLog->download_url,$cacheKey);
            $res = true;
            if($res === true){
                AiModel::where('hash', $model->hash)->update(['is_download'=> 1]);
                DownloadLog::where('model_hash_id', $model->hash)->delete();
                Cache::forget($cacheKey);
                echo "下载完成，请除download log, cache 置model.is_download=1\n";
            }

            //upload thumb / save to local
            $config = [
                'storage' => 'local',
            ];
            $storage = new UnitStorage($config);
            $objects = Helper::getObjectName('model', 'jpeg', 'images');
            $storage->putObject($objects[0], file_get_contents($model->thumb));
            $model->thumb = $objects[0];
            $model->save();
        }catch(Exception $e){
            DownloadLog::where('model_hash_id', $model->hash)->delete();
                
            Cache::put($cacheKey, __("Download failed").':'.mb_substr($e->getMessage(), 0 ,100), 15);
            echo "Error:".$e->getMessage()."\n";
            $this->fail($e);
        }
    }

    public function failed(Throwable $exception): void
    {
        DownloadLog::where('model_hash_id', $this->downloadLog->model_hash_id)->delete();
    }
}
