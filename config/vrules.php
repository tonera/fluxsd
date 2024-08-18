<?php
use Illuminate\Validation\Rule;
//提交验证规则集合:引擎-> 操作 -> 规则
return [
    'om' => [
        'generate'=> [
            'prompt'=>'required|max:500',
            'cfg_scale'=>'required|numeric|min:0|max:30',
            'seed'=>'required|integer|min:-1|max:4294967295',
            'steps'=>'required|integer|min:1,max:50',
            'width'=>'required|integer|min:1,max:2048',
            'height'=>'required|integer|min:1,max:2048',
            'model_name' => 'required|string',
            'lora_name' => 'string',
            'negative_prompt'=>'max:500',
            // 'enable_freeu' => [Rule::in(['yes', 'no'])],
            'image_num'=>'integer|min:1,max:4',
            // 'niji' => 'string',
            // 'face_enhance' => 'string',
            // 'upscale' => [Rule::in(['2x', '4x'])],
            // 'weird' => 'integer|min:0,max:1000',
            // 'cref' => 'integer|min:0,max:100',
            // 'sref' => 'integer|min:0,max:10',
            'init_img_path' =>'file|string',
            'denoising_strength' =>  'exclude_unless:init_img_path,required|required|numeric|min:0,max:1',
            'sampler_name' => 'required|string',
        ],
    ],
    'lc' =>[
        'generate'=> [
            'prompt'=>'required|max:500',
            'cfg_scale'=>'required|numeric|min:0|max:30',
            'seed'=>'required|integer|min:-1|max:4294967295',
            'steps'=>'required|integer|min:1,max:50',
            'width'=>'required|integer|min:1,max:2048',
            'height'=>'required|integer|min:1,max:2048',
            'model_name' => 'required|string',
            'lora_name' => 'string',
            'negative_prompt'=>'max:500',
            'enable_freeu' => [Rule::in(['yes', 'no'])],
            'image_num'=>'integer|min:1,max:4',
            // 'niji' => 'string',
            'face_enhance' => 'string',
            'upscale' => [Rule::in(['2x', '4x'])],
            // 'weird' => 'integer|min:0,max:1000',
            // 'cref' => 'integer|min:0,max:100',
            // 'sref' => 'integer|min:0,max:10',
            'init_img_path' =>'file|string',
            'denoising_strength' =>  'exclude_unless:init_img_path,required|required|numeric|min:0,max:1',
        ],
        'faceswap' => [
            'init_img_path'=>'image_or_string',
            'tpl_list'=>'required',
            'face_enhance'=> [Rule::in(['yes','no'])],
        ],
        'removebg' => [
            'init_img_path'=>'image_or_string',
        ],
        'portrait' => [
            'init_img_path'=>'image_or_string',
            'crop_face'=> [Rule::in(['yes','no'])],
        ],
        'avatar' => [
            'init_img_path'=>'image_or_string',
            'image_num'=>'integer|min:1,max:4',
            'cfg_scale'=>'required|numeric|min:0|max:30',
        ],
        'upscale' => [
            'init_img_path'=>'image_or_string',
            'scale_factor'=> [Rule::in([2, 4])],
        ],
    ],
    'sd' => [
        'generate'=> [
            'prompt'=>'required|max:500',
            'cfg_scale'=>'required|numeric|min:0|max:30',
            'seed'=>'required|integer|min:-1|max:4294967295',
            'steps'=>'required|integer|min:1,max:50',
            'width'=>'required|integer|min:1,max:2048',
            'height'=>'required|integer|min:1,max:2048',
            // 'model_name' => 'required|string',
            'lora_name' => 'string',
            'negative_prompt'=>'max:500',
            // 'enable_freeu' => [Rule::in(['yes', 'no'])],
            'image_num'=>'integer|min:1,max:4',
            // 'niji' => 'string',
            // 'face_enhance' => 'string',
            // 'upscale' => [Rule::in(['2x', '4x'])],
            // 'weird' => 'integer|min:0,max:1000',
            // 'cref' => 'integer|min:0,max:100',
            // 'sref' => 'integer|min:0,max:10',
            'init_img_path' =>'file|string',
            'denoising_strength' =>  'exclude_unless:init_img_path,required|required|numeric|min:0,max:1',
        ],
    ],
    'mj' => [
        'generate'=> [
            'prompt'=>'required|max:500',
            'cfg_scale'=>'required|numeric|min:0|max:1000',
            'seed'=>'required|integer|min:-1|max:4294967295',
            'steps'=>'required|integer|min:1,max:50',
            'width'=>'required|integer|min:1,max:2048',
            'height'=>'required|integer|min:1,max:2048',
            // 'model_name' => 'required|string',
            // 'lora_name' => 'string',
            'negative_prompt'=>'max:500',
            // 'enable_freeu' => [Rule::in(['yes', 'no'])],
            'image_num'=>'integer|min:1,max:4',
            'niji' => 'string',
            // 'face_enhance' => 'string',
            // 'upscale' => [Rule::in(['2x', '4x'])],
            'weird' => 'integer|min:0,max:1000',
            'cref' => 'integer|min:0,max:100',
            'sref' => 'integer|min:0,max:10',
            'init_img_path' =>'file|string',
            'denoising_strength' =>  'exclude_unless:init_img_path,required|required|numeric|min:0,max:2',
        ],
    ],
];

