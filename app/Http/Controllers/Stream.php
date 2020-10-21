<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Stream extends BaseController
{

    function sse()
    {
        // \DB::enableQueryLog();
        session_write_close();

        header("Content-Type: text/event-stream");
        header("Cache-Control: no-store");
        header("Access-Control-Allow-Origin: *");

        start:
        $users = \App\Models\Users::whereRaw("updated_at >= '" . date('Y-m-d') . "'")
            ->orderBy('updated_at', 'DESC')
            ->get();

        while (true) {
            set_time_limit(5);
            if (empty($lastUpdatedAt) && !$users->count())
                goto start;

            if ($users->count()) {
                $lastUpdatedAt = $users[0]->updated_at;
                echo "data: " . $users . "\n\n";
            } else {
                echo ": heartbeat\n\n";
            }

            // $lastQueries = \DB::getQueryLog();
            // foreach ($lastQueries as $lastQuery) {
            //     echo $lastQuery['query'].PHP_EOL;
            // }

            ob_flush();
            flush();
            ob_clean();
            sleep(1);

            $users = \App\Models\Users::whereRaw("updated_at > '$lastUpdatedAt'")
                ->orderBy('updated_at', 'DESC')
                ->get();
            if ($users->count())
                $lastUpdatedAt = $users[0]->updated_at;

            if (connection_aborted())
                exit();
        }
    }

    function main()
    {
        return view('main');
    }
}
