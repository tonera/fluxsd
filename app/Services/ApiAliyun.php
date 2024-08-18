<?php
namespace App\Services;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\HttpClient\EventSourceHttpClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Chunk\ServerSentEvent;

class ApiAliyun{
    public static function ask(string $model , array $messages, array $config = []){
        $client = new Client();
        $token = Common::getConfigKeyValue('text.qianwen.token');
        //qwen-turbo
        $post = [
            'model' => $model,
            'input' => [
                'messages' => $messages,
            ],
            'parameters' => ['result_format' => 'message'],
        ];
        //Env::get('app.district') 
        $header = [
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer ".$token,
        ];
        // var_dump($header);
        // exit;
        $client = new Client();
        try{
            $res = $client->post('https://dashscope.aliyuncs.com/api/v1/services/aigc/text-generation/generation',  [
                'headers' => $header,
                'json' => $post,
                // 'debug' => true,
            ]);
            // var_dump($res);
            $response = $res->getBody()->getContents();
            
            if($response){
                $json = json_decode($response, true);
                $content = [];
                foreach($json['output']['choices'] as $item){
                    $tmp = [];
                    $tmp['role'] = $item['message']['role'];
                    $tmp['content'] = $item['message']['content'];
                    $tmp['created_at'] = date("Y/m/d H:i:s");
                    $tmp['author'] = 'AI';
                    $content[] = $tmp;
                }
                return $content;

            }else{
                throw new Exception('Res error:'.$response);
            }
        }catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->hasResponse()) {
                throw new Exception('HTTP error:'.$e->getResponse()->getStatusCode() . " - " . $e->getResponse()->getReasonPhrase());
            } else {
                throw new Exception('Net error:'.$e->getMessage());
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public static function formatMessage(string $content , array $context = [] , array $system = []){
        $userMessage = [
            'role' => 'user',
            'content' => $content
        ];
        $context[] = $userMessage;
        if(!empty($system)){
            $context[] = $system;
        }
        return $context;
    }


}

