<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class LanguageController extends Controller
{
    public function changeLanguage(Request $request)
    {
        $request->validate(['lang' =>  ['required', Rule::in(array_keys(config('languages')))]]);

        Session::put('locale', $request->lang);
        App::setlocale($request->lang);
        return redirect()->back();
    }
}
