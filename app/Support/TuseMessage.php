<?php
//client's message process
namespace App\Support;

use App\Http\Resources\TaskImageResource;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskImage;
use Exception;

class TuseMessage{
    public $message;
    public $taskStatus = GlobalCode::TASK_QUEUED;

    public function __construct(array $message)
    {
        $this->message = $message;
    }

    public function validator(){
        $required = [];
        if(!isset($this->message['status'])){
            throw new Exception("Required fields are missing:status" , 9000);
        }
        switch($this->message['status']){
            case 'received':
                $required = ['id', 'job_type', 'message_type', 'client_id','user_id','status'];
                break;
            case 'progress':
                $required = ['id', 'job_type', 'message_type', 'client_id','user_id','status','thumb','client_id','user_id'];
                break;
            case 'failed':
                $required = ['id', 'job_type', 'message_type', 'client_id','user_id','status','msg'];
                break;
            case 'success':
                $required = ['id', 'job_type', 'message_type', 'width', 'height', 'size', 'big','thumb','client_id','user_id','status','used_time'];
                break;
            default:
                throw new Exception(__("Not found the status tag"), 9000);
                break;
        }
        foreach($required as $key){
            if(!isset($this->message[$key])){
                throw new Exception("Required fields are missing:{$key}" , 9000);
            }
        }
        return true;
    }

    //package the return message
    public function package(string $messageType , int $execTime, TaskImage $taskImage = null, Task $task = null):array
    {
        $package = [];
        switch($messageType){
            case 'ephemeral':
                //received
                if($this->message['status'] == 'received'){
                    $package = [
                        'message_type' => 'ephemeral',//ephemeral
                        'progress' => '30%',
                        'progress_label' => __('ET').$execTime, 
                        'execTime' => $execTime,
                    ];
                }else{
                    //progress
                    $package = [
                        'message_type' => 'ephemeral',//ephemeral
                        'progress' => '80%',
                        'progress_label' => __('ET').$execTime, 
                        'execTime' => $execTime,
                        'base64' => $this->message['base64']??null,
                    ];
                    if($package['base64']){
                        $package['show_url'] = $package['base64'];
                        $package['thumb'] = $package['base64'];
                    }
                }
                break;
            case 'failed':
                $package = [
                    'message_type' => 'failed',
                    'progress' => '0%',
                    'progress_label' => __('Generate failed'),
                    'msg' => $this->message['msg']??__('Generate failed'),
                ];
                $this->taskStatus = GlobalCode::TASK_FAILED;
                break;
            case 'standing':
                //根据图片返回数量判断状态
                $package = [
                    'message_type' => 'standing',
                    'progress' => '100%',
                    'progress_label' => __('Done'),
                    'execTime' => $execTime,
                ];
                $this->taskStatus = GlobalCode::TASK_SUCCESS;
                
                break;
            default:
                throw new Exception(__("Not found the status tag"), 9000);
                break;
        }
        $package['user_id'] = $this->message['user_id']??'';
        $package['task_id'] = $this->message['task_id']??'';

        if($taskImage !== null){
            $images = new TaskImageResource($taskImage);
            $data = json_decode($images->toJson(), true);
            $data['buttonGroups'] = Helper::getButtons($taskImage->task->toArray());
            return array_merge($package, $data);
        }else{
            if($task === null){
                $package['buttonGroups'] = [];
                return $package;
            }else{
                $tasks = new TaskResource($task);
                $data = json_decode($tasks->toJson(), true);
                $package['user_id'] = $task->user_id;
                $package['task_id'] = $task->task_id;
                $package['buttonGroups'] = [];
                $package['task'] = $data;
                return $package;
            }
        }
    }
}