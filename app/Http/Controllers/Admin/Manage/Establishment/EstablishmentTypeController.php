<?php

namespace App\Http\Controllers\Admin\Manage\Establishment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EstablishmentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('admin.manage.establishment-type.establishment-type-index');
    }
}
