<?php

//sd xl模型允许的上传图片尺寸'for stable-diffusion-xl-1024-v0-9 and stable-diffusion-xl-1024-v1-0 the allowed dimensions are 1024x1024, 1152x896, 1216x832, 1344x768, 1536x640, 640x1536, 768x1344, 832x1216, 896x1152, but we received 450x672', 1:1 5:4(1.25)  3:2(1.5) 16:9(1.78) 21:9(2.33)     9:16(0.56) 9:21(0.43)   2:3 4:5 

return [
    'sdratio' => [
        [
            'label' => '1:1', 'width' => 1024, 'height' => 1024,
        ],
        [
            'label' => '5:4', 'width' => 1152, 'height' => 896,//1.29 9:7
        ],
        [
            'label' => '3:2', 'width' => 1216, 'height' => 832,//1216x832  1.46 19:13
        ],
        [
            'label' => '16:9', 'width' => 1344, 'height' => 768,//1344x768 1.74 7:4
        ],
        [
            'label' => '21:9', 'width' => 1536, 'height' => 640,//1536x640 2.4 12:5
        ],
        [
            'label' => '9:21', 'width' => 640, 'height' => 1536,//640x1536
        ],
        [
            'label' => '9:16', 'width' => 768, 'height' => 1344,//768x1344
        ],
        [
            'label' => '2:3', 'width' => 832, 'height' => 1216,//832x1216
        ],
        [
            'label' => '4:5', 'width' => 896, 'height' => 1152,//896x1152
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


