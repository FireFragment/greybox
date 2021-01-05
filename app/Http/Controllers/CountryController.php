<?php

namespace App\Http\Controllers;

use App\Services\CountryService;

class CountryController extends Controller
{
    public function showCzech()
    {
        return response()->json(CountryService::listCzechCountryNames());
    }

    public function showEnglish()
    {
        return response()->json(CountryService::listEnglishCountryNames());
    }

    public function showBoth()
    {
        return response()->json(CountryService::listCountryNames());
    }
}