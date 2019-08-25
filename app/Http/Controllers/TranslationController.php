<?php

namespace App\Http\Controllers;

use App\Translation;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => [
            'create',
            'update',
            'delete'
        ]]);
    }

    public function showAll()
    {
        return response()->json(Translation::all());
    }

    public function showOne($id)
    {
        $translation = Translation::find($id);
        return response()->json($translation);
    }

    public function create(Request $request)
    {
        try {
            $translation = Translation::create($request->all());
            return response()->json($translation, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }        
    }
    
    public function update($id, Request $request)
    {
        try {
            $translation = Translation::findOrFail($id);

            if ($request->has('cs')) $this->updateColumn($translation, 'cs', $request->input('cs'));
            if ($request->has('en')) $this->updateColumn($translation, 'en', $request->input('en'));

            return response()->json($translation, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            Translation::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}