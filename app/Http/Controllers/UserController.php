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
    const PASSWORD_REGEX = '/^\S*(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/';

    public function __construct()
    {
        // TODO: solve auth/only
        $this->middleware('auth', ['only' => [
            'logout',
            'showAll',
            'showOne',
            'update',
            'updatePassword',
            'delete'
        ]]);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|email',
            'password' => 'required'
        ]);
        $username = User::normalizeUserName($request->input('username'));
        $user = User::where('username', $username)->first();
        if (empty($user))
        {
            return response()->json(['message' => 'invalidCredentials'], 401);
        }
        if (!$user->isPasswordCorrect($request->input('password')))
        {
            return response()->json(['message' => 'invalidCredentials'], 401);
        }
        try {
            $user->setApiToken();
            $user->setRole(); // TODO: vyřešit elegantněji
            $user->id_token = $user->api_token;
            return response()->json($user, 200)
                ->header('Authorization', 'Bearer '.$user->api_token)
                ->header('Access-Control-Expose-Headers', 'Authorization');
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        $user = User::where('api_token', $request->input('api_token'))->first();
        if (empty($user))
        {
            return response()->json(['message' => 'invalidCredentials'], 401);
        }
        try {
            $user->update([
                    'api_token' => null
            ]);
            return response()->json(['message' => 'logoutSuccessful'], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function showAll()
    {
        if (\Auth::user()->isAdmin()) return response()->json(User::all());
        return response()->json(array(\Auth::user()));
    }

    public function showOne($id)
    {
        $user = User::find($id);
        $this->authorize('showOne', $user, \Auth::user());
        $user->setRole();
        $return_value = array(
            'id' => $user->id,
            'username' => $user->username,
            'person' => $user->person()->first(),
            'role' => $user->role,
            'api_token' => $user->api_token,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at
        );
        return response()->json($return_value);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            /*
             * temporary workaround
             */
            //'person_id' => 'required',
            'username' => 'required|email',
            'password' => 'required|min:8|confirmed|regex:'.self::PASSWORD_REGEX
        ]);

        try {
            $hasher = app()->make('hash');
            $password = $hasher->make($request->input('password'));
            // TODO: refactor You may pass a default value as the second argument to the input method. This value will be returned if the requested input value is not present on the request: $name = $request->input('name', 'Sally');
            $preferredLocale = $request->input('preferred_locale');
            $username = User::normalizeUserName($request->input('username'));

            $user = User::create([
                'person_id' => $request->input('person_id'),
                'username' => $username,
                'password' => $password
            ]);
            if (!empty($preferredLocale)) {
                $user->update([
                    'preferred_locale' => $preferredLocale
                ]);
            }
            return response()->json($user, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            if (23000 == $e->getCode() || 23505 == $e->getCode()) {
                return response()->json(['username' => ['validation.unique']], 409);
            } else {
                $code = 500;
                $message = $e->getMessage();
            }
            return response()->json(['message' => $message], $code);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $user = User::findOrFail($id);
            $this->authorize('update', $user, \Auth::user());

            $preferredLocale = $request->input('preferred_locale');

            if (!empty($preferredLocale)) {
                $user->update([
                    'preferred_locale' => $preferredLocale
                ]);
            }
            return response()->json($user, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updatePassword($id, Request $request)
    {
        $this->validate($request, [
            'username' => 'required|email',
            'password_old' => 'required',
            'password' => 'required|min:8|confirmed|regex:'.self::PASSWORD_REGEX
        ]);

        try {
            $user = User::findOrFail($id);
            $this->authorize('update', $user, \Auth::user());
            if (Hash::check($request->input('password_old'), $user->password)) {
                try {
                    $hasher = app()->make('hash');
                    $password = $hasher->make($request->input('password'));
                    $preferredLocale = $request->input('preferred_locale');
                    $username = User::normalizeUserName($request->input('username'));

                    $user->update([
                        'username' => $username,
                        'password' => $password
                    ]);
                    if (!empty($preferredLocale)) {
                        $user->update([
                            'preferred_locale' => $preferredLocale
                        ]);
                    }
                    return response()->json($user, 200);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['message' => $e->getMessage()], 500);
                }
            } else {
                return response()->json(['message' => 'invalidCredentials'], 401);
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

        $username = User::normalizeUserName($request->input('username'));

        $user = User::where('username', $username)->first();
        if (!empty($user)) {
            try {
                DB::delete('delete from password_resets where email = ?', array($username));
                $recovery_token = sha1($user->id.time());
                DB::insert('insert into password_resets (email, token, created_at) values (?, ?, now())', array($username, $recovery_token));

                try {
                    $mail_data = array('token' => $recovery_token);

                    // TODO: vyřešit jak nastavit locale pouze pro email / případně jak používat locale vůbec
                    app('translator')->setLocale($user->preferredLocale());

                    Mail::to($username)->bcc('greybox@debatovani.cz')->send(new ResetPassword($mail_data));
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
            'password' => 'required|min:8|confirmed|regex:'.self::PASSWORD_REGEX
        ]);

        $token = $request->input('token');
        $username = DB::select('select email, created_at from password_resets where token = ?', array($token));
        if (!empty($username)) {
            // TODO: set cron for deleting expired tokens
            // TODO: solve timezones
            $difference = time() - strtotime($username[0]->created_at);
            if ($difference < 60*60*24) {
                $user = User::where('username', $username[0]->email)->first();
                if (!empty($user)) {
                    try {
                        $hasher = app()->make('hash');
                        $password = $hasher->make($request->input('password'));

                        $user->update([
                            'password' => $password
                        ]);
                        DB::delete('delete from password_resets where token = ?', array($token));
                        return response()->json($user, 200);
                    } catch (\Illuminate\Database\QueryException $e) {
                        return response()->json(['message' => $e->getMessage()], 500);
                    }
                } else {
                    return response()->json(['message' => 'userNotFound'], 404);
                }
            } else {
                return response()->json(['message' => 'tokenExpired'], 404);
            }
        } else {
            return response()->json(['message' => 'tokenNotFound'], 404);
        }
    }

    public function showPeople($userId, $eventId = null)
    {
        $user = User::find($userId);
        $userRegistrations = $user->registrations()->select('person')->whereNotNull('person')->groupBy('person')->get();

        if (null !== $eventId)
        {
            $event = \App\Event::find($eventId);
            $eventRegistrations = $event->registrations()->get();
        }

        $people = array();
        foreach ($userRegistrations as $registration)
        {
            $person = $registration->person()->first();
            if (!empty($person))
            {
                if (isset($eventRegistrations))
                {
                    foreach ($eventRegistrations as $eventRegistration)
                    {
                        $eventPerson = $eventRegistration->person()->first();

                        $person->registered = false;
                        if ($eventPerson->id == $person->id)
                        {
                            $person->registered = true;
                            break;
                        }
                    }
                }
                $people[] = $person;
            }
        }

        usort($people, function ($a, $b) {
            $coll = new \Collator('cs_CZ');
            return $coll->compare($a->surname, $b->surname);
        });

        return response()->json($people, 200);
    }

    public function showClients($id) {
        $user = User::find($id);
        $clients = $user->clients()->get();
        return response()->json($clients, 200);
    }

    public function showTeams($id)
    {
        $user = User::find($id);
        $registrations = $user->registrations()->select('team')->whereNotNull('team')->groupBy('team')->get();

        $teams = array();
        foreach ($registrations as $registration)
        {
            $team = $registration->team()->first();
            if (!empty($team)) {
                $teams[] = $team;
            }
        }

        usort($teams, function ($a, $b) {
            $coll = new \Collator('cs_CZ');
            return $coll->compare($a->name, $b->name);
        });

        return response()->json($teams, 200);

    }
}