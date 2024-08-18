<?php
//配置文件key 结构定义
return [
    'storage' => [
        'oss' => ['access_key' => '', 'secret_key' => '', 'endpoint' => 'oss-cn-hangzhou.aliyuncs.com', 'bucket' => '', 'access_url' => '','is_active'=>0,'label' => 'storage_oss_label'],
        's3' => ['access_key' => '', 'secret_key' => '', 'region' => '', 'bucket' => '', 'access_url' => '','is_active'=>0,'label' => 'storage_s3_label'],
        'local' => ['access_url' => '','is_active'=>1, 'label' => 'storage_lc_label'],
    ],
    'engine' => [
        'lc' => [
            'is_active'=>1,
            'label' => 'engine_lc_label',
            'client_path' => '',
        ],
        'atz' => ['token' => '','is_active'=>0,
            'label' => 'engine_atz_label',
            'apply_url' => 'https://fluxsd.com'],
        'sd' => ['token' => '','is_active'=>0,'label' => 'engine_sd_label','apply_url' => 'http://www.stability.ai'],
        'mj' => [
            'token' => '','is_active'=>0,
            'channel_id'=>'','guild_id'=>'','user_id'=>'','app_id'=>'','label' => 'engine_mj_label','apply_url' => 'http://www.discord.com'
            ],
    ],
    'translate' => [
        'google' => [
            'access_key' => '',
            'is_active'=>0,
            'label' => 'translate_google_label',
        ],
        'aliyun' => [
            'access_key' => '', 'secret_key' => '', 
            'is_active'=>0,
            'label' => 'translate_aliyun_label'
        ],
    ],
    'text' => [
        'together' => [
            'token' => '','is_active'=>0,'label' => 'text_together_label','apply_url' => 'https://www.together.ai'
        ],
        'qianwen' => [
            'token' => '','is_active'=>0,'label' => 'text_qianwen_label','apply_url' => 'https://www.aliyun.com'
        ],
    ],
    
];
?>