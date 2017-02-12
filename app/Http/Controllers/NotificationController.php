<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchNotification;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function matchNotificationIndex(){
        $data = Auth::user()->matchNotifications;

        return response()->json(
            $data,
            200,
            ['Content-Type' => 'application/json; charset=UTF-8', 'charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }
}
