<?php
namespace App\Support;

use App\Services\Common;
use App\Services\ImageService;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

const CON_YES = 'yes';
const CON_NO  = 'no';
const CON_MODEL = 'checkpoint';
const CON_LORA  = 'lora';
const ENGINE_LIST = [''];
const CON_LOCAL  = 'local';
const CON_SERVER  = 'server';
const CON_OSS  = 'oss';
const CON_S3  = 's3';
const CON_DEFAULT_MODELXL = 'xl-base'; //xl-base
const CON_DEFAULT_MODELXL_ID = '1TT5AVZLU3UO';
const CON_DEFAULT_MODEL15 = 'v1-5'; //
const CON_DEFAULT_MODEL15_ID = '1TT5AWACWDFK';
const CON_PIXART_MODEL_NAME = 'pixart'; 
// const CON_DEFAULT_SCHEDULER = '';
const CON_SDXL_TAGS = ['sdxl','presdxl','presd3'];

class UnitRequest{
    public $task_owner = "self";
    public $id;
    public $act;
    public $engine;
    public $prompt;
    public $negative_prompt;
    public $width = 1024;
    public $height = 1024;
    public $cfg_scale = 7.5;
    public $seed = 0;
    public $steps = 30;
    public $denoising_strength = 0.3;
    public $file_type;
    public $init_img_path;
    public $image_file2;
    public $image_num = 1;
    public $enable_freeu = CON_YES;
    public $model_hash_id;       
    public $lora_hash_id;          
    public $lora_scale = 0.9;           
    public $upscale = 0;             
    public $face_enhance = CON_YES;     
    public $crop_face = CON_YES;       
    public $tpl_list = [];             
    public $sampler_name = null;
    public $weird;                  // mj 0-3000
    public $cref;                   // mj 1-100
    public $sref;                   // mj 1-10
    public $niji;                   // mj
    public $collection_id;
    public $reference_id = '';
    public $asp_id = 0;

    public $prompt_en;                
    public $negative_prompt_en;         
    public $generate_type = 'txt2img';  
    public $lora_name;                
    public $lora_prompt;               
    public $model_name;               
    public $model_version;          
    public $base_size = 1024;         
    public $file_size = 0;           
    public $initImgBtyes;            

    public $storage = 'local';
    public $float16 = true; 
    public $big;                        
    public $thumb;
    public $user_id;
    public $task_id;
    public $message_type = 'user';
    public $face_arch = 'clean';
    public $face_mode = 'normal';

    public function __construct(array $data)
    {
        foreach($data as $key => $val){
            $this->$key = $val;
            if(($key == 'model_hash_id')){
                $this->initModelInfo($val, $data['engine']);
            }elseif(($key == 'lora_hash_id') && in_array($data['engine'], [GlobalCode::SD, GlobalCode::LC])){
                $this->initLoraInfo($val);
            }elseif($key == 'file_type'){
                $this->file_type = strtolower($val);
            }elseif($key == 'storage'){
                $this->storage = strtolower($val);
                if(!in_array($this->storage, [CON_LOCAL, CON_SERVER, CON_S3, CON_OSS])){
                    throw new Exception("Storage:{$this->storage} is not found");
                }
            }elseif($key == 'tpl_list' && !empty($val)){
                $this->tpl_list = json_decode($val, true);
            }elseif($key == 'sampler_name' && $val=='default'){
                $this->sampler_name = null;
            }
        }
        $this->check();
    }
    public function check(){
        switch($this->act){
            case 'MK':
            case 'APT':
            case 'generate':
                //size limit
                $maxSizeLimit = 1536;
                if($this->width > $maxSizeLimit || $this->height > $maxSizeLimit){
                    $maxSize = $this->width > $this->height ? $this->width  : $this->height ;
                    $scaleRate = $maxSize/$maxSizeLimit;
                    $this->width = intval($this->width /$scaleRate);
                    $this->height = intval($this->height /$scaleRate);
                    $this->width = $this->width - $this->width%64;
                    $this->height = $this->height - $this->height%64;
                }
                $rules = [
                    'engine' => ['required', Rule::in(GlobalCode::getEngingList())],
                    'prompt'=>'required|max:500',
                    'asp_id'=>'required|integer|min:0|max:10',
                    'seed'=>'required|integer|min:-1|max:4294967295',
                    
                    'steps'=>'required|integer|min:1,max:50',
                    // 'style_id'=>'required|integer',         
                    //model_type
                    //model_name
                    'collection_id'=>'numeric|nullable',
                    'negative_prompt'=>'max:500',
                    'denoising_strength' => 'numeric|min:0,max:1',
                    'width'=>'numeric|min:1|max:2048',
                    'height'=>'numeric|min:1|max:2048',
                    'enable_freeu' => [Rule::in([CON_YES, CON_NO])],
                    'file_type'=> Rule::in(['jpeg','png',null]),
                    'image_num' => 'integer',
                    
                ];
                if($this->engine != GlobalCode::MJ){
                    $rules = $this->mergeData($rules, [
                        'model_hash_id'=>'required_without:lora_hash_id',//模型名
                        'lora_hash_id'=>'required_without:model_hash_id',
                        'cfg_scale'=>'required|numeric|min:0|max:30',
                    ]);
                    if(!$this->model_hash_id && !$this->lora_hash_id){
                        throw new Exception("model_hash_id or lora_hash_id is required");
                    }
                }else{
                    $rules = $this->mergeData($rules, [
                        'cfg_scale'=>'required|numeric|min:0|max:1000',
                        'cref'=>'integer|min:0,max:100|nullable',
                        'sref'=>'integer|min:0,max:10|nullable',
                        'weird'=>'integer|min:0,max:3000|nullable',
                    ]);
                    
                }
                break;
            case 'PT':
                $rules = [
                    'engine' => ['required', Rule::in(GlobalCode::getEngingList())],
                    'init_img_path' => 'required', //file base64
                    'crop_face' => [Rule::in([CON_YES, CON_NO])],
                ];
                break;
            case 'SR':
            case 'RBG':
                $rules = [
                    'engine' => ['required', Rule::in(GlobalCode::getEngingList())],
                    'init_img_path' => 'required',   //file base64
                ];
                break;
            case 'FS':
                $rules = [
                    'engine' => ['required', Rule::in(GlobalCode::getEngingList())],
                    'init_img_path' => 'required',   //file base64
                    'image_file2' => 'required'       //base64
                ];
                break;
            case 'profile':
                $rules = [
                    'engine' => ['required', Rule::in(GlobalCode::getEngingList())],
                    'init_img_path' => 'required',   //file base64
                    'tpl_list' => 'required'     
                ];
                break;
            case 'VD':
                $rules = [
                    'engine' => ['required', Rule::in(GlobalCode::getEngingList())],
                    'init_img_path' => 'required',   //file base64
                    'cfg_scale' => 'required'    
                ];
                break;
            case 'u1':
            case 'u2':
            case 'u3':
            case 'u4':
            case 'v1':
            case 'v2':
            case 'v3':
            case 'v4':
            case 'reroll':
            case 'reroll2':
            case 'us2':
            case 'us4':
            case 'vst':
            case 'vsu':
            case 'left':
            case 'right':
            case 'up':
            case 'down':
            case 'us2':
            case 'us4':
            case 'zo1':
            case 'zo2':
            case 'square':
                $rules = [
                    'id' => 'required',
                    'act' => 'required',
                ];
                break;
            //PT SR RBG FS
            default:
                throw new Exception("Act:{$this->act} is not found");
        }
        $validator = Validator::make(get_object_vars($this), $rules);
        if ($validator->fails()) {
            $errors = '';
            foreach ($validator->errors()->messages() as $key => $value) {
                foreach($value as $val){
                    $errors .= "[".$val."]";
                }
            }
            throw new Exception("Act:{$this->act}, check error:{$errors}");
        }

    }

    public function getModelVersion($baseModel){
        return $baseModel;
    }   

    public function initModelInfo($hashId, $engine){
        if(!$hashId) return;
        if($engine != GlobalCode::LC) return;
        $model = UnitModels::getModelInfo($hashId);
        if(!$model) return;
        $this->model_version = $this->getModelVersion($model->base_model);
        $this->base_size = $model->base_size;
        $this->model_name = $model->sd_name;
    }
    public function initLoraInfo($hashId){
        if(!$hashId) return;
        $model = UnitModels::getModelInfo($hashId);
        if(!$model) return;
        $this->lora_name = $model->sd_name;
        $this->lora_prompt = $model->prompt;
    }

    public function initPost(){
        if(!$this->file_type){
            if(in_array($this->act, ['RBG'])){
                $this->file_type = 'png';
            }else{
                $this->file_type = 'jpeg';
            }
        }
        if(!$this->big){
            $objects = Helper::getObjectName('user_res',$this->file_type,'storage');
            $this->big = $objects[0];
            $this->thumb = $objects[1];
        }
        if(!empty($this->init_img_path)){
            //data = [url,size,width,height,ext]
            $image = ImageService::makeImageUrl($this->init_img_path, $this->storage);
            if($image['code'] != GlobalCode::SUCCESS){
                throw new Exception("Process init_img_path error:{$image['msg']}");
            }
            $this->init_img_path = $image['data']['uri']; //image.data=[url,size,width,height,ext]
            $this->initImgBtyes = base64_decode($image['data']['content']);
            $this->file_size = $image['data']['size'];
            $this->generate_type = 'img2img';
        }
  
        //image_file2(base64格式 )
        if(isset($this->image_file2)){
            $image = ImageService::makeImageUrl($this->image_file2, $this->storage);
            if($image['code'] != GlobalCode::SUCCESS){
                throw new Exception("Process image_file2 error:{$image['msg']}");
            }
            $this->image_file2 = $image['data']['uri']; 
        }
        if(in_array($this->act, ['MK','generate', 'APT']) && in_array($this->engine, [GlobalCode::ATZ, GlobalCode::LC])){
            if(!$this->model_hash_id){
                if(in_array($this->model_version, CON_SDXL_TAGS)){
                    $this->model_name = CON_DEFAULT_MODELXL;
                    $this->model_version = 'presdxl';
                    $this->model_hash_id = CON_DEFAULT_MODELXL_ID;
                }else{
                    $this->model_name = CON_DEFAULT_MODEL15;
                    $this->model_version = 'pre1.5';
                    $this->model_hash_id = CON_DEFAULT_MODEL15_ID;
                }
            }
            //pixart lcm 
            if(CON_PIXART_MODEL_NAME == $this->model_name){
                $steps = intval(Helper::getBenchParams(50, 8, $this->steps));
                $this->steps = $steps > 0 ? $steps : 1;
                $this->cfg_scale = 0.5;
            }
        }

        if(!in_array($this->model_version, CON_SDXL_TAGS)){
            $size = Helper::getLargestEightMultiple($this->width, $this->height , $this->base_size);
            $this->width  = $size['width'];
            $this->height = $size['height'];
            $this->upscale = $this->upscale == 0 ? 2 : $this->upscale;
        }

        if($this->act == 'SR'){
            $this->upscale = $this->upscale ? $this->upscale : 4;
            $this->face_enhance = 'yes';
        }
        return true;
    }

    public function package(array $data = []):array
    {
        $access_url = Common::getAccessUrl($this->storage);
        $this->generate_type = $this->init_img_path ? 'img2img' : 'txt2img';
        $post = [
            'id' => $this->id,
            'engine' => $this->engine,
            'prompt' => $this->prompt??'',
            'negative_prompt' => $this->negative_prompt,
            'prompt_en' => $this->prompt_en,
            'negative_prompt_en' => $this->negative_prompt_en,
            'width' => intval($this->width),
            'height' => intval($this->height),
            'seed' => intval($this->seed),
            'denoising_strength' => floatval($this->denoising_strength),
            'init_img_path' => $this->init_img_path,
            'file_size' => $this->file_size,
            'user_id' =>  $this->user_id,
            'task_id' =>  $this->task_id,
            'act' =>  $this->act,
            'job_type' => $this->act,
            'method' =>  $this->generate_type == 'txt2img'?1:2,
            'image_num'=>  intval($this->image_num),
            'steps' => $this->steps,
            'cfg_scale' => $this->cfg_scale,
            'image_file2' => $this->image_file2,
            'message_type' => $this->message_type,
            'storage' => $this->storage,
            'file_type' => $this->file_type,
            'float16' => $this->float16,
            'big' => $this->big,
            'thumb' => $this->thumb,
            'access_url' => $access_url,
            'reference_id' => $this->reference_id,
            
        ];
        // if($this->init_img_path){
        //     $post['init_img_path'] = $access_url.$this->init_img_path;
        // }
        // if($this->image_file2){
        //     $post['image_file2'] = $access_url.$this->image_file2;
        // }
        switch($this->act){
            case 'MK':
            case 'APT':
            case 'generate':
                if($this->engine == GlobalCode::ATZ || $this->engine == GlobalCode::LC){
                    $post = $this->mergeData($post, [
                        'upscale' => intval($this->upscale),
                        'face_enhance' => $this->face_enhance,
                        'arch' => $this->face_arch,
                        'mode' => $this->face_mode,
                        'enable_freeu' => $this->enable_freeu,//yes or no
                        'scheduler' => $this->sampler_name,
                        'sampler_name' => $this->sampler_name,
                        'num_inference_steps' => intval($this->steps),
                        'guidance_scale' => floatval($this->cfg_scale),
                        'model_name' => $this->model_name,//
                        'model_version' => $this->model_version,
                        'lora_name' => $this->lora_name,
                        'lora_scale' => floatval($this->lora_scale),
                        'lora_prompt' =>  $this->lora_prompt,
                        'model_hash_id' =>  $this->model_hash_id,
                        'lora_hash_id' =>  $this->lora_hash_id,
                    ]);
                }elseif($this->engine == GlobalCode::MJ){
                    $post = $this->mergeData($post, [
                        'weird' => intval($this->weird),
                        'cref' => intval($this->cref),
                        'sref' => intval($this->sref),
                        'steps' => intval($this->steps),
                        'niji' => $this->niji,
                        'cfg_scale' => floatval($this->cfg_scale),
                        'asp_id' => $this->asp_id,
                    ]);
                }elseif($this->engine == GlobalCode::SD){
                    $post = $this->mergeData($post, [
                        'sampler_name' => $this->sampler_name,
                        'batch_size' => 1,
                        'steps' => intval($this->steps),
                        'cfg_scale' => intval($this->cfg_scale),
                        'lora_name' => $this->lora_name,
                        'lora_scale' => floatval($this->lora_scale),
                        'lora_prompt' =>  $this->lora_prompt,
                        'model_hash_id' =>  $this->model_hash_id,
                        'lora_hash_id' =>  $this->lora_hash_id,
                        'asp_id' => $this->asp_id,
                    ]);
                }
                break;
            case 'PT':
                $post = $this->mergeData($post, [
                    'crop_face' => $this->crop_face,
                ]);
                break;
            case 'SR':
                $post = $this->mergeData($post, [
                    'face_enhance' => $this->face_enhance,
                    'upscale' => intval($this->upscale),
                ]);
                break;
            case 'RBG':
                break;
            case 'FS':
                $mergeArr = [];
                if($this->image_file2){
                    $mergeArr['tpl_list'] = [$this->image_file2];
                }
                $post = $this->mergeData($post, $mergeArr);
                break;
            case 'profile':
                $post = $this->mergeData($post, [
                    'tpl_list' => $this->tpl_list,     
                    // 'face_enhance' => $this->face_enhance,
                ]);
                break;
        }
        foreach($data as $key => $val){
            $post[$key] = $val;
        }
        ksort($post);
        return $post;
    }

    private function mergeData(array $post , array $merge):array
    {
        foreach($merge as $key => $val){
            if($val !== null){
                $post[$key] = $val;
            }
        }
        return $post;
    }

    public function splitPackage($package , $number = 2){
        if(!isset($package['image_num'])){
            return [$package];
        }
        $image_num = intval($package['image_num']);
        if($image_num <= $number){
            return [$package];
        }
        $list = [];
        $batch  = ceil($image_num / $number);
        // echo "batch = ". $batch."\n";
        for($i = 1; $i <= $batch; $i++){
            $objects = Helper::getObjectName('user-res',$package['file_type'],'storage');
            if($number * $i >= $image_num){
                $number = $image_num - $number * ($i - 1);
            }
            $package['image_num'] = $number;
            $package['big'] = $objects[0];
            $package['thumb'] = $objects[1];
            $list[] = $package;
        }
        return $list;
    }
    
}
