<?php

namespace App\Http\Controllers;

use App\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function __construct()
    {
        // TBD
        $this->middleware('auth', ['only' => [
            'showAll',
            'showOne',
            'create',
            'delete',
            'confirm'
        ]]);
    }

    public function showAll()
    {
        return response()->json(Registration::all());
    }

    public function showOne($id)
    {
        return response()->json(Registration::find($id));
    }

    public function create(Request $request)
    {
        // TBD
        $this->validate($request, [
            'name' => 'required'
        ]);

        echo "<h1>".\Auth::user()->id."</h1>";

        try {
            $registration = Registration::create([
                'name' => $request->input('name'),
                'surname' => $request->input('surname'),
                'birthdate' => $request->input('birthdate'),
                'id_number' => $request->input('id_number'),
                'street' => $request->input('street'),
                'city' => $request->input('city'),
                'zip' => $request->input('zip'),
                'note' => $request->input('note'),
                'event' => $request->input('event'),
                'team' => $request->input('team'),
                'registered_by' => \Auth::user()->id // to be checked
            ]);
            return response()->json($registration, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }        
    }

    public function delete($id)
    {
        Registration::findOrFail($id)->delete();
        return response('Deleted successfully', 200);
    }

    // TBD check API token
    public function confirm($id)
    {
        try {
            $registration = Registration::findOrFail($id);
            try {
                $registration->update([
                    'confirmed' => true
                ]);
                return response()->json($registration, 200);
            }  catch (\Illuminate\Database\QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}