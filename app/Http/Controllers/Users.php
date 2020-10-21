<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class Users extends BaseController
{

    function add(Request $request)
    {
        $user = new \App\Models\Users();

        $user->name              = $request->name;
        $user->password          = md5($request->password);
        $user->email             = $request->email;
        $user->email_verified_at = date('Y-m-d H:i:s');

        if ($user->save())
            echo 'SAVED';
        else {
            echo 'UNABLE TO SAVE';
        }
    }
}
