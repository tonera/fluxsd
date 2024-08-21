<?php
namespace App\Services;

use App\Support\Alogd;
use App\Support\GlobalCode;
use App\Support\UnitRequest;
use Exception;
use Illuminate\Support\Facades\Redis;


class LocalTask{
    public static function create(UnitRequest $req):array
    {
        $req->initPost();
        $input = $req->package();
        Alogd::write("LocalTask::create", "Create local task:{$input['task_id']} reference img={$input['init_img_path']}");
       
        try{
            $task = TaskService::createTask($input);
            if($input['storage'] == 'local'){
                $input['storage'] = 'server';
            }
            $input['id'] = $task->id;
            $redis = Redis::connection();
            $channel = GlobalCode::CHANNEL_LIST[$input['job_type']];

            $list = $req->splitPackage($input, 1);
            foreach($list as $idx => $val){
                if($input['image_num'] > 1){
                    $val['seed'] = rand(1,4294967295);
                }
                $qStat = $redis->lpush($channel , json_encode($val));
                Alogd::write("LocalTask::create", "lpush to redis:task_id={$input['task_id']}->{$idx} .LocalEngineListener wait to AI Client's response.");
            }

            if($qStat){
                $task->work_status = GlobalCode::TASK_QUEUED;
                $task->save();
                return [
                    'code' => GlobalCode::SUCCESS, 
                    'data' => $task, 
                    'msg' => "Queue:".$qStat,
                ];
            }else{
                return ['code' => 1020, 'msg' => 'Can not start to generate.Queue is shutdown'];
            }
        }catch(Exception $e){
            Alogd::write(GlobalCode::LC, 'error:msg='.$e->getMessage());
            return ['code'=>1009, 'msg'=>__('Operation failed:').$e->getMessage()];
        }

    }

}
