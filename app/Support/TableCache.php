<?php
namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TableCache{
    public static $cacheRowsLimit = 100;
    
    public static function getField(string $table, string $field , int $cacheTime = 86400, string $whereRaw = ''){
        $cacheKey = md5("tc_".$table.$field.$whereRaw);
        $package = Cache::get($cacheKey);
        if($package){
            $arr = json_decode($package, true);
            // echo "read from cache\n";
        }else{
            $query = DB::table($table)->select(DB::raw($field));
            if($whereRaw){
                $query = $query->whereRaw($whereRaw);
            }
            $list = $query->get();
            $arr = $list->pluck($field);
            if(count($arr) <= self::$cacheRowsLimit){
                Cache::put($cacheKey , json_encode($arr), $cacheTime);
            }
            // echo "read from db\n";
        }
        return $arr;
    }

    public static function getGroupField(string $table, string $field , int $cacheTime = 86400, string $whereRaw = ''){
        $cacheKey = md5("tcg_".$table.$field.$whereRaw);
        $package = Cache::get($cacheKey);
        if($package){
            $arr = json_decode($package, true);
            // echo "read from cache\n";
        }else{
            $query = DB::table($table)->select(DB::raw('count(*) as total, '.$field));
            if($whereRaw){
                $query = $query->whereRaw($whereRaw);
            }
            $query = $query->groupBy($field);
            $list = $query->get();
            $arr = [];
            foreach($list as $item){
                $arr[$item->$field] = $item->total;
            }
            if(count($arr) <= self::$cacheRowsLimit){
                Cache::put($cacheKey , json_encode($arr), $cacheTime);
            }
            // echo "read from db\n";
        }
        return $arr;
    }

}

