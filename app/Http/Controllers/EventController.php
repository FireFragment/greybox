<?php


namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;

class EventController extends Controller
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
        return response()->json(Event::all());
    }

    public function showOne($id)
    {
        $event = Event::find($id);

        $event->prices = $event->prices()->get();
        for ($i=0; $i<count($event->prices); $i++) {
            $event->prices[$i]->role = $event->prices[$i]->role()->get();
        }

        return response()->json($event);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'beginning' => 'required',
            'end' => 'required',
            'place' => 'required',
            'soft_deadline' => 'required',
            'hard_deadline' => 'required'
        ]);

        // TODO: Solve authorization
        // $this->authorize('create', null, \Auth::user());

        try {
            $event = Event::create([
               'name' => $request->input('name'),
               'beginning' => $request->input('beginning'),
               'end' => $request->input('end'),
               'place' => $request->input('place'),
               'soft_deadline' => $request->input('soft_deadline'),
               'hard_deadline' => $request->input('hard_deadline'),
               'note' => $request->input('note')
            ]);

            return response()->json($event, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function update($id, Request $request)
    {
        try {
            $event = Event::findOrFail($id);

            if ($request->has('name')) $event->update(['name' => $request->input('name')]);
            if ($request->has('beginning')) $event->update(['beginning' => $request->input('beginning')]);
            if ($request->has('end')) $event->update(['end' => $request->input('end')]);
            if ($request->has('place')) $event->update(['place' => $request->input('place')]);
            if ($request->has('soft_deadline')) $event->update(['soft_deadline' => $request->input('soft_deadline')]);
            if ($request->has('hard_deadline')) $event->update(['hard_deadline' => $request->input('hard_deadline')]);
            if ($request->has('note')) $event->update(['note' => $request->input('note')]);

            return response()->json($event, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            Event::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}