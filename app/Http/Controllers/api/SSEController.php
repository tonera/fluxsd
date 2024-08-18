<?php

namespace App\Http\Controllers\api;

use App\Services\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;

class SSEController extends Controller
{
    public function downloadProgress(Request $request){
        if (ob_get_contents()) ob_end_clean();
        $model_hash_ids = [];
        if(isset($request->ids) && $request->ids){
            $model_hash_ids = json_decode($request->ids, true)??[];
        }
        $response =  response()->stream(function () use ($model_hash_ids){
            while (true) {
                $modelStatus = [];
                foreach($model_hash_ids as $hash_id){
                    $cacheKey = Common::getCacheKey('model_hash_id', $hash_id);
                    $dlPercent = Cache::get($cacheKey);
                    if($dlPercent === null){
                        continue;
                    }
                    $modelStatus[$hash_id] = $dlPercent??0;
                    if($dlPercent!=null && is_numeric($dlPercent) == false){
                        $modelStatus[$hash_id] = "Failed";
                    }
                }

                echo 'data: '.json_encode($modelStatus)."\n";
                echo "\n";
                if (connection_aborted()) {break;}
           
                if(ob_get_level()>0){
                    ob_flush();
                }
                
                flush();
                sleep(2);
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
        return $response;
    }
}
