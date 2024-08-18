<?php
namespace App\Services;
use App\Jobs\AtzProgress;
use App\Support\Alogd;
use App\Support\GlobalCode;
use App\Support\UnitRequest;
use Exception;


class AtzTask{
    public static function create(UnitRequest $req):array
    {
        $req->initPost();
        $input = $req->package();
        $extraKeys = ['upscale', 'enable_freeu', 'face_enhance','lora_scale','crop_face','big', 'thumb','file_type','generate_type'
        ,'file_size','model_version','float16','image_num','job_type'];
        $task_pkg = [];
        foreach($extraKeys as $key){
            if(isset($input[$key]) && $input[$key] !== null){
                $task_pkg[$key] = $input[$key];
            }
        }
        $input['task_pkg'] = json_encode($task_pkg);
        
        Alogd::write("AtzTask::create", "ATZ绘画开始,创建任务:{$input['task_id']} {$input['act']}");

        try{
            $task = TaskService::createTask($input);
            AtzProgress::dispatch($task);
            return ['code' => GlobalCode::SUCCESS, 'data' => $task];

        }catch(Exception $e){
            return ['code' => 1009, 'msg' => __("创建任务失败")];
        }
  
    }

    

}
?>