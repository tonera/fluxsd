<?php

namespace App\Http\Controllers\api;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Controllers\Controller;
use App\Models\AiConfig;
use App\Models\User;
use App\Support\GlobalCode;
use Illuminate\Support\Facades\Cache;
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
            'together_token' => 'text.together.token',

            'engine.lc.is_active' => 'engine.lc.is_active',
            'engine.atz.is_active' => 'engine.atz.is_active',
            'text.together.is_active' => 'text.together.is_active',
        ];

        $input['engine.lc.is_active'] = 1;
        if(isset($input['atz_token']) && $input['atz_token']){
            $input['engine.atz.is_active'] = 1;
        }
        if(isset($input['together_token']) && $input['together_token']){
            $input['text.together.is_active'] = 1;
        }

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
        Cache::forget('aibox_tbl_config');
        return ['code' => GlobalCode::SUCCESS, 'data' => $user];

    }
}
