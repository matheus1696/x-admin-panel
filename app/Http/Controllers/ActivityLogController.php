<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogHelper;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    //
    public function index()
    {        
        return view('activity_log');
    }
}
