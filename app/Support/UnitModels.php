<?php
namespace App\Support;

use App\Models\AiModel;
use Exception;
use Illuminate\Support\Facades\Cache;

class UnitModels{
    public static function getModelInfo(string $hashId):?object
    {
        $key = 'tuse_model_'.$hashId;
        $string = Cache::get($key);
        if($string){
            // echo "from cache\n";
            $data = json_decode($string);
        }else{
            $data = AiModel::where('hash', $hashId)->first();
            if(!$data){
                throw new Exception("Model:{$hashId} not found");
            }
            $string = json_encode($data);
            Cache::set($key, $string );
        }
        return $data;
    }
}
