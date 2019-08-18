<?php

namespace App\Http\Controllers;

use App\Price;
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
            'amount' => 'required'
        ]);

        try {
            $price = Price::create($request->all());
            return response()->json($price, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }        
    }
    
    public function update($id, Request $request)
    {
        try {
            $price = Price::findOrFail($id);

            if ($request->has('event')) $this->updateColumn($price, 'event', $request->input('event'));
            if ($request->has('role')) $this->updateColumn($price, 'role', $request->input('role'));
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
            Price::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted successfully.'], 204);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}