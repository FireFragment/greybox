<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        try {
            $user = User::where('username', $request->input('username'))->first();
            if (Hash::check($request->input('password'), $user->password)) {
                try {
                    $api_token = sha1($user->id.time());

                    $user->update(['api_token' => $api_token]);
                    return response()->json($user, 200);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['message' => $e->getMessage()], 500);
                }
            } else {
                return response()->json(['message' => 'Incorrect username or password.'], 422);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function showAll()
    {
        return response()->json(User::all());
    }

    public function showOne($id)
    {
        return response()->json(User::find($id));
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'person_id' => 'required',
            'username' => 'required',
            'password' => 'required'
        ]);

        try {
            $hasher = app()->make('hash');
            $password = $hasher->make($request->input('password'));

            $user = User::create([
                'person_id' => $request->input('person_id'),
                'username' => $request->input('username'),
                'password' => $password
            ]);
            return response()->json($user, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }        
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password_old' => 'required',
            'password' => 'required'
        ]);

        try {
            $user = User::findOrFail($id);
            if (Hash::check($request->input('password_old'), $user->password)) {
                try {
                    $hasher = app()->make('hash');
                    $password = $hasher->make($request->input('password'));

                    $user->update([
                        'username' => $request->input('username'),
                        'password' => $password
                    ]);
                    return response()->json($user, 200);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['message' => $e->getMessage()], 500);
                }
            } else {
                return response()->json(['message' => 'Incorrect username or password.'], 422);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        return response('Deleted successfully', 200);
    }
}