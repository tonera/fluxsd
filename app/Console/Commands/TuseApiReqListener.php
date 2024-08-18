<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Services\Common;
use App\Services\ImageService;
use App\Support\Alogd;
use App\Support\GlobalCode;
use App\Support\Helper;
use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class TuseApiReqListener extends Command
{
    /**
     * listen FluxSD req
     *
     * @var string
     */
    protected $signature = 'TuseApiReqListener';
    protected $description = 'Command description';
    protected $redis = null;
    public const OUTPUT = true;

    /**
     * php artisan TuseApiReqListener
     */
    public function handle()
    {
        //get tuse api token
        $token = Common::getConfigKeyValue('engine.atz.token');
        if(!$token){
            echo "Token is null\n";
            sleep(3);
            return;
        }
        
        $this->redis = Redis::connection();
        $client = null;
        while (true) {
            $cacheKey = config('public.partjob_status_key');
            $onlineStatus = Cache::get($cacheKey);
            Alogd::write('TuseApiReqListener', "onlineStatus={$onlineStatus}", self::OUTPUT);
            if($onlineStatus != 'online'){
                echo "Part job status is offline\n";
                sleep(2);
                if($client != null){
                    $client->disconnect();
                }
                $client = null;
                continue;
            }else{
                if(!$client){
                    echo "Connect to :ws://".env('WSTS_HOST').":".env('WS_REQ_PORT')."/?token={$token}\n";
                    $client = new \WebSocket\Client("ws://".env('WSTS_HOST').":".env('WS_REQ_PORT')."/?token={$token}");
                }
            }
            try {
                $message = $client->receive();               
                // echo "data:". $message."\n";
                $this->dispatch($message);
 
            } catch (\WebSocket\ConnectionException $e) {
                // Possibly log errors
                echo $e->getMessage()."\n";
                sleep(1);
            }
        }
    }
    //
    protected function dispatch($content)
    {
        
        $package = json_decode($content, true);
        $input = $package['body'];
        Alogd::write('TuseApiReqListener', "Received req from tuse ws server:".$input['task_id'], self::OUTPUT);

        $channel = GlobalCode::CHANNEL_LIST[$input['job_type']];
        $qStat = $this->redis->lpush($channel , json_encode($input));
        Alogd::write('TuseApiReqListener', "qStat:".$qStat, self::OUTPUT);
    } 
}
