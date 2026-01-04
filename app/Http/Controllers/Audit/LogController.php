<?php

namespace App\Http\Controllers\Audit;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    //
    public function index()
    {       
        ActivityLogHelper::action('Visualizou o histórico de atividades do sistema');

        return view('activity_log');
    }
}
