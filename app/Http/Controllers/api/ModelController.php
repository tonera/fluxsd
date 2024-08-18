<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AiModelCollection;
use App\Jobs\DownloadModel;
use App\Models\AiModel;
use App\Models\AiModelImage;
use App\Models\DownloadLog;
use App\Services\Common;
use App\Support\GlobalCode;
use App\Support\Helper;
use App\Support\TableCache;
use App\Support\UnitStorage;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ModelController extends Controller
{
    /**
     * Display a list of the resource.
     */
    public function index(Request $request)
    {
        $types = $request->input("types")??'';
        $engine = $request->input("engine");
        if($engine  == 'atz'){
            //$types, $request->input("options")??'', $request->input("page")??1, $request->input("keyword")
            return Common::getTuseModels($request->input());
        }else{
            $jsonTypes = json_decode($types, true);
            $options = json_decode($request->input("options"), true);
            $query = AiModel::whereNull('deleted_at');
            if($jsonTypes && count($jsonTypes) > 0){
                $query = $query->whereIn('type', $jsonTypes??[]);
            }
            
            //filter "["SD 1.5","Other","Pony","SD 1.4","SDXL Turbo","SD 2.1 768","NSFW"]"
            if($options != null && is_array($options)){
                $query = $query->whereIn('base_model',$options);
                if(in_array('NSFW', $options)){
                    $query = $query->where('nsfw', 1);
                }else{
                    $query = $query->where('nsfw', 0);
                }
            }else{
                // $query = $query->where('sd_name','');
            }
            if($request->input("keyword")){
                $query = $query->where('name' ,'like','%'.$request->input("keyword").'%')->orwhere('sd_name' ,'like','%'.$request->input("keyword").'%');
            }
    
            if($engine == 'sd'){
                $query->where('engine', 'sd');
            }elseif($engine == 'lc'){
                $query->where('engine', 'lc');
            }else{
                return[];
            }
            $list = $query->orderby('favored', 'desc')
                        ->orderby('updated_at', 'desc')
                        ->paginate(40, ['*'], 'page', $request->page);
            $baseModels = TableCache::getGroupField('ai_models', 'base_model', 86400, "deleted_at is null");
            $types = TableCache::getGroupField('ai_models', 'type', 86400, "deleted_at is null");
            // var_dump($list->data);
 
            return (new AiModelCollection($list))->additional(['code' => GlobalCode::SUCCESS,  'base_models' => array_keys($baseModels), 'types' => array_keys($types)]);
        }        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AiModel $model)
    {
        if(isset($request->favored)){
            $favored = $request->favored == 1 ? 1 : 0 ;
            $model->favored = $favored;
            $model->save();
        }
        return ['code' => GlobalCode::SUCCESS, 'data' => $model];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AiModel $amModel)
    {
        if(!Auth::user()->is_admin){
            return ['code' => 408, 'msg' => __('Only administrators can delete models')];
        }
        //delete the model
        AiModelImage::where('model_id', $amModel->model_id)->delete();
        $amModel->delete();
        return ['code' => GlobalCode::SUCCESS, 'data' => $amModel];
    }

    public function download(Request $request){
        if(!$request->user()->is_admin){
            return ['code' => 408, 'msg' => __('Only administrators can download models')];
        }
        $post = $request->post();
        $rules = [
            'hash' =>'required|string',
            'name'=> 'required',
            'type'=> 'required',
            'nsfw'=> 'required',
            'base_model'=> 'required',
            'sd_name'=> 'required',
            'thumb'=> 'required',
            'base_size'=> 'required',
            'model_id'=> 'required',
        ];
        $validator = Validator::make($post, $rules);
        if ($validator->fails()) {
            return ['code' => 400, 'msg' => __('Params incorrect').json_encode($validator->errors()->messages(), JSON_UNESCAPED_UNICODE) ];
        }

        $model = AiModel::where('hash', $post['hash'])->first();
        if($model){
            $post['download_url'] = $model->download_url;
        }else{
            //get download url /api/models/download/1TT3Z1ZR4YRK
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
            $res = $client->Request('GET', env('TUSE_API').'/v1/models/download/'.$post['hash'], $options);
            $content = $res->getBody()->getContents();
            $json = json_decode($content, true);
            if($json && $json['code'] == GlobalCode::SUCCESS){
                $post['download_url'] = $json['data']['download_url'];
            }else{
                return ['code' => 4006, 'msg' => __('Api error')];
            }
        }

        try{
            $data = [
                'user_id' => Auth::id(),
                'model_hash_id' => $post['hash'],
                'download_url' => $post['download_url'],
            ];
            $downloadLog = DownloadLog::updateOrCreate(['model_hash_id'=> $post['hash']],$data);
            //download the model and save to local disk
            
            if(!$model) $model = DB::transaction(function () use($post) {
                $mData = [
                    'hash' => $post['hash'],
                    'name' => $post['name'],
                    'type' => strtolower($post['type']),
                    'nsfw' => $post['nsfw'],
                    'tags' => $post['tags']??'',
                    'base_model' => $post['base_model'],
                    'sd_name' => $post['sd_name'],
                    'download_url' => $post['download_url'],
                    'is_service' => 1,
                    'thumb' => $post['thumb'],
                    'base_size' => $post['base_size'],
                    'engine' => 'lc',
                    'favored' => 1,
                    'model_id' => $post['model_id'],
                    'is_download' => 1,
                ];
          
                $model = AiModel::create($mData);
                if(isset($post['images']) && count($post['images']) > 0){
                    foreach($post['images'] as $val){
                        $iData = [
                            'model_id' => $post['model_id'],
                            'thumb' => $val['thumb'],
                            'width' => $val['width'],
                            'height' => $val['height'],
                        ];
                        AiModelImage::create($iData);
                    }
                }
                return $model;
            });
            
            // read model to file
            DownloadModel::dispatch($downloadLog);
            
            // //download cover image
            // try{
            //     $config = [
            //         'storage' => 'local',
            //     ];
            //     $storage = new UnitStorage($config);
            //     $objects = Helper::getObjectName('model', 'jpeg', 'images');
            //     $storage->putObject($objects[0], file_get_contents($model->thumb));
            //     $model->thumb = $objects[0];
            //     $model->save();
            // }catch(Exception $e){

            // }
            
            return ['code' => GlobalCode::SUCCESS, 'data' => $model];
        }catch(Exception $e){
            return ['code' => 555, 'msg' => __('Server api error').$e->getMessage()];
        }
    }

    public function downloadStatus(Request $request){
        $rules = [
            'ids' =>'required|string',
        ];
        $validator = Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return ['code' => 400, 'msg' => __('Params incorrect').":ids can not be null"];
        }
        $modelIds = json_decode($request->input('ids'));
        $modelStatus = [];
        $errorMessage = [];
        foreach($modelIds as $model_hash_id){
            $cacheKey = Common::getCacheKey('model_hash_id', $model_hash_id);
            $dlPercent = Cache::get($cacheKey);
            $modelStatus[$model_hash_id] = $dlPercent??0;
            //dlPercent must be numberic
            if($dlPercent!=null && is_numeric($dlPercent) == false){
                $errorMessage[$model_hash_id] = $dlPercent;
                $modelStatus[$model_hash_id] = "Failed";
            }
        }

        return ['code' => GlobalCode::SUCCESS, 'data' => $modelStatus, 'error' => $errorMessage];
    }
}
