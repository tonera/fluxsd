<?php

namespace App\Http\Controllers;

use App\Support\CUrl;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ToolsController extends Controller
{
    // crop / edit image
    public function cropper(Request $request){
        $idStyles = [
            ['label' => 'Free', 'w' => 1, 'h' => 1, 'id' => 0],
            ['label' => '9:16', 'w' => 9, 'h' => 16, 'id' => 1],
            ['label' => '3:4', 'w' => 3, 'h' => 4, 'id' => 2],
            ['label' => '7:9', 'w' => 7, 'h' => 9, 'id' => 3],
            ['label' => '1:1', 'w' => 1, 'h' => 1, 'id' => 4],
            ['label' => '9:7', 'w' => 9, 'h' => 7, 'id' => 5],
            ['label' => '4:3', 'w' => 4, 'h' => 3, 'id' => 6],
            ['label' => '16:9', 'w' => 16, 'h' => 9, 'id' => 7],
        ];
        if($request->url){
            $content = CUrl::get($request->url);
            $base64 = 'data:image/png;base64,'.base64_encode($content);
        }
        return Inertia::render('Tools/Cropper',[
            'idPhotoStyle' => $idStyles,
            'base64' => $base64??'',
        ]); 
    }

    // super resolution
    public function upscale(Request $request){
        return Inertia::render('Tools/Upscale',[
            'img_url' => $request->url??'',
        ]); 
    }

    //removebg
    public function removebg(Request $request){
        if($request->url){
            $content = CUrl::get($request->url);
            $base64 = 'data:image/png;base64,'.base64_encode($content);
        }
        return Inertia::render('Tools/RemoveBG',[
            'base64' => $base64??'',
        ]); 
    }

    //faceswap
    public function faceswap(Request $request){
        if($request->url){
            $content = CUrl::get($request->url);
            $base64 = 'data:image/png;base64,'.base64_encode($content);
        }
        return Inertia::render('Tools/Faceswap',[
            'base64' => $base64??'',
        ]); 
    }

}
