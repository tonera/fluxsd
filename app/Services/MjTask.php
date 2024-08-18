<?php
namespace App\Services;

use App\Models\TaskImage;
use App\Support\Alogd;
use App\Support\GlobalCode;
use App\Support\IDGenerator;
use App\Support\UnitRequest;
use Exception;
use Illuminate\Support\Facades\Auth;

class MjTask{
    public static function create(UnitRequest $req):array {
        $req->initPost();
        $input = $req->package();
        $input['task_pkg'] = json_encode($input);
        Alogd::write(GlobalCode::MJ, "MJ任务开始,创建任务:{$input['task_id']}");

        $service = new DiscordService();
        
        list($imgString, $formatPrompt) = $service->formatPrompt($input['prompt_en'], $input);
        Alogd::write(GlobalCode::MJ,'格式化 formatPrompt='.$formatPrompt );
        
        $cleanPrompt = $service->clearParams($formatPrompt);
        
        Alogd::write(GlobalCode::MJ, "清洁化 cleanPrompt={$cleanPrompt} imgString = {$imgString}");
        $input['prompt_en'] = $cleanPrompt;
        $task = TaskService::createTask($input);
        $discordPrompt = '';
        try{
            if(empty($imgString)){
                $discordPrompt = $input['prompt_en'];
            }else{
                if(!isset($input['cref']) && !isset($input['sref'])){
                    $discordPrompt = $imgString.' '.$input['prompt_en'];
                }else{
                    $discordPrompt .= $input['prompt_en'];
                    if(isset($input['cref'])){
                        $discordPrompt .= ' --cref '.$imgString.' --cw '.$input['cref'];
                    }
                    if(isset($input['sref'])){
                        $discordPrompt .= ' --sref '.$imgString.' --sw '.$input['sref'];
                    }
                }
            }
            // $discordPrompt = $imgString == ''?$input['prompt_en'] : $imgString.' '.$input['prompt_en'];
            Alogd::write(GlobalCode::MJ,'调用MJ接口, Prompt='.$discordPrompt );
           
           
            $service->generate($discordPrompt);
            return ['code' => GlobalCode::SUCCESS, 'data' => $task];
        }catch(Exception $e){
            $task->work_status = -1;//AI生成失败
            $task->save();
            Alogd::write(GlobalCode::MJ, '调用生成MJ-AI绘画处理失败:msg='.$e->getMessage() .' prompt='.$input['prompt_en']);
            return ['code'=>1009, 'msg'=>'MJ-AI创作失败,请检查是否有敏感提示词'];
        }
    }

    /**
     *
     * @param string $act
     * @param array $userId
     * @param TaskImage $taskImage
     * @return array
     */
    public static function upscale(string $act, array $input , TaskImage $taskImage){
        $input['task_id'] = $input['task_id'] ?? IDGenerator::id();
        Alogd::write(GlobalCode::MJ, "MJ-upscale任务 {$act} 开始,创建任务:{$input['task_id']}");
        $input['width'] = $taskImage->task->width;
        $input['height'] = $taskImage->task->height;
        $input['prompt'] = $taskImage->task->prompt;
        $input['prompt_en'] = $taskImage->task->prompt_en;
        $input['method'] = 2;
        $task = TaskService::createTask($input);
        try{
            $service = new DiscordService();
            $package = json_decode($taskImage->task->task_pkg);
            $service->change($package, $act);
            return ['code' => GlobalCode::SUCCESS, 'data' => $task];
        }catch(Exception $e){
            Alogd::write(GlobalCode::MJ, 'MJ提升图片处理失败, act='.$act .' '.$e->getMessage());
            $task->work_status = -1;//AI生成失败
            $task->save();
            return ['code'=>1009, 'msg'=>'MJ-AI调用接口生成图片失败'];
        }
    }
}
?>