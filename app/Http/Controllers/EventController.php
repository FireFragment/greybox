<?php


namespace App\Http\Controllers;

use App\Event,
    App\Translation;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => [
            'create',
            'update',
            'delete',
            'showRegistrations'
        ]]);
    }

    public function showAll()
    {
        $user = \Auth::user();
        if ($user !== null && $user->can('showAll', Event::class)) {
            $events = Event::all();
        } else {
            $events = Event::where('hard_deadline', '>', date("Y-m-d H:i:s"))->get();
        }
        foreach ($events as $event) {
            $event->name = $event->nameTranslation()->first();
            $event->note = $event->noteTranslation()->first();
        }
        return response()->json($events);
    }

    public function showOne($id)
    {
        $event = Event::find($id);

        if (null === $event) {
            return response()->json(['message' => 'eventNotFound'], 404);
        }

        $event->name = $event->nameTranslation()->first();
        $event->note = $event->noteTranslation()->first();

        $event->prices = $event->prices()->get();
        for ($i=0; $i<count($event->prices); $i++) {
            $event->prices[$i]->role = $event->prices[$i]->role()->first();
        }

        return response()->json($event);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name_cs' => 'required',
            'beginning' => 'required',
            'end' => 'required',
            'place' => 'required',
            'soft_deadline' => 'required',
            'hard_deadline' => 'required'
        ]);

        $this->authorize('create', Event::class);

        try {
            $nameTranslation = Translation::create([
                'cs' => $request->input('name_cs'),
                'en' => $request->input('name_en')
            ]);
            $noteTranslation = new Translation();
            if ($request->has('note_cs')) {
                $noteTranslation = Translation::create([
                    'cs' => $request->input('note_cs'),
                    'en' => $request->input('note_en')
                ]);
            }
            $event = Event::create([
               'name' => $nameTranslation->id,
               'beginning' => $request->input('beginning'),
               'end' => $request->input('end'),
               'place' => $request->input('place'),
               'soft_deadline' => $request->input('soft_deadline'),
               'hard_deadline' => $request->input('hard_deadline'),
               'note' => $noteTranslation->id
            ]);

            $event->name = $nameTranslation;
            $event->note = $noteTranslation;
            return response()->json($event, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function update($id, Request $request)
    {
        try {
            $this->authorize('update', Event::class);

            $event = Event::findOrFail($id);
            $nameTranslation = $event->nameTranslation()->first();
            $noteTranslation = $event->noteTranslation()->first();

            if ($request->has('name_cs')) $this->updateColumn($nameTranslation, 'cs', $request->input('name_cs'));
            if ($request->has('name_en')) $this->updateColumn($nameTranslation, 'en', $request->input('name_en'));
            if ($request->has('beginning')) $this->updateColumn($event, 'beginning', $request->input('beginning'));
            if ($request->has('end')) $this->updateColumn($event, 'end', $request->input('end'));
            if ($request->has('place')) $this->updateColumn($event, 'place', $request->input('place'));
            if ($request->has('soft_deadline')) $this->updateColumn($event, 'soft_deadline', $request->input('soft_deadline'));
            if ($request->has('hard_deadline')) $this->updateColumn($event, 'hard_deadline', $request->input('hard_deadline'));
            if ($request->has('note_cs')) $this->updateColumn($noteTranslation, 'cs', $request->input('note_cs'));
            if ($request->has('note_en')) $this->updateColumn($noteTranslation, 'en', $request->input('note_en'));

            $event->name = $nameTranslation;
            $event->note = $noteTranslation;
            return response()->json($event, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        $this->authorize('delete', Event::class);

        try {
            $event = Event::findOrFail($id);
            $event->delete();
            $event->nameTranslation()->delete();
            $event->noteTranslation()->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function showRegistrations($id)
    {
        $event = Event::find($id);
        $this->authorize('showRegistrations', $event);

        $registrations = $event->registrations()->get();

        foreach ($registrations as $registration) {
            $registration->role = $registration->role()->first();
            $registration->role->name = $registration->role->translation()->first();
            $registration->person = $registration->person()->first();
            $registration->team = $registration->team()->first();
        }

        return response()->json($registrations);
    }

    public function showUserRegistrations($eventId, $userId)
    {
        $event = Event::find($eventId);
        $user = \App\User::find($userId);
        if ($user->id !== \Auth::user()->id) {
            $this->authorize('showUserRegistrations', Event::class);
        }

        $registrations = $event->registrations()->where('registered_by', $user->id)->get();

        foreach ($registrations as $registration) {
            $registration->role = $registration->role()->first();
            $registration->role->name = $registration->role->translation()->first();
            $registration->person = $registration->person()->first();
            $registration->team = $registration->team()->first();
        }

        return response()->json($registrations);
    }
}