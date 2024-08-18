<?php
namespace App\Support;

use Illuminate\Support\Facades\Log;
use Swoole\Coroutine\Http\Client;
use function Swoole\Coroutine\run;
use Illuminate\Support\Env;

//swoole管理客户端口
class Swoole
{
    public static function sendMessage($message){
        $package = [
            'type'=>'response',
            'body'=> $message
        ];
        $client = new WsSwooleClient('127.0.0.1', Env::get('WS_PORT'), '/?token='.Env::get('publicWSToken').'&task_id=transfer' );
        $client->connect();
        $client->send(json_encode($package, JSON_UNESCAPED_SLASHES));
    }
    

    //已废弃，在线上会引起协程错误：只能在cgi模式下运行
    public static function _sendMessage($message){
        \Swoole\Coroutine\run(function () use($message) {
            echo "Send msg to ".Env::get('WS_HOST').":".Env::get('WS_PORT')."\n";
            $cli = new Client('127.0.0.1', Env::get('WS_PORT'));
            //$cli = new Client('192.168.0.105', 9501);
            $cli->upgrade('/?token='.Env::get('publicWSToken').'&task_id=transfer');
            // $cli->setHeaders([
            //     'User-Agent' => 'Chrome/49.0.2587.3',
            //     'Accept' => 'text/html,application/xhtml+xml,application/xml',
            // ]);
            $package = [
                'type'=>'response',
                'body'=> $message
            ];
            $cli->push(json_encode($package));
            $cli->close();
        });
    }
}
?>

