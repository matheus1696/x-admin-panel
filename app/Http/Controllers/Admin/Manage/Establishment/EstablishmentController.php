<?php

namespace App\Http\Controllers\Admin\Manage\Establishment;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\Manage\Company\Establishment;
use Illuminate\Http\Request;

class EstablishmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        ActivityLogHelper::action('Visualizou a página de estabelecimentos');

        return view('admin.manage.establishment.establishment-index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Establishment $establishment)
    {
        //
        ActivityLogHelper::action('Visualizou o estabelecimento: ' . $establishment->name);

        return view('admin.manage.establishment.establishment-show', compact('establishment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
