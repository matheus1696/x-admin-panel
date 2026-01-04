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
        ActivityLogHelper::action('Página do painel após autenticação');

        return view('dashboard');
    }
}
