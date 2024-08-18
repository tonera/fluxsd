<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\AiConfig;
use App\Support\GlobalCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ConfigApiController extends Controller
{
    protected $cacheKey = "aibox_tbl_config";

    public function update(Request $request)
    {
        if(!$request->user()->is_admin){
            return ['code' => 408, 'msg' => __('Only administrators can manage configuration')];
        }
        $rules = [
            'pkey' =>'required|string',
            'skey' => 'required|string',
            'config'=> 'required',
        ];
        $validator = Validator::make($request->post(), $rules);
        if ($validator->fails()) {
            return ['code' => 400, 'msg' => __('参数错误')];
        }
        $config = json_decode($request->config,true);
        foreach($config as $key => $value){
            $editKey = $request->pkey.'.'. $request->skey;
            if($editKey.'.label' == $key || $editKey.'.apply_url' == $key){
                continue;
            }
            if(stristr($key, $editKey) !== false){

                if('storage.'.$request->skey.'.access_url' == $key && strlen($value) > 1 && substr($value, -1) != '/'){
                    $value = $value.'/';
                }
                AiConfig::updateOrCreate(
                    ['c_key'=> $key],
                    ['c_value' => $value]
                );
            }
        }
        Cache::forget($this->cacheKey);
        return ['code' => GlobalCode::SUCCESS, 'data' => ''];
    }

    public function active(Request $request){
        //todo : token can use
        $rules = [
            'c_key' =>'required|string',
            'c_value' => 'required',
        ];
        $validator = Validator::make($request->post(), $rules);
        if ($validator->fails()) {
            return ['code' => 400, 'msg' => __('参数错误')];
        }

        //onle one storage engine works
        $editKeys = explode( '.', $request->c_key);
        if('storage' == $editKeys[0]){
            $configMeta = config('config_meta');
            foreach($configMeta['storage'] as $key => $val){
                $updateKey = 'storage.'.$key.'.is_active';
                if($updateKey != $request->c_key){
                    AiConfig::where('c_key', $updateKey)
                        ->update(['c_value' => 0]);
                }
            }
        }
        $config = AiConfig::updateOrCreate(
            ['c_key'=> $request->c_key],
            ['c_value' => $request->c_value]
        );
        Cache::forget($this->cacheKey);
        return ['code' => GlobalCode::SUCCESS, 'data' => $config];
    }

}
