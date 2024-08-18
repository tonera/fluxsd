<?php
namespace App\Services;

use App\Jobs\SdProgress;
use App\Jobs\StabProgress;
use App\Models\TaskImage;
use App\Support\Alogd;
use App\Support\IDGenerator;
use App\Support\InterventionImage;
use App\Support\GlobalCode;
use App\Support\UnitImage;
use App\Support\UnitRequest;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class SdTask{
    public static function create(UnitRequest $req):array{
        $req->initPost();
        $input = $req->package();
        Alogd::write(GlobalCode::SD, "SD绘画开始,创建任务:{$req->task_id}");
        $input['task_pkg'] = json_encode($input);
        // var_dump($input);
        // exit;
        $task = TaskService::createTask($input);       
        SdProgress::dispatch($task)->delay(Carbon::now()->addSeconds(1));
        return ['code' => GlobalCode::SUCCESS, 'data' => $task];
    }

    //sd video image only support 1024x576  768x768 576x1024
    public static function formatImage($imageBytes){
        $img = new UnitImage();
        $img->loadImage($imageBytes);
        $list = [[1024,576] , [768,768] , [576,1024]];
        foreach($list as $key => $val){
            $dimensions[] = [
                'width' => $val[0],
                'height' => $val[1],
            ];
        }
        $newImgBytes = $img->resizeSd($dimensions ,'jpeg',99);
        return $newImgBytes;
    }

}
?>