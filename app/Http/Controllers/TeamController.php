<?php

namespace App\Http\Controllers;

use App\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct()
    {
        // TBD
        $this->middleware('auth', ['only' => [
            'showAll',
            'showOne',
            'create',
            'delete',
            'update',
            'merge'
        ]]);
    }

    public function showAll()
    {
        return response()->json(Team::orderBy('name')->get());
    }

    public function showOne($id)
    {
        return response()->json(Team::find($id));
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        try {
            $team = Team::create([
                'name' => $request->input('name'),
                'registered_by' => \Auth::user()->id // to be checked
            ]);
            return response()->json($team, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }        
    }

    public function update($id, Request $request)
    {
        try {
            $team = Team::findOrFail($id);

            if ($request->has('name')) $team->update(['name' => $request->input('name')]);

            return response()->json($team, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            Team::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // TBD check API token
    public function confirm($id)
    {
        try {
            $team = Team::findOrFail($id);
            try {
                $team->update([
                    'confirmed' => true
                ]);
                return response()->json($team, 200);
            }  catch (\Illuminate\Database\QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function merge(Request $request)
    {
        $team = $request->input('team');
        $merge = $request->input('merge');
        try {
            \DB::table('registrations')
                ->where('team', $team)
                ->update(['team' => $merge]);
            Team::findOrFail($team)->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}