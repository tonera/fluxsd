<?php

namespace App\Http\Controllers\api;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use App\Models\AiConfig;
use App\Models\User;
use App\Support\GlobalCode;
use Illuminate\Http\Request;

// access_url:"",
// atz_token:"",
// together_token:""

class SetupController extends Controller
{
    public function store(Request $request){
        $admin = User::where('is_admin', 1)->first();
        if($admin){
            return ['code' => 700, 'msg' => __('Administrator already exists, the system is installed')];
        }
        $input = $request->post();

        $updateKeys = [
            'access_url' => 'storage.local.access_url',
            'atz_token'=> 'engine.atz.token',
            'together_token' => 'text.together.token'
        ];

        foreach($updateKeys as $key => $editKey){
            if(isset($input[$key]) && $input[$key]){
                AiConfig::updateOrCreate(
                    ['c_key'=> $editKey],
                    ['c_value' => $input[$key]]
                );
            }
        }

        $fortifyUser = new CreateNewUser();
        $user = $fortifyUser->create($input);
        $user->is_admin = 1;
        $user->save();
        return ['code' => GlobalCode::SUCCESS, 'data' => $user];

    }
}
