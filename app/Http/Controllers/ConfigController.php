<?php

namespace App\Http\Controllers;

use App\Services\Common;
use Inertia\Inertia;

class ConfigController extends Controller
{
    public function index(){
        $configMeta = config('config_meta');
        $merge = Common::getConfig();
        
        // var_dump($merge['engine']);
        $config = array_replace_recursive($configMeta, $merge);
        foreach($config as $key => $val){
            foreach($val as $k => $v){
                if(!isset($configMeta[$key][$k])){
                    unset($config[$key][$k]);
                }
            }
        }

        // var_dump($configMeta);
        return Inertia::render('Config/Index',[
            'ai_config' => $config,
            'config_options' => [
                ['label' => __('ai engine'),'option' => 'engine'],
                ['label' => __('Text model'),'option' => 'text'],
                ['label' => __('storage'),'option' => 'storage'],
                ['label' => __('translate'),'option' => 'translate'],
            ],
            'clientConfig' => Common::getClientConfig(),
        ]); 
    }

}
