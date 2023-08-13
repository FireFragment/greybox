<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => [
        ]]);
    }

    public function showAll()
    {
        return response()->json(Competition::all());
    }
}