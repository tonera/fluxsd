<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\Common;
use App\Services\ImageService;
use App\Support\Alogd;
use App\Support\GlobalCode;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class ApiJobController extends Controller
{
    public function checkToken(Request $request){
        $rules = [
            'client_id' => 'required|string',
            'access_key' => 'required|string',
            'job_type' => ['required', Rule::in(array_keys(GlobalCode::CHANNEL_LIST))],
        ];
        $ck = $this->ckeckParams($request, $rules);
        if($ck['code'] != GlobalCode::SUCCESS){
            return $ck;
        }
        $c = Common::getConfigKeyValue();
        $ret = [
            'oss' => [
                'id' => $c['storage.oss.access_key']??'', 
                'sec' => $c['storage.oss.secret_key']??'', 
                'end' => $c['storage.oss.endpoint']??'', 
                'buk' => $c['storage.oss.bucket']??'', 
            ],
            's3' => [
                'id' => $c['storage.s3.access_key']??'', 
                'sec' => $c['storage.s3.secret_key']??'', 
                'end' => $c['storage.s3.region']??'', 
                'buk' => $c['storage.s3.bucket']??'', 
            ],
            'redis' => [
                'pwd' => Env::get('REDIS_PASSWORD'),
                'channel' => GlobalCode::getChannel($request->job_type),
                'host' => Env::get('CLIENT_REDIS_HOST'),
                'port' => Env::get('CLIENT_REDIS_PORT'), //REDIS_PORT
                'db' => Config::get('database.redis.default.database'),
                'reschannle' => GlobalCode::CHANNEL_LIST['ATZ-RES'],
            ],
            'server' => [
                'callback' => env('APP_URL')."/api/job/callback",
                'token' => Common::getConfigKeyValue('engine.atz.token'),
            ]
        ];
        //var_dump($ret);
        return [
            'code' => GlobalCode::SUCCESS,
            'data' => urlencode(json_encode($ret))
        ];
    }

    /**
     * ai client post the images
     */
    public function callback(Request $request){
        $startTime = time();
        $rules = [
            'client_id' => 'required|string',
            'access_key' => 'required|string',
            'job_type' => ['required', Rule::in(array_keys(GlobalCode::CHANNEL_LIST))],
            'status' => ['required', Rule::in(['received','failed','success','progress'])],
            'task_id' => 'required|string',
            'user_id' => 'required',
            'id' => 'required',
            // 'thumb' => 'string',
            // 'big' => 'string',
            // 'used_time' => '',
        ];
  
        $data = $request->post();
        $validator = Validator::make($data , $rules);
        if ($validator->fails()) {
            return ['code' => 10010, 'msg' => 'Params check failed'.json_encode($validator->errors()->messages())];
        }

        if($request->client_id != env('AI_CLIENT_ID')){
            return ['code' => 1021, 'msg' => 'Not found this client id'];
        }
       
        $accessKey = md5(env('AI_CLIENT_KEY').$request->task_id.$request->user_id.$request->id);
        if($accessKey != $request->access_key){
            return ['code' => 1021, 'msg' => 'Access key error.'];
        }
        
        $ckStatus = null;
        $resStatus = null;
        switch($request->status){
            case 'received':
                $ckStatus = [GlobalCode::TASK_QUEUED, GlobalCode::TASK_CREATE ];
                $resStatus = GlobalCode::TASK_RUNNING;
            break;
            case 'failed':
                $ckStatus = [GlobalCode::TASK_RUNNING,GlobalCode::TASK_QUEUED, GlobalCode::TASK_CREATE ];
                $resStatus = GlobalCode::TASK_FAILED;
            break;
            case 'success':
                $ckStatus = [GlobalCode::TASK_RUNNING,GlobalCode::TASK_QUEUED, GlobalCode::TASK_CREATE,GlobalCode::TASK_FAILED,GlobalCode::TASK_SUCCESS];
                $resStatus = GlobalCode::TASK_SUCCESS;
            break;
            case 'progress':
                $ckStatus = [GlobalCode::TASK_RUNNING,GlobalCode::TASK_QUEUED, GlobalCode::TASK_CREATE ];
                $resStatus = GlobalCode::TASK_RUNNING;
            break;
            default:
                $ckStatus = [GlobalCode::TASK_RUNNING];
                $resStatus = GlobalCode::TASK_FAILED;
        }
       
        $task = Task::where('task_id', $data['task_id'])->whereIn('work_status', $ckStatus)->first();
        if(!$task){
            return ['code' => 1022, 'msg' => __('The task does not exist or may have been completed')];
        }

        // $path = Storage::putFile('avatars', $request->file('avatar'));
        $files = $request->file('file');
        
        if($files){
            try{
                $image_data = json_decode($data['images_big'], true);
                $thumb_data = json_decode($data['images_thumb'], true);
    
                foreach($files as $key => $file){
                    $big = isset($image_data[$key]) ? $image_data[$key]['path']:"";
                    $thumb = isset($thumb_data[$key]) ? $thumb_data[$key]['path']:"";
                    if($big){
                        $imageBytes = File::get($file);
                        //upload
                        $res = ImageService::storageImages($imageBytes , [$big , $thumb] , $task);
                        if(!$res){
                            Alogd::write(GlobalCode::LC, "Receive image from ai client failed");
                        }
                    }
                }
                return ['code' => GlobalCode::SUCCESS, 'msg' => 'ok'];
            }catch(Exception $e){
                Alogd::write(GlobalCode::LC,$e->getTraceAsString());
                return ['code' => 2002, 'msg' => __('can not upload').$e->getMessage()];
            }
            
        }
    }

    public function ckeckParams(Request $request,  array $rules){
        $ip = $request->getClientIp();
        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails()) {
            //var_dump($validator->errors()->messages());
            return ['code' => 10010, 'msg' => 'Params check failed'.json_encode($validator->errors()->messages())];
        }
        if($request->client_id != env('AI_CLIENT_ID')){
            return ['code' => 1021, 'msg' => 'Not found this client id'];
        }
       
        if(env('AI_CLIENT_KEY') != $request->access_key){
            return ['code' => 1021, 'msg' => 'Access key error.'];
        }

        return ['code' => GlobalCode::SUCCESS, 'msg' => 'ok'];
    } 
}
