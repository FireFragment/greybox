<?php


namespace App\Http\Controllers;

use App\Event,
    App\Translation;
use App\Services\TeamRulesCheckingService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => [
            'showOne',
            'create',
            'update',
            'delete',
            'showRegistrations',
            'showTeams'
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
            $event->invoice_text = $event->invoiceTextTranslation()->first();
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
        $event->invoice_text = $event->invoiceTextTranslation()->first();
        $event->note = $event->noteTranslation()->first();

        $event->prices = $event->prices()->get();
        for ($i=0; $i<count($event->prices); $i++) {
            $priceDescription = $event->prices[$i]->translation()->first();
            if ('Accommodation' == $priceDescription->en) {
                unset($event->prices[$i]);
            } else {
                $event->prices[$i]->role = $event->prices[$i]->role()->first();
            }
        }

        $dietaryRequirements = $event->dietaryRequirements()->orderBy('order')->get();
        foreach ($dietaryRequirements as $dietaryRequirement)
        {
            $dietaryRequirement->name = $dietaryRequirement->translation()->first();
        }
        $event->dietaryRequirements = $dietaryRequirements;

        return response()->json($event);
    }

    /*
     * TODO: Add accommodation, meals and membership required
     */
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
            $invoiceTextTranslation = new Translation();
            if ($request->has('invoice_cs')) {
                $invoiceTextTranslation = Translation::create([
                    'cs' => $request->input('invoice_cs'),
                    'en' => $request->input('invoice_en')
                ]);
            }
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
               'invoice_text' => $invoiceTextTranslation->id,
               'note' => $noteTranslation->id
            ]);

            $event->name = $nameTranslation;
            $event->invoice_text = $invoiceTextTranslation;
            $event->note = $noteTranslation;
            return response()->json($event, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /*
     * TODO: Add accommodation, meals and membership required
     */
    public function update($id, Request $request)
    {
        try {
            $this->authorize('update', Event::class);

            $event = Event::findOrFail($id);

            if ($request->has('name_cs')) {
                $nameTranslation = $event->nameTranslation()->updateOrCreate([], [
                    'cs' => $request->input('name_cs'),
                    'en' => $request->input('name_en')
                ]);
            }
            if ($request->has('beginning')) $this->updateColumn($event, 'beginning', $request->input('beginning'));
            if ($request->has('end')) $this->updateColumn($event, 'end', $request->input('end'));
            if ($request->has('place')) $this->updateColumn($event, 'place', $request->input('place'));
            if ($request->has('soft_deadline')) $this->updateColumn($event, 'soft_deadline', $request->input('soft_deadline'));
            if ($request->has('hard_deadline')) $this->updateColumn($event, 'hard_deadline', $request->input('hard_deadline'));
            if ($request->has('invoice_cs')) {
                $invoiceTextTranslation = $event->invoiceTextTranslation()->updateOrCreate([], [
                    'cs' => $request->input('invoice_cs'),
                    'en' => $request->input('invoice_en')
                ]);
            }
            if ($request->has('note_cs')) {
                $noteTranslation = $event->noteTranslation()->getRelated()->updateOrCreate([],[
                    'cs' => $request->input('note_cs'),
                    'en' => $request->input('note_en')
                ]);
            }

            $event->name = $event->nameTranslation()->first();
            $event->invoice_text = $event->invoiceTextTranslation()->first();
            $event->note = $event->noteTranslation()->first();
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
            $event->invoiceTextTranslation()->delete();
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
            $registration->person->dietary_requirement = $registration->person->dietaryRequirement()->first();
            if (!empty($registration->person->dietary_requirement)) $registration->person->dietary_requirement->name = $registration->person->dietary_requirement->translation()->first();
            $registration->team = $registration->team()->first();
        }

        return response()->json($registrations);
    }

    public function showTeams($id)
    {
        $event = Event::find($id);
        $this->authorize('showRegistrations', $event);

        $rulesCheckingService = new TeamRulesCheckingService();
        $registrations = $event->registrations()->where('role', 1)->get();

        $teams = array();
        foreach ($registrations as $registration) {
            $team = $registration->team()->first();
            if (null === $team) {
                $id = 'n/a';
                $registeredBy = $registration->registeredBy()->first()->withPerson();
            } else {
                $id = $team->id;
                $registeredBy = $team->registeredBy()->first()->withPerson();
            }
            if (!array_key_exists($id, $teams)) $teams[$id] = new \stdClass();
            $teams[$id]->team = $team;
            $teams[$id]->members[] = $registration->person()->first();
            if (null === $team) {
                $teams[$id]->registered_by[] = $registeredBy;
            } else {
                $teams[$id]->registered_by = $registeredBy;
            }
        }

        $teamsToPublish = array();
        foreach ($teams as $team)
        {
            if (null !== $team->team)
            {
                $team->warnings = $rulesCheckingService->checkTeamRules($team->team->name, $team->members, $event->competition, $event->finals);
            }
            $teamsToPublish[] = $team;
        }

        return response()->json($teamsToPublish);
    }

    public function showUserRegistrations($eventId, $userId)
    {
        $event = Event::findOrFail($eventId);
        $user = \App\User::findOrFail($userId);
        try {
            if ($user->id !== \Auth::user()->id) {
                $this->authorize('showUserRegistrations', Event::class);
            }
        } catch (\Exception $exception) {
            return response()->json(['message' => 'user not logged in'], 401);
        }

        $registrations = $event->registrations()->where('registered_by', $user->id)->get();

        foreach ($registrations as $registration) {
            $registration->role = $registration->role()->first();
            $registration->role->name = $registration->role->translation()->first();
            $registration->person = $registration->person()->first();
            $registration->person->dietary_requirement = $registration->person->dietaryRequirement()->first();
            if (!empty($registration->person->dietary_requirement)) $registration->person->dietary_requirement->name = $registration->person->dietary_requirement->translation()->first();
            $registration->team = $registration->team()->first();
        }

        return response()->json($registrations);
    }

    public function draw($eventId, $roundNumber)
    {
        $matchingProcessor = new \DebateMatch\MatchingProcessor();
    }
}