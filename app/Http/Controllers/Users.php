<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class Users extends BaseController
{

    function add(Request $request)
    {
        $user = new \App\Models\Users();

        $user->username    = $request->username;
        $user->password    = $request->password;   //md5($request->password);
        $user->email       = $request->email;
        $user->contact     = $request->contact;
        $user->verified_at = date('Y-m-d H:i:s');

        if ($user->save())
            return response()->json([
                'code' => 200,
                'state' => 'saved',
            ]);
        else {
            return response()->json([
                'code' => 300,
                'state' => 'error',
            ]);
        }
    }
}
