<?php

namespace App\Console\Commands;

use App\Models\AiModel;
use Illuminate\Console\Command;

class AddImageModel extends Command
{
    protected $signature = 'AddImageModel';

    protected $description = 'Add a new image model manually';

    //php artisan AddImageModel
    public function handle()
    {
        $version = 'presd3';
        $base_size = 1024;
        $sd_name = 'Stable Diffusion 3';
        $hash = md5($sd_name);
        $mData = [
            'model_id' => substr($hash, 0 ,12),
            'hash' => $hash,
            'name' => $sd_name,
            'type' => 'lora',
            'nsfw' => 0,
            'tags' => '',
            'base_model' => $version,
            'sd_name' => $sd_name,
            'download_url' => null,
            'model_info' => null,
            'rating' => 5,
            'thumb' => 'images/models/sd3.jpeg',
            'base_size' => $base_size ,
            'download_count' => 0,
            'engine' => 'sd',
            'is_download' => 1,
        ];
        $res = AiModel::firstOrCreate(
            ['hash' => $hash],
            $mData,
        );
        echo $res->hash . " done \n";
    }
}
