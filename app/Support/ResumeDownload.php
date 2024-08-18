<?php

namespace App\Support;

use App\Models\AiModel;
use App\Models\DownloadLog;
use App\Services\Common;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class ResumeDownload{ 

    //流式下载文件，如果设置了cachekey 会将下载进度缓存在此key
    public static function download($file, $requestUrl, $cacheKey = null){
        if(!file_exists($file)){
            $fp = @fopen($file, "a+");
            if($fp === false){
                throw new Exception("Model file is not writable");
            }
        }else{
            if (!is_writable($file)) {
                throw new Exception("File is not writable");
            }else{
                $fp = fopen($file, "a+");
            }
        }
        $fileNames = explode('.', $file);
        $ext = array_pop($fileNames);
        // var_dump(array_pop($fileNames));
        // exit;
        //断点续传，计算从哪里开始
        $fileSize = filesize($file);
        echo "文件{$file}已下载:{$fileSize}\n";
        // exit;
        $client = new Client();
        try{
            $response = $client->get($requestUrl, [
                'headers' => [
                    '_token_' => '',
                    'RANGE' => "bytes=".$fileSize."-"
                ],
                'stream' => true,
                'connect_timeout' => 3.14,
                'timeout' => 0,
                'read_timeout' => 10,
                // 'debug' => true,
            ]);
        }catch(Exception $e){
            if($e->getCode() == 416){
                //已下载完成
                echo "已下载完成\n";
                return true;
            }else{
                throw new Exception($e->getMessage());
            }
        }
        
        $contentRange = explode('/', $response->getHeader('Content-Range')[0]);
        $totalSize = $contentRange[1];
        // var_dump($contentRange);
        echo "Start from"."bytes=". Common::formatBytes($fileSize)." Total=".Common::formatBytes($totalSize)." size={$totalSize}\n";
        // var_dump($response->getHeaders(),$totalSize,$fileSize);
        // exit;

        $size = $fileSize;
        $packageSize = 8192;
        $percent = 0;
        while(!$response->getBody()->eof()){
            $result = $response->getBody()->read($packageSize);
            $size += $packageSize;
            if (fwrite($fp, $result, strlen($result)) === FALSE) {
                throw new Exception("Cannot write to file ($file) size = {$size}");
            }
            $percent = number_format($size / $totalSize*100,2);
            $percent = $percent > 100 ? 100 :$percent;
            if($cacheKey){
                Cache::put($cacheKey, $percent,7*24*3600);//缓存进度7天 
            }
            if(($size % ($packageSize*100)) == 0){
                echo "Done:".Common::formatBytes($size)." {$percent}% size={$size}\n";
                fflush($fp);
            }
            
            unset($result);
            // exit;
        }

        fclose($fp);
        if(intval($percent) >= 100){
            return true;
        }else{
            return false;
        }
    }

    
}