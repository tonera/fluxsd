<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use App\Models\DownloadLog;
use Illuminate\Support\Facades\Route;
use App\Services\Common;
use App\Support\IDGenerator;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Illuminate\Foundation\Application;

class IndexController extends Controller
{
    public function index(){
        $isInstalled = Common::isInstalled();
        if(!$isInstalled){
            return redirect()->route('index.setup');
        }

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'app_meta' => config('app_meta')
        ]);
    }
    
    //models market
    public function models(){

        $list = AiModel::where('engine', 'lc')->get();
        $modelIds = $list->where('is_download',1)->pluck('hash')->all();
        $failedIds = $list->where('is_download',0)->pluck('hash')->all();

        $dlList = DownloadLog::get();
        $dlPercent = [];
        foreach ($dlList as $key => $value) {
            $cacheKey = Common::getCacheKey('model_hash_id', $value->model_hash_id);
            $dlPercent[$value->model_hash_id] = Cache::get($cacheKey)??0;
        }

        return inertia::render('Models/Index',[
            'modelIds' => $modelIds,
            'failedIds' => $failedIds,
            'dlPercent' => $dlPercent,
            'client_path' => Common::getConfigKeyValue('engine.lc.client_path')??'',
        ]); 
    }

    //video index
    public function video(){
        return inertia::render('Video/Index',[
        ]); 
    }

    public function text(){
        $configArr = Common::getConfig();
        $engines = [];
        if(isset($configArr['text']) && is_array($configArr['text'])){
            foreach($configArr['text'] as $key => $val){
                if($val['is_active'] == 1){
                    $engines[] = [
                        'label' => __($key),
                        'engine' => $key
                    ];
                }
            }
        }
        
        return inertia::render('Text/Index',[
            'engines' => $engines,
        ]); 
    }

    public function setup(){
        $localIp = env('APP_HOST');
        $isInstalled = Common::isInstalled();
        if($isInstalled){
            return redirect()->route('index');
        }
        return inertia::render('Setup',[
            'clientConfig' => Common::getClientConfig(),
            'localIp' => $localIp,
            'localPort' => env('APP_PORT',8000)
        ]); 
    }
}
