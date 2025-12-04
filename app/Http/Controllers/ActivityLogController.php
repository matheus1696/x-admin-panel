<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;

class ActivityLogController extends Controller
{
    //
    public function index()
    {       
        ActivityLogHelper::action('Acessou a página de atividades do sistema');

        return view('activity_log');
    }
}
