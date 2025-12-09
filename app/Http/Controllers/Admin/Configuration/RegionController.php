<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Models\Configuration\Region\RegionCountry;
use App\Models\Configuration\Region\RegionState;
use App\Models\Region\RegionCity;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function cityIndex()
    {
        //
        return view('admin.configuration.region.city-index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function cityStatus(RegionCity $regionCity) 
    {
        //
        $regionCity->status = !$regionCity->status;
        $regionCity->save();

        return redirect()->back()->with('success', 'Status da cidade atualizada com sucesso.');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function stateIndex()
    {
        //
        return view('admin.configuration.region.state-index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function stateStatus(RegionState $regionState) 
    {
        //
        $regionState->status = !$regionState->status;
        $regionState->save();

        return redirect()->back()->with('success', 'Status do estado atualizado com sucesso.');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function countryIndex()
    {
        //
        return view('admin.configuration.region.country-index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function countryStatus(RegionCountry $regionCountry) 
    {
        //
        $regionCountry->status = !$regionCountry->status;
        $regionCountry->save();

        return redirect()->back()->with('success', 'Status do pais atualizado com sucesso.');
    }
}
