<?php
namespace App\Services;

use App\Support\Alogd;
use Exception;
use GuzzleHttp\Client;
use Symfony\Component\HttpClient\EventSourceHttpClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Chunk\ServerSentEvent;

class ApiTogether{
    public static function ask(string $model , array $messages, array $config = []){
        $client = new Client();
        $token = Common::getConfigKeyValue('text.together.token');
        foreach($messages as $key => $val){
            foreach($val as $k => $v){
                if(!in_array($k, ['role', 'content'])){
                    unset($messages[$key][$k]);
                }
            }
        }

        $body = [
            'model' => $model,
            'messages' => $messages,
            "max_tokens"=>512,
            "temperature"=>0.7,
            "top_p"=>0.7,
            "top_k"=>50,
            "repetition_penalty"=>1,
            "stop"=>"[\"<|eot_id|>\"]",
            "stream"=>false
        ];
        foreach($config as $k => $v){
            $body[$k] = $v;
        }
        
        $post = [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Content-Type' => 'application/json'
            ],
            // 'json' => $postArr,
            'body' => json_encode($body, JSON_PRETTY_PRINT),
            // 'debug' => true,
        ];
        // var_dump($post);
        // exit;

        try{
            $res = $client->Request('POST', 'https://api.together.xyz/v1/chat/completions', $post);
            $response = $res->getBody()->getContents();
            Alogd::write('ApiTogether:',$response);
            $json = json_decode($response, true);
            $content = [];
            if($response){
                foreach($json['choices'] as $item){
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
            // var_dump($e->getMessage());
            // echo "响应体：" . $e->getResponse()->getBody()->getContents() . "\n";
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

    public static function Symfony($model){
        $client = HttpClient::create();
        $client = new EventSourceHttpClient($client, reconnectionTime: 2);
        $headers = [
            'Authorization' => 'Bearer 4af9c63e2153d6a16b3b0fa9145c98a1e4bd91c1e6af913f4c826f9c1ebef912',
            'Content-Type' => 'application/json',
            'Accept' => 'text/event-stream', // normally set by ->connect()
            'Cache-Control' => 'no-cache', // normally set by ->connect()
        ];
        
        $body = json_encode([
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'who are you?'
                ],
            ],
            "max_tokens"=>512,
            "temperature"=>0.7,
            "top_p"=>0.7,
            "top_k"=>50,
            "repetition_penalty"=>1,
            "stop"=>"[\"<|eot_id|>\"]",
            "stream"=>true
        ]);
        
        $source = $client->request(
            method: 'POST',
            url: 'https://api.together.xyz/v1/chat/completions',
            options: [
                'buffer' => false,
                'headers' => $headers,
                'body' => $body,
            ],
        );
        
        while ($source) {
            foreach ($client->stream($source) as $chunk) {
                if ($chunk instanceof ServerSentEvent) {
                    // dump($chunk->getArrayData());
                    var_dump($chunk);
                }
            }
        }
        
    }

}

