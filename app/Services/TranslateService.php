<?php
namespace App\Services;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use AlibabaCloud\Alimt\Alimt;
use Exception;
use Illuminate\Support\Facades\Log;
use Google\Cloud\Translate\V2\TranslateClient;

class TranslateService{
    public static function translate(array $config, array $arr){
        $cnServicies = ['aliyun'];
        if(env('APP_LOCALE') == 'zh'){
            // Log::emergency("aliyun翻译");
            if(isset($config['translate.aliyun.is_active']) && $config['translate.aliyun.is_active']){
                $accessKey = $config['translate.aliyun.access_key']??null;
                $accessSecret = $config['translate.aliyun.secret_key']??null;
                if(!$accessKey || !$accessSecret){
                    // Log::emergency("阿里云翻译服务出错了:accesskey或者secret为空:{$accessKey} {$accessSecret}");
                    return $arr;
                }else{
                    $ret = [];
                    foreach($arr as $content){
                        $ret[] = self::aliyunTrans($config, $content);
                    }
                    return $ret;
                }
            }else{
                // Log::emergency("中国区无激活的翻译服务，文本直接不翻译返回");
                return $arr;
            }
        }else{
            //try google
            // Log::emergency("Google翻译");
            if(isset($config['translate.google.is_active']) && $config['translate.google.is_active']){
                if(isset($config['translate.google.access_key']) && $config['translate.google.access_key']){
                    return self::GoogleTrans( $config, $arr);
                }else{
                    // Log::emergency("Google翻译access_key为空，直接返回原文本");
                    return $arr;
                }
            }else{
                // Log::emergency("海外区Google翻译没有激活，文本直接不翻译返回");
                return $arr;
            }
        }
    }

    public static function aliyunTrans(array $config, string $content,  $srcLang='zh', $tagLang='en'){
        if(!$content){
            return '';
        }
        AlibabaCloud::accessKeyClient($config['translate.aliyun.access_key'] , $config['translate.aliyun.secret_key'])
        ->regionId('cn-hangzhou')
        ->asDefaultClient()->options([]);
        try {
            $request = Alimt::v20181012()->translateGeneral();
            $result = $request
        
                ->withFormatType("text")
                ->withSourceLanguage($srcLang)
                ->withTargetLanguage($tagLang)
                ->withSourceText($content)
                ->withScene("general")
                ->debug(false) // Enable the debug will output detailed information
        
                ->request();
            
            $res = $result->toArray();
            if($res['Code'] == '200'){
                return $res['Data']['Translated'];
            }else{
                // Log::emergency("阿里云翻译服务出错了。".json_encode($res, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
                return $content;
            }
        } catch (ClientException $exception) {
            // echo $exception->getMessage() . PHP_EOL;
            // Log::emergency("阿里云翻译服务出错了。".$exception->getMessage() );
            return $content;
        } catch (ServerException $exception) {
            // echo $exception->getMessage() . PHP_EOL;
            // echo $exception->getErrorCode() . PHP_EOL;
            // echo $exception->getRequestId() . PHP_EOL;
            // echo $exception->getErrorMessage() . PHP_EOL;
            // Log::emergency("阿里云翻译服务出错了。".$exception->getMessage() );
            return $content;
        }
    }

    public static function GoogleTrans(array $config, array $contentArray,  $srcLang='zh', $tagLang='en'){
        $translate = new TranslateClient([
            'key' => $config['translate.google.access_key']
        ]);
        try{
            $results = $translate->translateBatch($contentArray,[
                'target' => $tagLang
            ]);
            $transArray = [];
            foreach ($results as $result) {
                $key = array_search($result['input'],$contentArray);
                $transArray[$key] = $result['text'];
                //echo $result['text']."<br/>\n";
            }
            return array_values($transArray);
        }catch(Exception $e){
            // Log::error('调用Google翻译出错:'.$e->getMessage());
            return $contentArray;
        }
        
    }
}