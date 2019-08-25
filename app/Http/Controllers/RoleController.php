<?php

namespace App\Http\Controllers;

use App\Role,
    App\Translation;
use Illuminate\Http\Request;

class RoleController extends Controller
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
        $roles = Role::all();
        foreach ($roles as $role) {
            $role->name_translation = $role->translation()->get();
        }
        return response()->json($roles);
    }

    public function showOne($id)
    {
        $role = Role::find($id);
        $role->name_translation = $role->translation()->get();
        return response()->json($role);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name_cs' => 'required'
        ]);

        try {
            $translation = Translation::create([
                'cs' => $request->input('name_cs'),
                'en' => $request->input('name_en')
            ]);
            $role = Role::create([
                'name_translation' => $translation->id,
                'icon' => $request->input('icon')
            ]);
            $role->name_translation = $translation;
            return response()->json($role, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }        
    }
    
    public function update($id, Request $request)
    {
        try {
            $role = Role::findOrFail($id);
            $translation = $role->translation()->first();

            if ($request->has('name_cs')) $this->updateColumn($translation, 'cs', $request->input('name_cs'));
            if ($request->has('name_en')) $this->updateColumn($translation, 'en', $request->input('name_en'));
            if ($request->has('icon')) $this->updateColumn($role, 'icon', $request->input('icon'));

            $role->name_translation = $translation;
            return response()->json($role, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            $translation = $role->translation();
            $translation->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}