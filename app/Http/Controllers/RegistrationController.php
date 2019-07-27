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
        return response()->json($registration);
    }

    public function showByUser($id)
    {
        return response()->json(Registration::select('name', 'surname', 'birthdate', 'id_number', 'street', 'city', 'zip')->where('registered_by', $id)->where('event', 'like', '2%')->groupBy('name', 'surname', 'birthdate', 'id_number', 'street', 'city', 'zip')->orderBy('surname', 'asc')->get());
    }

    public function showByEvent($id)
    {
        return response()->json(Registration::select('registrations.name', 'registrations.surname', 'registrations.event', 'teams.name as teamname')->where('registrations.registered_by', \Auth::user()->id)->where('registrations.event', 'like', $id.'%')->leftJoin('teams', 'registrations.team', '=', 'teams.id')->get());
    }

    public function create(Request $request)
    {
        // TODO: add person
        $this->validate($request, [
            'event'
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
                'team' => $request->input('team'),
                'registered_by' => \Auth::user()->id // to be checked
            ]);
            return response()->json($registration, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }        
    }
    
    public function update($id, Request $request)
    {
        try {
            $registration = Registration::findOrFail($id);

            if ($request->has('person')) $this->updateColumn($registration, 'person', $request->input('person'));
            if ($request->has('note')) $this->updateColumn($registration, 'note', $request->input('note'));
            if ($request->has('event')) $this->updateColumn($registration, 'event_id', $request->input('event'));
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