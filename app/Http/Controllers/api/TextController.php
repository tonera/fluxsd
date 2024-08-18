<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\ApiAliyun;
use App\Services\ApiTogether;
use App\Support\Alogd;
use App\Support\GlobalCode;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TextController extends Controller
{
    public function models(Request $request){
        $engine = $request->engine??'';
        $config = config('model_text');
        if(isset($config[$engine])){
            $list = [];
            foreach($config[$engine]['models'] as $val){
                $tmp = explode('/', $val);
                $list[] = [
                    'name' => $val,
                    'label' => $tmp[count($tmp)-1],
                ];
            }
            return ['code' => GlobalCode::SUCCESS, 'data' => ['config'=> $config[$engine]['config'], 'models' => $list]];
        }else{
            return ['code' => 400, 'msg' => __('Params incorrect')];
        }
    }

    public function ask(Request $request){
//         $ret = <<<EOF

// EOF;
//         $tmp = $content= [];
//         $tmp['role'] = 'assistant';
//         $tmp['content'] = $ret;
//         $tmp['created_at'] = date("Y/m/d H:i:s");
//         $tmp['author'] = 'AI';
//         $content[] = $tmp;
//         return ['code' => GlobalCode::SUCCESS, 'data' => $content];

        $post = $request->post();
        $engine = $post['engine']??'';
        $model_text = config('model_text');
        $models = isset($model_text[$engine])? $model_text[$engine]['models'] :[];
        
        $rules = [
            'messages' => 'required|string',
            'model_config' => 'required|string',
            'engine' => ['required', Rule::in(array_keys($model_text))],
            'model' => ['required', Rule::in($models)],
        ];
  
        $validator = Validator::make($post , $rules);
        if ($validator->fails()) {
            return ['code' => 10010, 'msg' => 'Params check failed'.json_encode($validator->errors()->messages())];
        }
        
        $context = json_decode($post['messages'], true);
        $model_config = [];
        if(isset($post['model_config']) && $post['model_config']){
            $post_config = json_decode($post['model_config'], true);
            $model_text = config('model_text');
            $allowedKeys = [];
            foreach($model_text[$engine]['config'] as $val){
                $allowedKeys[] = $val['key'];
            }            
            foreach($allowedKeys as $key){
                if(isset($post_config[$key])){
                    $model_config[$key] = $post_config[$key];
                }
            }
        }
        
        try{
            // Alogd::write('Text ask:', $post['engine'].':'.$post['messages']. '.config='.json_encode($model_config));
            if($post['engine'] == 'qianwen'){
                $content = ApiAliyun::ask($post['model'], $context,  $model_config);
            }else{
                $content = ApiTogether::ask($post['model'], $context,  $model_config);
            }
            if(is_array($content)){
                return ['code' => GlobalCode::SUCCESS, 'data' => $content];
            }else{
                Alogd::write('error1:', $content);
                return ['code' => 555, 'msg' => __('Api error')];
            }
        }catch (Exception $e){
            Alogd::write('error2:', $e->getMessage());
            return ['code' => 555, 'msg' => $e->getMessage()];
        }
        
    }
}
