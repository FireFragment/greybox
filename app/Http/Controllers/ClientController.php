<?php

namespace App\Http\Controllers;

use App\Client;
use Fakturoid\Exception as FakturoidException;
use Illuminate\Http\Request;

class ClientController extends FakturoidController
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
        return response()->json(Client::all());
    }

    public function showOne($id)
    {
        $client = Client::find($id);
        return response()->json($client);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $data = $request->all();

        try {
            $fc = $this->getFakturoidClient();

            $subject = $fc->createSubject($data);
            $data['fakturoid_id'] = $subject->getBody()->id;
            $data['country'] = $subject->getBody()->country;
            $data['user'] = \Auth::user()->id;

            try {
                $client = Client::create($data);
                return response()->json($client, 201);
            } catch (\Illuminate\Database\QueryException $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
        } catch (FakturoidException $e) {
            return response()->json($e->getMessage(), $e->getCode());
        }
    }
    
    public function update($id, Request $request)
    {
        $data = $request->all();

        try {
            $client = Client::findOrFail($id);

            try {
                $fc = $this->getFakturoidClient();
                $subject = $fc->updateSubject($client->fakturoid_id, $data);

                try {
                    $client->update($data);
                    return response()->json($client, 200);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 500);
                }
            } catch (FakturoidException $e) {
                return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 404);
        }
    }

    public function delete($id)
    {
        try {
            $client = Client::findOrFail($id);

            try {
                $fc = $this->getFakturoidClient();
                $fc->deleteSubject($client->fakturoid_id);

                try {
                    $client->delete();
                    return response()->json(['message' => 'Deleted successfully.'], 204);
                } catch (\Illuminate\Database\QueryException $e) {
                    return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 500);
                }
            } catch (FakturoidException $e) {
                return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'code' => $e->getCode()], 404);
        }
    }
}