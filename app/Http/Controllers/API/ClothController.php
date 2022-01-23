<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Cloth;

use App\Http\Requests\ClothStoreRequest;

class ClothController extends Controller
{

    public function store(ClothStoreRequest $request){

        Cloth::create($request->validated());

        return response()->json([
            'title' => 'Create Successful',
            'message' => 'A piece of clothing has been created.',
            'write' => true,
            'data' => Cloth::all()
        ], 200);
    }

    public function update(ClothStoreRequest $request, Cloth $cloth){
        
        $cloth->update($request->validated());

        return response()->json([
            'title' => 'Update Successful',
            'message' => 'A piece of clothing has been updated.',
            'write' => true,
            'data' => Cloth::all()
        ], 200);
    }

    public function destroy(Cloth $cloth){
        $cloth->delete();

        return response()->json([
            'title' => 'Delete Successful',
            'message' => 'A piece of clothing has been deleted.',
            'write' => true,
            'data' => Cloth::all()
        ], 200);
    }

    public function show(Cloth $cloth){

        return response()->json([
            'title' => 'Show Successful',
            'message' => '',
            'write' => false,
            'data' => $cloth
        ], 200);
    }

    public function index(){

        return response()->json([
            'title' => 'Index Successful',
            'message' => 'Cloth index successful',
            'write' => false,
            'data' => Cloth::all()
        ], 200);
    }
}
