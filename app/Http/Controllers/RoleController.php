<?php

namespace App\Http\Controllers;

use App\Role;
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
        return response()->json(Role::all());
    }

    public function showOne($id)
    {
        $role = Role::find($id);
        return response()->json($role);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        try {
            $role = Role::create($request->all());
            return response()->json($role, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }        
    }
    
    public function update($id, Request $request)
    {
        try {
            $role = Role::findOrFail($id);

            if ($request->has('name')) $this->updateColumn($role, 'name', $request->input('name'));
            if ($request->has('icon')) $this->updateColumn($role, 'icon', $request->input('icon'));

            return response()->json($role, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            Role::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}