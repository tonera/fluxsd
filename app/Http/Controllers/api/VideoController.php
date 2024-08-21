<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Jobs\SdVideoProgress;
use App\Services\SdTask;
use App\Services\StabilityService;
use App\Services\TaskService;
use App\Support\GlobalCode;
use App\Support\IDGenerator;
use App\Support\UnitRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    public function store(Request $request){
        $rules = [
            'init_img_path' => [
                'required',File::image(),
            ],
            'cfg_scale' => 'required|min:0|max:10',
            'motion_bucket_id' => 'required|min:1|max:255',
            'seed' => 'min:0|max:4294967294'
        ];

  
        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails()) {
            return ['code' => 10010, 'msg' => 'Params check failed'.json_encode($validator->errors()->messages())];
        }
        $input = $request->post();

        // $file = $request->file('init_img_path');
        $path = $request->init_img_path->path();
        $extension = $request->init_img_path->extension();
        $imageBytes = SdTask::formatImage(file_get_contents($path));

        $tempFile = tempnam(sys_get_temp_dir(), 'video_');
        $handle = fopen($tempFile, 'w+');
        // Write some data to the temporary file
        fwrite($handle, $imageBytes);
        fclose($handle);
        $input['init_img_path'] = $tempFile;
        try{
            $res = StabilityService::image2video($input);
            $json = json_decode($res, true);
            // Delete the temporary file
            unlink($tempFile);
            if(isset($json['id'])){
                //download video
                $req = new UnitRequest(array_merge($input, [
                    'act' => 'VD',
                    'engine' => 'sd',
                    'task_id' => IDGenerator::id(),
                    'user_id' => 2,
                ]));
                $taskData = $req->package(['request_id' => $json['id']]);
                $taskData['task_pkg'] = json_encode($input);
                $task = TaskService::createTask($taskData);       
                SdVideoProgress::dispatch($task)->delay(Carbon::now()->addSeconds(15));

                return ['code' => GlobalCode::SUCCESS, 'data' => $json['id']];
            }else{
                return ['code' => 555, 'msg' => __('Api error')];
            }
        }catch(Exception $e){
            return ['code' => 555, 'msg' => $e->getMessage()];
        }

    }
}
