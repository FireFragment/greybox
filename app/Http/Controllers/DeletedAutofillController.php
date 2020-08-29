<?php

namespace App\Http\Controllers;

use App\DeletedAutofill;
use http\Env\Response;
use Illuminate\Http\Request;

class DeletedAutofillController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => [
            'create'
        ]]);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'person' => 'required_without:team',
            'team' => 'required_without:person'
        ]);

        try {
            $deletedAutofill = DeletedAutofill::create([
                'user' => \Auth::user()->id,
                'person' => $request->input('person', null),
                'team' => $request->input('team', null)
            ]);
            return response()->json($deletedAutofill, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            $code = $e->getCode();
            if (23000 == $code || 23505 == $code) {
                return response()->json(['message' => 'duplicateDeletedAutofill'], 409);
            }
            return response()->json(['message' => $e->getMessage(), 'code' => $code], 500);
        }        
    }
}