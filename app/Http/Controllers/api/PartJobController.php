<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use App\Services\Common;
use App\Support\GlobalCode;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class PartJobController extends Controller
{
    //switch partjob
    public function toggle(Request $request){
        $post = $request->post();
        $rules = [
            'switch' => 'required|string',//on off
            'hardware' => 'required|string'
        ];
        $validator = Validator::make($post , $rules);
        if ($validator->fails()) {
            return ['code' => 400, 'msg' => __('Params incorrect')];
        }

        if($post['switch'] == 'on'){
            //client is online?
            $lastOnlineTime = Cache::get(GlobalCode::CLIENT_HEARTBEAT_KEY);
            if(!$lastOnlineTime || (time() - $lastOnlineTime) > 120 ){
                return ['code' => 1023, 'msg' => __('Ai Image Generator is not running')];
            }
            return $this->open($post);
        }else{
            return $this->close($post);
        }
    }

    public function status(){
        $access_key = Common::getConfigKeyValue('engine.atz.token');
        if(!$access_key){
            return ['code' => 9001, 'msg' => __('Atz Token can not be null')];
        }
        $client = new Client();
        $options = [
            'headers' => [
                'Authorization' => 'Bearer '.$access_key,
                'Content-Type' => 'application/json',
                'Accept-Language' => Session::get('locale','en_US')
            ],
        ];
        $res = $client->Request('GET', env('TUSE_API').'/v1/client/status', $options);
        $content = $res->getBody()->getContents();
        $json = json_decode($content, true);
        if($json){
            return $json;
        }else{
            return ['code' => 4006, 'msg' => __('Api error')];
        }
    }

    protected function open(array $post):array 
    {
        $access_key = Common::getConfigKeyValue('engine.atz.token');
        if(!$access_key){
            return ['code' => 9001, 'msg' => __('Atz Token can not be null')];
        }

        $list = AiModel::where('engine','lc')->where('is_download',1)->where('is_service',1)->get();
        $model_has_ids = $list->pluck("hash");
        //services: ['MK','RBG','SR'] ;
        $job_types = Common::getServiceJobTypes();
        $body = [
            'models' => json_encode($model_has_ids),
            'hardware' => $post['hardware'],
            'job_types' => json_encode($job_types),
        ];
        $options = [
            'headers' => [
                'Authorization' => 'Bearer '. $access_key,
                'Content-Type' => 'application/json',
                'Accept-Language' => Session::get('locale','en_US')
            ],
            'body' => json_encode($body),
            'connect_timeout' => 3,
            'read_timeout' => 10,
            'timeout' => 3,
        ];
      
        $client = new Client();
        $res = $client->Request('POST', env('TUSE_API').'/v1/client/register', $options);
        $content = $res->getBody()->getContents();
        $json = json_decode($content, true);
        if($json && $json['code'] == GlobalCode::SUCCESS) {
            $cacheKey = config('public.partjob_status_key');
            Cache::set($cacheKey, 'online');
            return $json;
        }else{
            $cacheKey = config('public.partjob_status_key');
            Cache::set($cacheKey, 'offline');
            return ['code' => 9001, 'msg' => $json['msg']??__('Can not register a worker')];
        }
    }

    protected function close(array $post):array
    {
        $cacheKey = config('public.partjob_status_key');
        Cache::set($cacheKey, 'offline');
        return ['code' => GlobalCode::SUCCESS];
    }

    public function deposits(Request $request){
        $access_key = Common::getConfigKeyValue('engine.atz.token');
        if(!$access_key){
            return ['code' => 9001, 'msg' => __('Atz Token can not be null')];
        }
        $options = [
            'headers' => [
                'Authorization' => 'Bearer '. $access_key,
                'Content-Type' => 'application/json',
                'Accept-Language' => Session::get('locale','en_US')
            ],
            'connect_timeout' => 3,
            'read_timeout' => 10,
            'timeout' => 3,
            'query' => [
                'filter' => 'rewards',
                'limit' => 6,
            ]
        ];
      
        $client = new Client();
        try{
            $res = $client->Request('GET', env('TUSE_API').'/v1/deposits', $options);
            $content = $res->getBody()->getContents();
            $json = json_decode($content, true);
            return $json;
        }catch(Exception $e){
            return ['code' => 4006, 'msg' => __('Server api error')];
        }
    }
}
