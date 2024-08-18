<?php
namespace App\Services;

use App\Models\Task;
use App\Models\TaskImage;
use App\Support\GlobalCode;
use App\Support\Helper;
use App\Support\UnitImage;
use App\Support\UnitStorage;
use Error;
use GuzzleHttp\Client;

class ImageService{
    public static function storageImages($imageBytes, array $objects, Task $task, $third_url = null)
    {
        $fileType = 'jpeg';
        $is_merge = 0;
        if($task->engine == 'mj' && $task->act == 'create'){
            $is_merge = 1;
        }
        if($task && $task->act == 'RBG'){
            $fileType = 'png';
        }

        $activeConfig = Common::getOnlyActiveConfig('storage');
        if($activeConfig == false){
            $activeConfig = [];
            $activeConfig['indexKey'] = 'local';
        }
        $config = [
            'storage' => $activeConfig['indexKey'],
            'access_key' => $activeConfig['access_key']??'', 
            'access_secret' => $activeConfig['secret_key']??'', 
            'bucket' => $activeConfig['bucket']??'', 
            'endpoint' => $activeConfig['endpoint']??'', 
            'region' => $activeConfig['region']??'', 
        ];
        $storage = new UnitStorage($config);
        $storage->putObject($objects[0], $imageBytes);
        //thumb
        $img = new UnitImage();
        $img->loadImage($imageBytes);
        $thumbBytes = $img->scale($fileType, 80, 800,800);
        $storage->putObject($objects[1], $thumbBytes);
        //save to db
        $access_url = $activeConfig['access_url']??'';
        if($activeConfig['indexKey'] == 'local' && !$access_url){
            $access_url = '/';
        }
        $task->width = $img->meta['width'];
        $task->height = $img->meta['height'];
        $task->save();
        // $taskImage = self::saveTaskImage($task, [$access_url.$objects[0], $access_url.$objects[1]], $is_merge);
        return [
            'width' => $img->meta['width'], 
            'height' => $img->meta['height'], 
            'size' => strlen($imageBytes), 
            'url' => $access_url.$objects[1],
            'uri' => $objects[0],
        ];
    }

    public static function saveTaskImage(Task $task, array $objects, $is_merge = 0, $third_url = null){
        $data = [
            'task_id' => $task->task_id,
            'third_url' => $third_url,
            'show_url' => $objects[0],
            'thumb' => $objects[1],
            'show_status' => 10,
            'user_id' => $task->user_id,
            'is_merge' => $is_merge,
        ];
        return TaskImage::firstOrCreate([
            'task_id' => $task->task_id,
            'show_url' => $objects[0],
        ],$data);
    }

    public static function makeImageUrl($imageObject, $storage = null){
        $activeConfig = Common::getOnlyActiveConfig('storage');
        $config = [
            'storage' => $activeConfig['indexKey'],
            'access_key' => $activeConfig['access_key']??'', 
            'access_secret' => $activeConfig['secret_key']??'', 
            'bucket' => $activeConfig['bucket']??'', 
            'endpoint' => $activeConfig['endpoint']??'', 
            'region' => $activeConfig['region']??'', 
        ];
        
        $imageBytes = null;
        if(is_string($imageObject )){
            if(preg_match('/^(http)/i', $imageObject )){    
                $client = new Client();
                $imageBytes = $client->get($imageObject )->getBody()->getContents();
                $extension = Helper::getFileExtension($imageObject, 'url');
            }else{
                //base64
                $imageBytes = base64_decode(Helper::getClearBase64Image($imageObject));
                $extension = Helper::getFileExtension($imageObject, 'base64');
            }
        }else{
            if( $imageObject !== null){
                try{
                    $imageBytes = $imageObject->getContent();
                    $extension = $imageObject->extension();
                }catch(Error $e){
                    return ['code' => 10010, 'msg' => 'Image Exception, check the size of image','debug' => $e->getMessage()];
                }
            }else{
                return ['code' => 10010, 'msg' => 'Image Exception, check the size of image'];
            }
        }
        $storage = new UnitStorage($config);
        $objects = Helper::getObjectName('user', $extension, 'storage');
        $img = new UnitImage();
        $img->loadImage($imageBytes);
        if($img->meta['width'] > 1024 || $img->meta['height'] > 1024){
            $imageBytes = $img->scale();
        }
        $size = strlen($imageBytes);
        $storage->putObject($objects[0], $imageBytes);

        $access_url = $activeConfig['access_url']??'';
        if($activeConfig['indexKey'] == 'local' && !$access_url){
            $access_url = '/';
        }
        return [
            'code' => GlobalCode::SUCCESS, 
            'data' => [
                'width' => $img->image->width(), 
                'height' => $img->image->height(), 
                'size' => $size, 
                'url' => $access_url.$objects[0],
                'uri' => $objects[0],
                'ext' => strtolower($extension),
                'content' => base64_encode($imageBytes)
            ]
        ];
    }
}


