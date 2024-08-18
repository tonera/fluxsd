<?php
return [
    'last_task_used_time_key' => 'atz_ltut_',//任务耗时秒数
    'aires_key' => 'atz_res',//各ai引擎返回响应的缓存结果，用来给前端重新连接后马上返回结果的cache(['key' => 'value'], now()->addMinutes(10));
    'partjob_status_key' => 'pj_status',
];
?>