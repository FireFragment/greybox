<?php

namespace App\Http\Controllers;

use App\Event,
    App\Price,
    App\Translation;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => [
            'showAll',
            'showOne',
            'create',
            'update',
            'delete'
        ]]);
    }

    public function showAll()
    {
        return response()->json(Price::all());
    }

    public function showOne($id)
    {
        $price = Price::find($id);
        return response()->json($price);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'event' => 'required',
            'role' => 'required',
            'description_cs' => 'required',
            'amount' => 'required'
        ]);

        $event = Event::findOrFail($request->input('event'));
        $this->authorize('create', $event);

        try {
            $descriptionTranslation = Translation::create([
                'cs' => $request->input('description_cs'),
                'en' => $request->input('description_en')
            ]);
            $noteTranslation = Translation::create([
                'cs' => $request->input('note_cs'),
                'en' => $request->input('note_en')
            ]);
            $price = Price::create([
                'event' => $event->id,
                'role' => $request->input('role'),
                'description' => $descriptionTranslation->id,
                'amount' => $request->input('amount'),
                'currency' => $request->input('currency', 'CZK'),
                'note' => $noteTranslation->id
            ]);
            return response()->json($price, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }        
    }
    
    public function update($id, Request $request)
    {
        try {
            $price = Price::findOrFail($id);

            $this->authorize('update', $price->event()->first());

            if ($request->has('event')) $this->updateColumn($price, 'event', $request->input('event'));
            if ($request->has('role')) $this->updateColumn($price, 'role', $request->input('role'));
            if ($request->has('description_cs')) {
                $descriptionTranslation = $price->translation()->updateOrCreate([], [
                    'cs' => $request->input('description_cs'),
                    'en' => $request->input('description_en')
                ]);
                $this->updateColumn($price, 'description', $descriptionTranslation->id);
            }
            if ($request->has('note_cs')) {
                $noteTranslation = $price->noteTranslation()->updateOrCreate([], [
                    'cs' => $request->input('note_cs'),
                    'en' => $request->input('note_en')
                ]);
                $this->updateColumn($price, 'note', $noteTranslation->id);
            }
            if ($request->has('amount')) $this->updateColumn($price, 'amount', $request->input('amount'));
            if ($request->has('currency')) $this->updateColumn($price, 'currency', $request->input('currency'));

            return response()->json($price, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function delete($id)
    {
        try {
            $price = Price::findOrFail($id);

            $this->authorize('delete', $price->event()->first());

            $price->delete();
            $descriptionTranslation = $price->translation()->first();
            if (null != $descriptionTranslation) $descriptionTranslation->delete();
            $noteTranslation = $price->noteTranslation()->first();
            if (null != $noteTranslation) $noteTranslation->delete();

            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}