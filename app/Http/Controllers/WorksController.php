<?php

namespace App\Http\Controllers;
use Inertia\Inertia;

class WorksController extends Controller
{
    public function index(){
        return inertia::render('Works/Index',[
            'extParams'=>[],
        ]); 
    }
}
