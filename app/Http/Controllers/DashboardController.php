<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        //
        ActivityLogHelper::action('Página inicial do painel');

        return view('dashboard');
    }
}
