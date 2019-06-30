<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    private $regex = '/^\S*(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/';

    public function __construct()
    {
        $this->middleware('auth', ['only' => [
            'logout',
            'showAll',
            'showOne',
            'update',
            'delete'
        ]]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->input('username'))->first();
        if (!empty($user)) {
            if (Hash::check($request->input('password'), $user->password)) {
                try {
                    $api_token = sha1($user->id.time());

                    $user->update(['api_token' => $api_token]);

                    $return_value = array(
                        'id_token' => $api_token,
                        'id' => $user->id,
                        'username' => $user->username,
                        'api_token' => $user->api_token,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at
                    );

                    return response()->json($return_value, 200)
                        ->header('Authorization', 'Bearer '.$api_token)
                        ->header('Access-Control-Expose-Headers', 'Authorization');
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['message' => $e->getMessage()], 500);
                }
            } else {
                return response()->json(['message' => 'invalidCredentials'], 401);
            }
        } else {
            return response()->json(['message' => 'invalidCredentials'], 401);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = User::where('api_token', $request->input('api_token'))->first();
            try {
                $user->update([
                        'api_token' => null
                ]);
                return response()->json($user, 200);
            } catch (\Illuminate\Database\QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 500);
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
        $user = User::find($id);
        $this->authorize('showOne', $user, \Auth::user());
        return response()->json($user);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            /*
             * temporary workaround
             */
            //'person_id' => 'required',
            'username' => 'required|email',
            'password' => 'required|min:8|confirmed|regex:'.$this->regex
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
            if ($e->getCode() == 23000) {
                $code = 409;
                $message = "duplicateEntry";
            } else {
                $code = 500;
                $message = $e->getMessage();
            }
            return response()->json(['message' => $message], $code);
        }        
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password_old' => 'required',
            'password' => 'required|min:8|confirmed|regex:'.$this->regex
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
        try {
            User::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function sendResetPasswordEmail(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|email'
        ]);

        $username = $request->input('username');

        $user = User::where('username', $username)->first();
        if (!empty($user)) {
            try {
                $recovery_token = sha1($user->id.time());
                DB::insert('insert into password_resets (email, token, created_at) values (?, ?, now())', array($username, $recovery_token));

                try {
                    $mail_data = array('token' => $recovery_token);
                    Mail::to($username)->send(new ResetPassword($mail_data));
                    return response()->json(['message' => 'E-mail sent.'], 200);
                } catch (\Exception $e) {
                    return response()->json(['message' => $e->getMessage()], 500);
                }
            } catch (\Illuminate\Database\QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'userNotFound'], 404);
        }
    }

    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'password' => 'required|min:8|confirmed|regex:'.$this->regex
        ]);

        $username = DB::select('select email from password_resets where token = ?', array($request->input('token')));
        if (!empty($username)) {
            $user = User::where('username', $username[0]->email)->first();
            if (!empty($user)) {
                try {
                    $hasher = app()->make('hash');
                    $password = $hasher->make($request->input('password'));

                    $user->update([
                        'password' => $password
                    ]);
                    return response()->json($user, 200);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['message' => $e->getMessage()], 500);
                }
            } else {
                return response()->json(['message' => 'userNotFound'], 404);
            }
        } else {
            return response()->json(['message' => 'tokenNotFound'], 404);
        }
    }
}