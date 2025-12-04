<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;

class ActivityLogController extends Controller
{
    //
    public function index()
    {       
        ActivityLogHelper::action('Acessou o log de atividades');

        return view('activity_log');
    }
}
