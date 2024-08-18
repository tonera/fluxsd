<?php

//sd xl模型允许的上传图片尺寸'for stable-diffusion-xl-1024-v0-9 and stable-diffusion-xl-1024-v1-0 the allowed dimensions are 1024x1024, 1152x896, 1216x832, 1344x768, 1536x640, 640x1536, 768x1344, 832x1216, 896x1152, but we received 450x672',

return [
    'sdratio' => [
        [
            'label' => '1:1', 'width' => 1024, 'height' => 1024,
        ],
        [
            'label' => '9:7', 'width' => 1152, 'height' => 896,
        ],
        [
            'label' => '19:13', 'width' => 1216, 'height' => 832,//1216x832
        ],
        [
            'label' => '7:4', 'width' => 1344, 'height' => 768,//1344x768
        ],
        [
            'label' => '12:5', 'width' => 1536, 'height' => 640,//1536x640
        ],
        [
            'label' => '5:12', 'width' => 640, 'height' => 1536,//640x1536
        ],
        [
            'label' => '4:7', 'width' => 768, 'height' => 1344,//768x1344
        ],
        [
            'label' => '13:19', 'width' => 832, 'height' => 1216,//832x1216
        ],
        [
            'label' => '7:9', 'width' => 896, 'height' => 1152,//896x1152
        ],
    ],
    'omratio' => [
        [
            'label' => '1:1','width' => 1024,'height' => 1024,
        ],
        [
            'label' => '9:16','width' => 576, 'height' => 1024,
        ],
        [
            'label' => '16:9','width' => 1024,'height' => 576,
        ],
        [
            'label' => '4:3','width' => 1024,'height' => 768,
        ],
        [
            'label' => '3:2','width' => 1024,'height' => 680,
        ],
        [
            'label' => '3:4','width' => 680,'height' => 1024,
        ],
        [
            'label' => '2:1','width' => 1024,'height' => 512,
        ],
        [
            'label' => '1:2','width' => 512,'height' => 1024,
        ],
    ],
];


