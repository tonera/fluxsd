<?php
namespace App\Support;

use Illuminate\Support\Facades\App;
use Exception;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use App\Services\DiscordService;

class Helper{

    public static function getFileExtension($str, $type){
        $ext = '';
        switch($type){
            case 'suffix':
                $ext = $str;
                break;
            case 'url':
                $arr = parse_url($str);
                $ext = pathinfo($arr['path'], PATHINFO_EXTENSION);
                break;
            case 'base64':
                $tmp = explode(':', substr($str, 0, strpos($str, ';')));
                if(count($tmp) > 1){
                    $ext = explode('/', explode(':', substr($str, 0, strpos($str, ';')))[1])[1];
                }else{
                    $ext = 'jpg';
                }
                break;
            default:
                $ext = pathinfo($str, PATHINFO_EXTENSION);
                break;
        }
        return $ext;
    }

    public static function getObjectName($from='user', $fileType='jpeg', $path = ""){
        $month = date("Ym");
        $big = md5(IDGenerator::id().'b').".".$fileType;
        $small = md5(IDGenerator::id().'s').".jpeg";
        $bigArr = [];
        $smallArr = [];
        switch($from){
            case 'user':
                $bigArr =  [$path,"init",$month,$big];
                break;
            case 'avatar':
                $bigArr =  [$path,"avatar",$month,$big];
                break;
            case 'user_res':
                $bigArr =  [$path,$month,$big];
                $smallArr = [$path,$month,$small];
                break;
            case 'store':
                $bigArr =  [$path,"orig",$month,$big];
                $smallArr =  [$path,"thum",$month,$small];
                break;
            case 'model':
                $bigArr =  [$path,"models",$big];
                $smallArr =  [$path,"models",$small];
                break;
            default:
                $bigArr =  [$path,"others",$month,$big];
                $smallArr =  [$path,"others",$month,$small];
        }
        return [implode('/',$bigArr), implode('/',$smallArr)];
    }

    public static function isHanzi(?string $str, $rate = 0.3){
        if(empty($str)){
            return false;
        }
        $total = mb_strlen($str);
        preg_match_all("/[\x{4e00}-\x{9fa5}]+/u", $str, $matches);//chinese
        $hanzi = 0;
        foreach($matches[0] as $item){
            $hanzi += mb_strlen($item);
        }
        return ($hanzi/$total) > $rate;
    }

    public static function getButtons(array $task){
        if(empty($task)) return [];
        $buttonsGroup = [];
        try{
            //-mj
            if($task['engine'] == GlobalCode::MJ && isset($task['task_pkg']) && !empty($task['task_pkg'])){
                $pkg = json_decode($task['task_pkg']);
                $buttonsGroup = DiscordService::getButtonGroups($pkg->components??null);
            }
            $buttons = [];
            if(!in_array($task['act'], ['SR', 'PT','APT'])){
                $buttons[] = ['act'=> 'SR', 'label' => __('HD'),'api'=>env('BTN_API')];
            }
            if(!in_array($task['act'], ['RBG', 'PT','APT'])){
                $buttons[] = ['act'=> 'RBG', 'label' => __('Remove_BG'),'api'=>env('BTN_API')];
            }
            $buttons[] = ['act'=> 'EDIT', 'label' => __('Edit'),'api'=>env('BTN_API')];
            $buttonsGroup[] = $buttons;
        }catch(Exception $e){
            ;
        }
        return $buttonsGroup;
    }

    public static function getLimitSize($w , $h, $limit ){
        $rate = $w/$h;
        if($w > $h){
            $w = $limit;
            $h = $w/$rate;
        }else{
            $h = $limit;
            $w = $h * $rate;
        }
        return ['width' => ceil($w), 'height' => ceil($h)];
    }

    public static function getOutPaintingSize($width, $height, $direction = 'center'){
        $max = 1024;//
        $rate = 0.3;//max 50%
        //init
        $size = self::getLimitSize($width, $height, $max/(1+$rate));
        $orgWidth  = $size['width'];
        $orgHeight = $size['height'];
        $newHeight = $newWidth  = 0;
        if($direction == 'center'){
            $newWidth = $orgWidth * ($rate+1);
            $newHeight = $orgHeight * ($rate+1);
        }else{
            if( $direction == 'left' ||  $direction == 'right'){
                $newWidth = $orgWidth * (1+$rate);
                $newHeight = $orgHeight;
            }else{
                //up or down
                $newWidth = $orgWidth;
                $newHeight = $orgHeight * (1+$rate);
            }
        }

        $center_x = $center_y = 0 ;
        switch($direction){
            case 'center':
                break;
            case 'left':
                $center_x = $orgWidth * $rate;
            break;
            case 'right':
                $center_x = - $orgWidth * $rate;
            break;
            case 'up':
                $center_y = - $orgHeight * $rate;
            break;
            case 'down':
                $center_y = $orgHeight * $rate;
            break;
        }
        return [intval($orgWidth ), intval($orgHeight), intval($newWidth) , intval($newHeight),intval($center_x) ,intval($center_y) ];
    }

    public static function getClearBase64Image(string $base64){
        if(strpos($base64, ",") === false){
            return $base64;
        }else{
            $base64 = substr($base64 , strpos($base64 , ","));
            if($base64[0] == ','){
                $base64 = substr($base64 , 1 );
            }
            return $base64;
        }
        
    }


    public static function getExecTime($task){
        $usedTimeKey = Config::get('public.last_task_used_time_key').$task->engine.'_'.$task->model_name.'_'.$task->act;
        $startTime = $task->created_at->timestamp;
        $cacheTime = Cache::get($usedTimeKey) ;
        $lastUsedTime = $cacheTime ? intval($cacheTime) : time();
        $execTime = $lastUsedTime - (time() - $startTime);
        $execTime = $execTime < 0 ? 30:$execTime;
        $execTime = $execTime * ($task->image_num??1);
        // Alogd::write(GlobalCode::COMMON, "{$usedTimeKey}缓存时间:{$execTime}");
        $execTime = $execTime > 300 ? 0 : $execTime;
        return $execTime;
    }
    public static function setExecTime($task){
        $usedTimeKey = Config::get('public.last_task_used_time_key').$task->engine.'_'.$task->model_name.'_'.$task->act;
        $usedTime = time() - ($task->created_at->timestamp);
        $usedTime = ceil($usedTime / ($task->image_num??1));
        Cache::set($usedTimeKey, $usedTime );
    }


    /**
     * input input max value
     * output output max value
     * val current value
     */
    public static function getBenchParams($inputMax, $outputMax, $val){
        return $val*$outputMax/$inputMax;
    }

    public static function transConfig($key){
        $list = Config::get($key);
        switch($key){
            case 'toolbox':
                foreach($list as $key => $item){
                    $list[$key]['label'] = __('pub.'.$list[$key]['label']);
                    if(isset($item['switcher'])){
                        foreach($item['switcher'] as $key2 => $val2){
                            $list[$key]['switcher'][$key2]['label'] = __('pub.'.$val2['label']);
                        }
                    }
                    //doubleImage
                    if(isset($item['doubleImage'])){
                        $list[$key]['doubleImage']['label1'] = __('pub.'.$list[$key]['doubleImage']['label1']);
                        $list[$key]['doubleImage']['label2'] = __('pub.'.$list[$key]['doubleImage']['label2']);
                    }
                    if(isset($item['singleSelector'])){
                        foreach($item['singleSelector'] as $key2 => $val2){
                            $list[$key]['singleSelector'][$key2]['label'] = __('pub.'.$val2['label']);
                        }
                    }
                    if(isset($item['inputLabel'])){
                        $list[$key]['inputLabel'] = __($item['inputLabel']);
                    }
                }
                return $list;    
            break;
        }
    }

    public static function getConfigByEnv($name){
        if(Env::get('APP_AREA','CN') == 'CN'){
            return Config::get($name);
        }else{
            return Config::get($name.'En');
        }
    }

    public static function simplifyFraction($numerator, $denominator) {
        function gcd($a, $b) {
            if ($b == 0) {
                return $a;
            } else {
                return gcd($b, $a % $b);
            }
        }
        $gcd_value = gcd($numerator, $denominator);
    
        $simplified_numerator = $numerator / $gcd_value;
        $simplified_denominator = $denominator / $gcd_value;
    
        $simplified_denominator = abs($simplified_denominator);
    
        return $simplified_numerator.':'.$simplified_denominator;
    }

    public static function getLargestEightMultiple($width, $height, $limit){
        $size = self::getLimitSize($width, $height, $limit);
        return [
            'width' => self::find_largest_multiple_of_eight($size['width']), 
            'height' => self::find_largest_multiple_of_eight($size['height']), 
        ];
    }

    public static function find_largest_multiple_of_eight($limit) {
        $remainder = $limit % 8;
        if ($remainder == 0) {
            return $limit;
        } else {
            $next_multiple = min($limit - $remainder, $limit - $remainder + 8);
            return $next_multiple;
        }
    }

    public static function isEnglish($string)
    {
        // Removing html tags
        $string = strip_tags($string);

        // Removing html entities
        $string = preg_replace('/&([a-z0-9]+|#[0-9]{1,6}|#x[0-9a-fA-F]{1,6});/is', '', $string);
        
        // Removing symbols
        $string = preg_replace('/[-!$%^&*()_+|~=`{}\[\]:";\'<>?,.\/#’—-–]/si', '', $string);

        // Removing spaces
        $string = preg_replace('/\s+/si', '', $string);
        
        // Counting english characters
        preg_match_all('/\w+/si', $string, $english_match);
        $english_char = strlen(implode('', $english_match[0]));

        // Counting non english characters
        preg_match_all('/\W+/si', $string, $match);
        $non_english_char = strlen(implode('', $match[0]));

        // Checks if number of english characters are grater

        if ($english_char > $non_english_char)
        {
            return true;
        }

        return false;
    }
    
}
?>