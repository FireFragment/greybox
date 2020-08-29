<?php

namespace App\Http\Controllers;

use App\DietaryRequirement;

class DietaryRequirementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => [
        ]]);
    }

    public function showAll()
    {
        $dietaryRequirements = DietaryRequirement::all();
        foreach ($dietaryRequirements as $dietaryRequirement) {
            $dietaryRequirement->name = $dietaryRequirement->translation()->first();
        }
        return response()->json($dietaryRequirements);
    }

    public function showOne($id)
    {
        $dietaryRequirement = DietaryRequirement::findOrFail($id);
        $dietaryRequirement->name = $dietaryRequirement->translation()->first();
        return response()->json($dietaryRequirement);
    }
}