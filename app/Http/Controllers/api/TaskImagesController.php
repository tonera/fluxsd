<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskImageCollection;
use App\Models\TaskImage;
use App\Services\Common;
use App\Support\CUrl;
use App\Support\GlobalCode;
use App\Support\Helper;
use App\Support\UnitStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskImagesController extends Controller
{
    /**
     * Display a list of the resource.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $list = TaskImage::where('user_id', $userId)->orderBy("created_at","desc")->paginate(30);
        //buttonGroups
        $list->each(function ( $item, $key) {
            if($item->task){
                return $item->buttonGroups = Helper::getButtons($item->task->toArray());
            }
        });
        return (new TaskImageCollection($list))
            ->additional(['code' => GlobalCode::SUCCESS]);
    }

    public function show(TaskImage $image){
        return 'bbbb387119c8a1f89aeac8093c13582c';
    }

    //works index
    public function works(Request $request){
        $page = (int)$request->page??1;
        $page = $page < 1 ? 1 : $page ;
        $limit = 100;
        $offset = ($page-1) * $limit;
      
        $uidList = [Auth::id()];
        $total = TaskImage::whereIn('user_id', $uidList )->count();
        $totalPage = ceil($total/$limit);
    
        $list = DB::select('select a.id, a.task_id,a.thumb, a.show_url, b.storage, a.show_status, b.user_id, 
        b.model_name,b.prompt, b.prompt_en, b.negative_prompt, b.negative_prompt_en,b.act,b.steps, b.seed, b.cfg_scale,
        b.width, b.height,b.denoising_strength, b.created_at, b.style_id, b.work_status, b.engine 
            from task_images a 
            left join tasks b on a.task_id = b.task_id 
            where a.user_id in ('.implode(',', $uidList).') and a.deleted_at is null
            order by a.id desc limit ?,'.$limit, [intval($offset)]);
        
        
        
        $retd = [];
        //group by date
        foreach($list as $item){
            $access_url = Common::getAccessUrl($item->storage);
            $item->show_url = $access_url.$item->show_url;
            $item->thumb = $access_url.$item->thumb;
            $cDate = substr($item->created_at,0,10);
            if(isset($retd[$cDate])){
                $retd[$cDate]['images'][] = $item;
            }else{
                $retd[$cDate] = [
                    'cover_url' => $item->show_url,
                    'created_at' => $item->created_at,
                    'user_id' => $item->user_id,
                    'label' => $cDate,
                    'images' => [],
                ];
                $retd[$cDate]['images'][] = $item;
            }
        }
        $meta = [
            'last_page' => $totalPage ,
            'current_page' => $page,
            'total' => $total,
            'per_page' => $limit,
        ];
   
        return ['code'=>GlobalCode::SUCCESS ,'msg'=>'', 'meta' => $meta, 'data' => array_values($retd)];
    }

    public function destroy(TaskImage $image){
        $activeConfig = Common::getOnlyActiveConfig('storage');
        $config = [
            'storage' => $activeConfig['indexKey'],
            'access_key' => $activeConfig['access_key']??'', 
            'access_secret' => $activeConfig['secret_key']??'', 
            'bucket' => $activeConfig['bucket']??'', 
            'endpoint' => $activeConfig['endpoint']??'', 
            'region' => $activeConfig['region']??'', 
        ];
        $storage = new UnitStorage($config);
        $storage->deleteObject($image->show_url);
        $storage->deleteObject($image->thumb);
        $image->delete();
        return ['code' => GlobalCode::SUCCESS, 'data' => $image];
    }

    //convert to base64 from url
    public function convert(Request $request){
        if(isset($request->url)){
            $content = CUrl::get($request->url);
            return 'data:image/png;base64,'.base64_encode($content);
        }else{
            return '';
        }
    }

}
