<?php
namespace App\Services;

use App\Support\Alogd;
use App\Support\GlobalCode;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Exception\RequestException;

/**
 * Stability
 */

class StabilityService {
    public static $apiHost = 'https://api.stability.ai/v1';
    public static $paramsConfig = [
        'masking' => [
            'prompt_en' => 'required',//text_prompts
            'init_image' => 'required',
            'mask_source' => 'required',//MASK_IMAGE_BLACK /MASK_IMAGE_WHITE/INIT_IMAGE_ALPHA
            'mask_image' => 'required',

            'cfg_scale' => 'optional',
            'clip_guidance_preset',
            'sampler' => 'optional',
            'samples' => 'optional',
            'seed' => 'optional',
            'steps' => 'optional',
            'style_preset' => 'optional',
        ],
        'upscale' => [
            'init_image' => 'required',//映射image

            'width' => 'optional',
            'height' => 'optional',
            'prompt_en' => 'optional',//text_prompts
            'seed' => 'optional',
            'steps' => 'optional',
            'cfg_scale' => 'optional',
        ],
        'img2img' => [
            'prompt_en' => 'required',//text_prompts
            'init_image' => 'required',

            'init_image_mode' => 'optional',
            'image_strength' => 'optional',
            'cfg_scale' => 'optional',
            'clip_guidance_preset' => 'optional',
            'sampler' => 'optional',
            'samples' => 'optional',
            'seed' => 'optional',
            'steps' => 'optional',
            'style_preset' => 'optional',
            'step_schedule_start' => 'optional',
            'step_schedule_end' => 'optional',
        ], 
        'txt2img' => [
            'prompt_en'=> 'required',//text_prompts

            'width' => 'optional',
            'height' => 'optional',
            'cfg_scale' => 'optional',
            'clip_guidance_preset' => 'optional',
            'sampler' => 'optional',
            'samples' => 'optional',
            'seed' => 'optional',
            'steps' => 'optional',
            'style_preset' => 'optional',
        ],
    ];

    //List all engines available to your organization/user
    public static function list(){
        $apiHost = self::$apiHost .'/engines/list';
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiHost,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                'Authorization: Bearer '. Common::getConfigKeyValue('engine.sd.token'),
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return self::formatOutput($response , $err);
    }

    /**
     * @param string $engine [esrgan-v1-x2plus,stable-diffusion-xl-1024-v0-9,stable-diffusion-xl-1024-v1-0,stable-diffusion-v1-6,stable-diffusion-xl-beta-v2-2-2]
     * @param array $params
     * @return array
     */
    public static function txt2img(string $engine, array $params){
        
        $engine = empty($engine)?'stable-diffusion-xl-1024-v1-0':$engine;
        $apiHost = self::$apiHost .'/generation/'.$engine.'/text-to-image';
        
        $arr = self::formatParams($params, 'txt2img');
        
        $arr['text_prompts'] = [
            [
                'text' => $arr['text_prompts[0][text]'],
                'weight' => $params['lora_scale']??1,
            ]
        ];
        unset($arr['text_prompts[0][text]']);
        $payload = json_encode($arr);
        
        // Alogd::write(GlobalCode::SD, 'payload='.$payload);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiHost,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Content-Type: application/json",
                'Authorization: Bearer '.Common::getConfigKeyValue('engine.sd.token'),
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return self::formatOutput($response , $err);
    }

    /**
     * @param string $engine
     * @param [type] $params
     * @return array
     */
    static function img2img(string $engine, $params){
        //stable-diffusion-v1-5
        $engine = empty($engine)?'stable-diffusion-xl-1024-v1-0':$engine;
        $apiHost = self::$apiHost .'/generation/'.$engine.'/image-to-image';
        $formData = (self::formatParams($params, 'img2img'));
  
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiHost,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $formData,
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Content-Type: multipart/form-data",
                'Authorization: Bearer '.Common::getConfigKeyValue('engine.sd.token'),
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return self::formatOutput($response , $err);
    }

    /**
     * @param string $engine
     * @param array $params
     * @return array
     */
    public static function upscale(string $engine, array $params){
        $engine = empty($engine)?'esrgan-v1-x2plus':$engine;
        $apiHost = self::$apiHost .'/generation/'.$engine.'/image-to-image/upscale';
        $formData = self::formatParams($params, 'upscale');
        $formData['image'] = $formData['init_image'];
        unset($formData['init_image']);

        list($width,$height) = getimagesizefromstring($formData['image']);
        
        // if(isset($formData['width'])){
        //     $formData['width'] = $formData['width'];//输出宽度，只能指出宽或高度之一
        // }else{
        //     if(isset($formData['height'])) $formData['height'] = $formData['height'];//输出宽度，只能指出宽或高度之一
        // }
        if($engine === 'stable-diffusion-x4-latent-upscaler'){
            if(($width * $height) > 1048576 ){
                throw new Exception('The image size is too large, should be no larger than 1024 x 1024',1000);
            }
        }else{
            if(($width * $height) > 393216 ){
                throw new Exception('The image size is too large, should be no larger than 512 x 768',1000);
            }
            //keey key='image' => 'required',//映射image'width' => 'optional','height' => 'optional',
            $keepKeys = ['image'];
            foreach($formData as $key => $val){
                if(!in_array($key, $keepKeys)){
                    unset($formData[$key]);
                }
            }
        }
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiHost,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $formData,
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Content-Type: multipart/form-data",
                'Authorization: Bearer '.Common::getConfigKeyValue('engine.sd.token'),
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return self::formatOutput($response , $err);
    }

    /**
     * @param string $engine:stable-inpainting-512-v2-0
     * @param array $params
     * @return array
     * https://api.stability.ai/v1/generation/stable-inpainting-512-v2-0/image-to-image/masking
     * https://api.stability.ai/v1/generation/stable-diffusion-xl-1024-v1-0/image-to-image/masking
     * stable-diffusion-v1-6
     * 
     */
    public static function masking(string $engine, array $params){
        $engine = empty($engine)?'stable-diffusion-xl-1024-v1-0':$engine;
        $apiHost = self::$apiHost .'/generation/'.$engine.'/image-to-image/masking';
        $formData = (self::formatParams($params, 'masking'));

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiHost,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $formData,
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Content-Type: multipart/form-data",
                'Authorization: Bearer '.Common::getConfigKeyValue('engine.sd.token'),
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return self::formatOutput($response , $err);
    }

    public static function formatOutput(string $response , string $err){
        if(empty($response)){
            return ['code' => 1009, 'msg' => $err];
        }
        $res = json_decode($response, true);
        if(isset($res['id']) && isset($res['message'])){
            return ['code' => 1009, 'msg' => $res['message']];
        }
        if(isset($res['artifacts'])){
            return ['code' => GlobalCode::SUCCESS , 'data'=>$res['artifacts'][0]];
        }else{
            return ['code' => GlobalCode::SUCCESS , 'data'=>$res];
        }
    }


    public static function formatParams($params, $operation){
        
        if(isset($params['sampler_name']) && $params['sampler_name'] == '默认' ) unset($params['sampler_name']);

        $returnParams = [];
        foreach(self::$paramsConfig[$operation] as $key => $val){
            if( $val === 'required' && !isset($params[$key])){
                throw new Exception("必选参数缺失:{$key}", 10010);
            }
            if(isset($params[$key])) $returnParams[$key] = $params[$key];
        }
        
       

        if(isset($returnParams['prompt_en'])){
            $returnParams['text_prompts[0][text]'] = $returnParams['prompt_en'];
        }
        if(isset($returnParams['prompt_weight'])){
            $returnParams['text_prompts[0][weight]'] = floatval($returnParams['prompt_weight']);
        }
        if(isset($returnParams['negative_prompt_en'])){
            $returnParams['text_prompts[1][text]'] = $returnParams['negative_prompt_en'];
            $returnParams['text_prompts[1][weight]'] = -1;
        }
        unset($returnParams['prompt_en']);

        $dd = [
            'prompt_en' => 'required',//text_prompts
            'init_image' => 'required',

            'init_image_mode' => 'optional',
            'image_strength' => 'optional',
            'cfg_scale' => 'optional',
            'clip_guidance_preset' => 'optional',
            'sampler' => 'optional',
            'samples' => 'optional',
            'seed' => 'optional',
            'steps' => 'optional',
            'style_preset' => 'optional',
            'step_schedule_start' => 'optional',
            'step_schedule_end' => 'optional',
        ];
        $intParamKeys = ['cfg_scale','seed','width','height','steps'];
        $floatParamKeys = ['step_schedule_start','step_schedule_end'];
        foreach($returnParams as $key => $value){
            if(in_array($key, $intParamKeys)){
                $returnParams[$key] = intval($returnParams[$key]);
            }elseif(in_array($key, $floatParamKeys)){
                $returnParams[$key] = floatval($returnParams[$key]);
            }
        }
        
        $rules = [
            'cfg_scale' => 'integer|min:0|max:35',
            'clip_guidance_preset' => Rule::in(['FAST_BLUE' , 'FAST_GREEN' , 'NONE' , 'SIMPLE' , 'SLOW' , 'SLOWER' , 'SLOWEST']),
            'sampler' => Rule::in(['DDIM' , 'DDPM' , 'K_DPMPP_2M' , 'K_DPMPP_2S_ANCESTRAL' , 'K_DPM_2' , 'K_DPM_2_ANCESTRAL' , 'K_EULER' , 'K_EULER_ANCESTRAL' , 'K_HEUN' , 'K_LMS']),//
            'style_preset' => Rule::in(['3d-model', 'analog-film', 'anime', 'cinematic', 'comic-book', 'digital-art' ,'enhance' , 'fantasy-art', 'isometric', 'line-art' ,'low-poly' , 'modeling-compound' ,'neon-punk', 'origami' , 'photographic' ,'pixel-art', 'tile-texture']),
            'seed' => 'integer|min:0|max:4294967295',
            'samples' => 'integer|min:1|max:10',
            'image_strength' => 'numeric|min:0|max:1',
            'init_image_mode' => Rule::in(['IMAGE_STRENGTH', 'STEP_SCHEDULE']),
            //For SDXL v0.9: valid dimensions are 1024x1024, 1152x896, 1216x832, 1344x768, 1536x640, 640x1536, 768x1344, 832x1216, or 896x1152 For SDXL v1.0: valid dimensions are the same as SDXL v0.9
            // 'width' => 'integer|min:1|max:10',
            // 'height' => 'integer|min:1|max:10',
        ];
        $messages = [
            'required' => ':attribute is required.',
            'numeric' => ':attribute must be numeric',
            'integer' => ':attribute must be int',
            'between' => 'The :attribute value :input is not between :min - :max.',
            'in' => ":attribute not in the list",
        ];
        $validator = Validator::make($returnParams, $rules, $messages);
        if($validator->fails()) {
            
            throw new Exception(json_encode($validator->errors()->toArray()), 10010);
        }
        
        return $returnParams;

    }

    public static function image2video(array $input){
        $url = 'https://api.stability.ai/v2beta/image-to-video';
        $client = new Client();
        $body = new MultipartStream([
            [
                'name' => 'image',
                'contents' => fopen($input['init_img_path'], 'r'),
                'filename' => basename($input['init_img_path']),
                'headers' => [
                    'Content-Type' => 'image/jpeg', // Change this to the appropriate MIME type
                ],
            ],
            [
                'name' => 'cfg_scale',
                'contents' => $input['cfg_scale'],
            ],
            [
                'name' => 'motion_bucket_id',
                'contents' => $input['motion_bucket_id'],
            ],
            [
                'name' => 'seed',
                'contents' => $input['seed']??0,
            ],
        ]);
        $headers = [
            'Content-Type' => 'multipart/form-data; boundary=' . $body->getBoundary(),
            'authorization' =>  'Bearer '.Common::getConfigKeyValue('engine.sd.token'),
        ];
        try {
            $response = $client->post($url, ['headers' => $headers, 'body' => $body, 'debug' => false]);
            // Check the response status code
            if ($response->getStatusCode() == 200) {
                $res = $response->getBody()->getContents();
                return $res;
            } else {
                // echo 'Failed to upload file.';
                throw new Exception('Failed to generate video.');
            }
        } catch (RequestException $e) {
            // echo "Error: " . $e->getMessage();
            throw new Exception($e->getResponse()->getReasonPhrase());
            // var_dump($e->getResponse()->getReasonPhrase());
            
        }

    }

    /**
     * 3d
     * @param string $engine
     * @param [type] $params
     * @return array
     */
    static function img23d($input){
        $url = 'https://api.stability.ai/v2beta/3d/stable-fast-3d';
        $client = new Client();
        $body = new MultipartStream([
            [
                'name' => 'image',
                'contents' => fopen($input['init_img_path'], 'r'),
                'filename' => basename($input['init_img_path']),
                'headers' => [
                    'Content-Type' => 'image/jpeg', // Change this to the appropriate MIME type
                ],
            ],
        ]);
        $headers = [
            'Content-Type' => 'multipart/form-data; boundary=' . $body->getBoundary(),
            'authorization' =>  'Bearer '.Common::getConfigKeyValue('engine.sd.token'),
        ];
        try {
            $response = $client->post($url, ['headers' => $headers, 'body' => $body]);
            // Check the response status code
            if ($response->getStatusCode() == 200) {
                $res = $response->getBody()->getContents();
                return $res;
            } else {
                // echo 'Failed to upload file.';
                throw new Exception('Failed to generate video.');
            }
        } catch (RequestException $e) {
            // echo "Error: " . $e->getMessage();
            throw new Exception($e->getResponse()->getReasonPhrase());
            // var_dump($e->getResponse()->getReasonPhrase());
            
        }
    }

}

?>


