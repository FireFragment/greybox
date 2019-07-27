<?php


namespace App\Http\Controllers;

use App\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => [
            'showAll',
            'showOne',
            /*'create',*/
            'update',
            'delete'
        ]]);
    }

    public function showAll()
    {
        return response()->json(Person::all());
    }

    public function showOne($id)
    {
        $person = Person::find($id);
        return response()->json($person);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'surname' => 'required',
            'birthdate' => 'required',
            'street' => 'required',
            'city' => 'required',
            'zip' => 'required'
        ]);

        // TODO: Solve authorization
        // $this->authorize('create', null, \Auth::user());

        try {
            $person = Person::create([
                'name' => $request->input('name'),
                'surname' => $request->input('surname'),
                'birthdate' => $request->input('birthdate'),
                'id_number' => $request->input('id_number'),
                'street' => $request->input('street'),
                'city' => $request->input('city'),
                'zip' => $request->input('zip'),
                'vegetarian' => $request->input('vegetarian'),
                'note' => $request->input('note')
            ]);

            return response()->json($person, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $person = Person::findOrFail($id);

            if ($request->has('name')) $person->update(['name' => $request->input('name')]);
            if ($request->has('surname')) $person->update(['surname' => $request->input('surname')]);
            if ($request->has('birthdate')) $person->update(['birthdate' => $request->input('birthdate')]);
            if ($request->has('id_number')) $person->update(['id_number' => $request->input('id_number')]);
            if ($request->has('street')) $person->update(['street' => $request->input('street')]);
            if ($request->has('city')) $person->update(['city' => $request->input('city')]);
            if ($request->has('zip')) $person->update(['zip' => $request->input('zip')]);
            if ($request->has('vegetarian')) $person->update(['vegetarian' => $request->input('vegetarian')]);
            if ($request->has('note')) $person->update(['note' => $request->input('note')]);

            return response()->json($person, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            Person::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}