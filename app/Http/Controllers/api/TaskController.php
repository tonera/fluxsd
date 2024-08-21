<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\TaskImage;
use App\Services\AtzTask;
use App\Services\Common;
use App\Services\LocalTask;
use App\Services\MjTask;
use App\Services\SdTask;
use App\Services\TranslateService;
use App\Support\Alogd;
use App\Support\GlobalCode;
use App\Support\Helper;
use App\Support\IDGenerator;
use App\Support\UnitRequest;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function store(Request $request){
        $input = $request->all();
        $activeConfig = Common::getOnlyActiveConfig('storage');
        $input['storage'] = $activeConfig['indexKey']??'local';
        Alogd::write("TaskController", "Create a new task, params:");
        // Alogd::write("TaskController", $input);

        try{
            $req = new UnitRequest($input);
        }catch(Exception $e){
            return ['code' => 10010, 'msg' => __('Params incorrect').':'.$e->getMessage()]; 
        }
        
        if($req->engine != GlobalCode::LC){
            $access_key = Common::getConfigKeyValue('engine.'.$req->engine.'.token');
            if(!$access_key){
                return ['code' => 600, 'msg' => __('To use this service, please configure the access key first')]; 
            }
        }else{
            $path = Common::getConfigKeyValue('engine.lc.client_path');
            if(!$path){
                return ['code' => 600, 'msg' => __('No local AI generator is configured')]; 
            }
        }

        //translate
        if(isset($req->prompt_en) && $req->prompt_en && $req->prompt_en != 'undefined'){
            Alogd::write("TaskController", 'prompt_en has exists:'.$req->prompt_en);
            //delete -- if mj
            $arr = explode("--", $req->prompt_en);
            $promptEn = $arr[0];
            $negativePromptEn = $promptEn??'';
        }else{
            if(Helper::isEnglish($req->prompt) || Helper::isEnglish($req->negative_prompt)){
                $promptEn = $req->prompt;
                $negativePromptEn = $req->negative_prompt??'';
                Alogd::write("TaskController", 'Prompt is english:'.$promptEn);
            }else{
                $appConfig = Common::getConfigKeyValue();
                $transPrompts = TranslateService::translate($appConfig , [$req->prompt??'', $req->negative_prompt??'']);
                $promptEn = $transPrompts[0];
                $negativePromptEn = $transPrompts[1]??'';
                Alogd::write("TaskController", 'Prompt is chinese, translate result:'.$promptEn);
            }
        }
        $req->prompt_en = substr($promptEn,0,800);
        $req->negative_prompt_en = substr($negativePromptEn,0,800);
        $req->task_id = IDGenerator::id();
        $req->user_id = Auth::id();

        try{
            $taskModel = [];
            if($req->engine == GlobalCode::MJ){
                if($req->act == 'MK'){
                    $taskModel = MjTask::create($req);
                }else{
                    $taskImage = TaskImage::find($input['id']);
                    $input['user_id'] = Auth::id();
                    $taskModel = MjTask::upscale($req->act, $input ,$taskImage);
                }
            }elseif($req->engine == GlobalCode::SD){
                $taskModel = SdTask::create($req);
            }elseif($req->engine == GlobalCode::ATZ){
                $taskModel = AtzTask::create($req);
            }else{
                $taskModel = LocalTask::create($req);
            }
            if(isset($taskModel['code']) && $taskModel['code'] === GlobalCode::SUCCESS){
                $task = $taskModel['data'];
                $execTime = Helper::getExecTime($task);
                // Alogd::write("TaskController", "execTime = {$execTime} s");
                return (new TaskResource($task))
                    ->additional([
                        'code' => GlobalCode::SUCCESS,
                        // 'wsHost' => 'ws://'. Common::getHostIp(),':'.env('WS_PORT') ,
                        'execTime' => $execTime,
                    ]);
            }else{
                return ['code' => $taskModel['code'] , 'msg'=> $taskModel['msg']??'Generating error'];
            }
 
        }catch(Exception $e){
            // var_dump($e->getLine(), $e->getFile());
            return ['code' => 400, 'msg' => $e->getMessage()];
        }catch(Error $e){
            // var_dump($e->getLine(), $e->getFile());
            return ['code' => 400, 'msg' => $e->getMessage()];
        }
    }

    public function upload(Request $request){
        $objects = Helper::getObjectName('user', 'jpeg');
        if($request->file('init_image')){
            // $path = Storage::putFile('init_image', $request->file('init_image'));
            // $path = Storage::putFileAs('photos',  $request->file('init_image'), 'photo.jpg');
            $path = Storage::putFile('public', $request->file('init_image'));
            $url = env('APP_URL').Storage::url($path);
            return ['code' => GlobalCode::SUCCESS,'data'=> ['path'=>$path, 'url' => $url]];
        }   
    }

    
    
}


