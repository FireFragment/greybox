<?php

namespace App\Http\Controllers;

use App\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function __construct()
    {
        // TBD
        $this->middleware('auth', ['only' => [
            'showAll',
            'showOne',
            'create',
            'delete',
            'update'
        ]]);
    }

    public function showAll()
    {
        return response()->json(Institution::orderBy('place')->get());
    }

    public function showOne($id)
    {
        return response()->json(Institution::find($id));
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'place' => 'required'
        ]);

        try {
            $institution = Institution::create([
                'name' => $request->input('name'),
                'short_name' => $request->input('short_name', substr($request->input('name'), 0, 30)),
                'place' => $request->input('place'),
                'head' => $request->input('head'),
                'application' => $request->input('application', 0),
                'note' => $request->input('note')
            ]);
            return response()->json($institution, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }        
    }

    public function update($id, Request $request)
    {
        try {
            $institution = Institution::findOrFail($id);

            if ($request->has('name')) $institution->update(['name' => $request->input('name')]);
            if ($request->has('short_name')) $institution->update(['short_name' => $request->input('short_name')]);
            if ($request->has('place')) $institution->update(['place' => $request->input('place')]);
            if ($request->has('head')) $institution->update(['head' => $request->input('head')]);
            if ($request->has('application')) $institution->update(['application' => $request->input('application')]);
            if ($request->has('note')) $institution->update(['note' => $request->input('note')]);

            return response()->json($institution, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            Institution::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}