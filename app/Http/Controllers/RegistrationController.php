<?php

namespace App\Http\Controllers;

use App\Registration;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function __construct()
    {
        // TBD
        $this->middleware('auth', ['only' => [
            'showAll',
            'showOne',
            'create',
            'update',
            'delete',
            'confirm',
            'showByEvent'
        ]]);
    }

    public function showAll()
    {
        return response()->json(Registration::all());
    }

    public function showOne($id)
    {
        $registration = Registration::find($id);
        $this->authorize('showOne', $registration);

        $registration->person = $registration->person()->get();
        $registration->event = $registration->event()->get();
        $registration->role = $registration->role()->get();
        $registration->registered_by = $registration->registeredBy()->get();

        return response()->json($registration);
    }

    public function create(Request $request)
    {
        // TODO: add person
        $this->validate($request, [
            'event' => 'required'
        ]);

        try {
            $registration = Registration::create([
                'person' => $request->input('person'),
                'name' => $request->input('name'),
                'surname' => $request->input('surname'),
                'birthdate' => $request->input('birthdate'),
                'id_number' => $request->input('id_number'),
                'street' => $request->input('street'),
                'city' => $request->input('city'),
                'zip' => $request->input('zip'),
                'note' => $request->input('note'),
                'event' => $request->input('event'),
                'event_id' => $request->input('event'),
                'role' => $request->input('role'),
                'accommodation' => $request->input('accommodation'),
                'team' => $request->input('team'),
                'registered_by' => \Auth::user()->id // to be checked
            ]);
            return response()->json($registration, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 500);
        }        
    }
    
    public function update($id, Request $request)
    {
        try {
            $registration = Registration::findOrFail($id);

            if ($request->has('person')) $this->updateColumn($registration, 'person', $request->input('person'));
            if ($request->has('note')) $this->updateColumn($registration, 'note', $request->input('note'));
            if ($request->has('event')) $this->updateColumn($registration, 'event_id', $request->input('event'));
            if ($request->has('role')) $this->updateColumn($registration, 'role', $request->input('role'));
            if ($request->has('accommodation')) $this->updateColumn($registration, 'accommodation', $request->input('accommodation'));
            if ($request->has('team')) $this->updateColumn($registration, 'team', $request->input('team'));

            return response()->json($registration, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            Registration::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
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