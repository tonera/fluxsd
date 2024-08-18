<?php

namespace App\Http\Controllers;

use App\Services\Common;
use App\Support\JwtToken;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ImageController extends Controller
{
    public function index(){
        $user = Auth::user();
        $tokenData = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'ctime' => $user->created_at->format('Y-m-d H:i:s'),
        ];
        $token = JwtToken::signToken($tokenData);

        $configArr = Common::getConfig();
        $listEngines = $configArr['engine']??[];
        $engines = [];
        foreach($listEngines as $key => $val){
            if($val['is_active'] == 1){
                $engines[] = [
                    'label' => __($key),
                    'engine' => $key
                ];
            }
        }
        return Inertia::render('Image/ImageIndex',[
            'sdratio' => config('aspects.sdratio'),
            'omratio' => config('aspects.omratio'),
            'token' => $token,
            'omSamplers' => $this->getSampler('om') ,
            'lcSamplers' => $this->getSampler('lc') ,
            'sdSamplers' => $this->getSampler('sd') ,
            'engines' => $engines,
        ]); 
    }

    protected function getSampler($engine){
        // $list = Helper::lookup('am_models', 'civitai_image_sampler_name',['engine'=>$engine]);
        $config = config('samplers');
        if(!isset($config[$engine])){
            return [];
        }
        $list = $config[$engine];
        $ret = [];
        foreach($list as $key => $value){
            $ret[] = ['label' => $value, 'val' => $value];
        }
        return $ret;
    }

}
