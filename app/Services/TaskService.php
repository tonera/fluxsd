<?php
namespace App\Services;

use App\Models\Task;
use App\Models\TaskImage;
use App\Support\GlobalCode;
use Exception;

class TaskService{
    /**
     */
    public static function getImageByNumer(Task $task, $number): TaskImage
    {
        $list = TaskImage::where('task_id',$task->task_id)->get();
        if($number > count($list) || $number < 1){
            throw new Exception("Image not found");
        }
        $index = $number - 1;
        return $list[$index];
    }

    public static function createTask(array $params){
        $params['idx_prompt'] = md5($params['prompt_en']??'');
        $params['work_status'] = GlobalCode::TASK_CREATE;
        // $params['task_pkg'] = json_encode($params);
      
        $exTask = Task::where('task_id', $params['task_id'])->first();
        if($exTask != null){
            return $exTask;
        }
        
        $task = Task::create($params);
        return $task;
    }
}

