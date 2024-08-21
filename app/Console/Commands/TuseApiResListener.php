<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Services\Common;
use App\Services\ImageService;
use App\Support\Alogd;
use App\Support\Helper;
use App\Support\ReverbClient;
use App\Support\TuseMessage;
use Exception;
use GuzzleHttp\Client as GuzzleHttpClient;
use Illuminate\Console\Command;


class TuseApiResListener extends Command
{
    /**
     * listen FluxSD res
     *
     * @var string
     */
    protected $signature = 'TuseApiResListener';
    protected $description = 'Command description';

    /**
     * php artisan TuseApiResListener
     */
    public function handle()
    {
        // get tuse api token
        $token = Common::getConfigKeyValue('engine.atz.token');
        if(!$token){
            echo "Token is null\n";
            sleep(3);
            return;
        }
        echo env('FLUXSD_API_WS_RES')."/?token={$token}\n";
        $client = new \WebSocket\Client(env('FLUXSD_API_WS_RES')."/?token={$token}", ['timeout'=>5]);
    
        while (true) {
            try {
                $message = $client->receive();               
                echo "data length:". strlen($message) ."\n";
                self::dispatch($message);
                sleep(1);
            } catch (\WebSocket\ConnectionException $e) {
                // Possibly log errors
                echo $e->getMessage()."\n";
                sleep(3);
            }
        }
    }

    public static function dispatch($content)
    {
        $isOutput = true;
        Alogd::write('TuseApiResListener', "Received msg from TSAPI WS server:", $isOutput);
        Alogd::write('TuseApiResListener', $content, $isOutput);
        if(!$content){
            return;
        }
        try{
            $package = json_decode($content, true);
            $message = $package['body'];
            $task_id = $message['reference_id'];
            $task = Task::where('task_id', $task_id)->first();
            if(!$task){
                Alogd::write('TuseApiResListener', "task:".$message['reference_id']." not found.", $isOutput); 
                return;
            }
            $message['user_id'] = $task->user_id;
            $message['task_id'] = $task->task_id;
            // $package = json_encode($message);
            // var_dump($message);
            Alogd::write('TuseApiResListener', "Process task:".$task->task_id, $isOutput);
            //download and save image
            if(isset($message['show_url'])){
                // $imageBytes = file_get_contents($message['show_url']);
                $client = new GuzzleHttpClient();
                $imageBytes = $client->get($message['show_url'])->getBody()->getContents();

                $fileType = $task->act == 'RBG'?'png':'jpeg';
                $objects = Helper::getObjectName('user_res', $fileType,'storage');
                ImageService::storageImages($imageBytes, $objects, $task);
                $message['big'] = $objects[0];
                $message['thumb'] = $objects[1];
                $cmd = new LocalEngineListener();
                $cmd->process($message);
            
                // var_dump($message);
                echo "收到来自tuse-api的消息，推送前端:". strlen(json_encode($message))."\n";
                
                Alogd::write('TuseApiResListener', "Download image, task_id:".$task->task_id." img={$message['show_url']} size=". strlen($imageBytes). " path=".$message['big'], $isOutput);
        
            }else{
                Alogd::write('TuseApiResListener', "Not found show_url , ephemeral msg.", $isOutput);
                $tm = new TuseMessage($message);
                $tm->validator();
                $execTime = Helper::getExecTime($task);
                ReverbClient::sendMessage($tm->package('ephemeral', $execTime));
            }
            

        }catch(Exception $e){
            var_dump($e->getTraceAsString());
            Alogd::write('TuseApiResListener', "Parse data error:".$e->getMessage(), $isOutput);
        }
        
        // echo $content->data."\n\n";
    
    } 
}
