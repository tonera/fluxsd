<?php
namespace App\Services;

use App\Models\AiConfig;
use App\Models\User;
use App\Support\IDGenerator;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class Common{
    //clear cache for config
    public static function clearConfigCache(){
        $keys = ['aibox_tbl_config'];
        foreach($keys as $key){
            Cache::forget($key);
        }
    }

    public static function getConfig():array
    {
        $merge = [];
        $list = AiConfig::all()->toArray();
        foreach($list as $item){
            $res = self::strToArrayMultiLevel($item['c_key'], $item['c_value'], '.');
            $merge = array_merge_recursive($merge, $res);
        }
        
        return $merge;
    }

    public static function getConfigKeyValue($key = null){
        $cacheKey = "aibox_tbl_config";
        $content = Cache::get($cacheKey);
        if(!$content){
            $list = AiConfig::all()->toArray();
            $content = json_encode($list);
            Cache::put($cacheKey, $content);
        }else{
            $list = json_decode($content, true);
        }
        
        $ret = [];
        foreach ($list as $value) {
            $ret[$value['c_key']] = $value['c_value'];
        }
       
        if($key === null){
            return $ret;
        }else{
            if(isset($ret[$key])){
                return $ret[$key];
            }else{
                return null;
            }
        }
    }

    public static function getAccessUrl(string $storage){
        $access_url = self::getConfigKeyValue('storage.'.$storage.'.access_url');
        if($access_url){
            if(substr($access_url, -1) != '/'){
                $access_url = $access_url.'/';
            }
        }else{
            $access_url = env('APP_URL').'/';
        }
        return $access_url;
    }

    public static function getOnlyActiveConfig(string $key)
    {
        $appConfig = self::getConfig();
        if(isset($appConfig[$key]) && is_array($appConfig[$key])){
            foreach($appConfig[$key] as $key => $val){
                if(isset($val['is_active']) && $val['is_active'] == 1){
                    $val['indexKey'] = $key;
                    return $val;
                }
            }
        }
        return false;
    }

    public static function strToArrayMultiLevel($str, $val, $delimiter = '.') {
        $result = [];
        $array = explode($delimiter, $str);
        $temp = &$result;
        foreach ($array as $key) {
            $temp = &$temp[$key];
        }
        $temp = $val;
        return $result;
    }

    public static function getCdnUrl(string $path, string $storage){
        if(!$path) return '';
        if($path[0] == '/'){
            $path = substr($path,1);
        }
        return self::getAccessUrl($storage).$path;
    }

    //get atz models and cache it
    public static function getTuseModels(array $params){
        //string $types, string $bmodel, $page, $keyword=null

        $cacheKey = md5("tuse_models". implode('.',$params));
        $apiUrl = env('TUSE_API').'/v1/models';
        $content = Cache::get($cacheKey);
        $content = null;
        if($content ){
            return $content;
        }else{
            $client = new Client();
            $options = [
                'headers' => [
                    'Authorization' => 'Bearer e1eadcc9a6aa574239227c613aaece94dfd2d516db358334f21a9b59bb65bc6a',
                    'Content-Type' => 'application/json'
                ],
                'query' => $params,
            ];
            $res = $client->Request('GET', $apiUrl, $options);
            $content = $res->getBody()->getContents();
            Cache::put($cacheKey , $content , 24*3600);//1 day
            return $content;
        }
        
    }

    public static function formatBytes($size, $precision = 2)
    {
        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');

            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        } else {
            return $size;
        }
    }

    //get models or loras saved path
    public static function getModelPath($type){
        $path = Common::getConfigKeyValue('engine.lc.client_path');
        $path = $path?$path:base_path();
        $middlePath = '';
        if($type == 'lora'){
            $middlePath = 'loras';
        }else{
            $middlePath = 'models';
        }
        $path = realpath($path) . DIRECTORY_SEPARATOR . $middlePath;
        return $path;
    }

    public static function getCacheKey($type, $value){
        switch($type){
            case 'model_hash_id':
                return 'DL_PC_'.$value;
            default:
            return 'TMP_'. $value;
        }
    }

    //get part job types
    public static function getServiceJobTypes():array
    {
        return ['MK'];
    }

    //get local ip
    public static function getHostIp(){        
        $ip = '';
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_connect($socket, '8.8.8.8', 53);
        socket_getsockname($socket, $localIP);
        socket_close($socket);
        $ip = inet_ntop(inet_pton($localIP));
        return $ip;
    }


    public static function isInstalled(){
        $cacheKey = 'has_admin';
        $content = Cache::get($cacheKey);
        if($content){
            return true;
        }else{
            $admin = User::where('is_admin', 1)->first();
            if($admin){
                Cache::forever($cacheKey, $admin->id);
                return true;
            }else{
                return false;
            }
        }
    }

    public static function getClientConfig(){
        $localIp = env('APP_HOST');
        $clientConfig = "[base]\n";
        $clientConfig .= "job_type=MK\n";
        $clientConfig .= "access_key=".env('AI_CLIENT_KEY', IDGenerator::id())."\n";
        $clientConfig .= "client_id=".env('AI_CLIENT_ID', IDGenerator::ids())."\n";
        $clientConfig .= "check_url=http://".$localIp.":".env('APP_PORT',8000)."/api/job/checktoken\n";
        $clientConfig .= "callback=http://".$localIp.":".env('APP_PORT',8000)."/api/job/callback\n";
        $clientConfig .= "local_store=.".DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR."\n";
        return $clientConfig;
    }

}