<?php
namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Alogd
{
    public static $strLimit = 1800;

    public static function write($type , $msg, $isOutput = false){
        $userId = Auth::id()??'';
        if(is_string($msg)){
            $logData = strlen($msg) > self::$strLimit ? substr($msg , 0 ,self::$strLimit) : $msg;
            $output = $type.' - '.'User:'.$userId .' '. $logData;
        }
        elseif(self::isBinary($msg)){
            $output = $type.' - '.'User:'.$userId .' '. 'binary:'.strlen($msg);
        }
        elseif(is_object($msg)){
            $json = json_encode($msg, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
            $logData = strlen($json) > self::$strLimit ? substr($json , 0 ,self::$strLimit) : $json;
            $output = $type.' - '.'User:'.$userId .' '. $logData;
        }
        elseif(is_array($msg)){
            $logData = [];
            self::getLogData($msg, $logData);
            $output = $type.' - '.'User:'.$userId .' '. json_encode($logData, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        }
        $output = $type.' - '.'User:'.$userId .' '. substr(json_encode($msg, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE), 0,self::$strLimit);
        if($isOutput){
            echo $output."\n";
        }
        if(env('LOG_DEBUG')){
            if($type == 'debug'){
                Log::channel('debug')->info($output);
            }else{
                Log::channel('task')->info($output);
            }
        }
    }

    public static function getLogData( $arr, & $ret){
        foreach($arr as $k => $v){
            if(is_array($v)){
                self::getLogData($v, $ret);
            }else{
                if(self::isBinary($v)){
                    $ret[$k] = 'binary:'.strlen($v);
                }elseif(is_string($v)){
                    if(strlen($v) > self::$strLimit ){
                        $ret[$k] = substr($v, 0, self::$strLimit);
                    }else{
                        $ret[$k] = $v;
                    }
                }else{
                    $ret[$k] = gettype($v).'...';
                }
            }
        }
        return $ret;
    }

    public static function isBinary($str) {
        if(!is_string(($str))){
            return false;
        }
        return preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0;
    }
    
}
