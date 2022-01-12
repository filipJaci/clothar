<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Cloth;

use Illuminate\Http\Request;

use App\Http\Requests\DayStoreRequest;

class ClothDayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'title' => 'Index Successful',
            'message' => 'Cloth and Day index successful',
            'write' => false,
            'data' => Day::with('clothes')->get()
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\DayStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DayStoreRequest $request)
    {
        $day = Day::firstOrCreate([
            'date' => $request['date']
        ]);

        $syncData = [];

        foreach($request['clothes'] as $clothId){
            $syncData[$clothId] = ['ocassion' => 1];
        }

        $day->clothes()->sync($syncData);

        return response()->json([
            'title' => 'Store Successful',
            'message' => 'Cloth and Day Store Successful',
            'write' => false,
            'data' => Day::with('clothes')->get()
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Day  $day
     * @return \Illuminate\Http\Response
     */
    public function destroy(Day $day)
    {
        $day->delete();

        return response()->json([
            'title' => 'Delete Successful',
            'message' => 'Cloth and Day Delete Successful',
            'write' => false,
            'data' => new \stdClass()
        ], 200);
    }
}
