<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Models\Day;
use App\Models\Cloth;

use Illuminate\Http\Request;

use App\Http\Requests\DayStoreRequest;

class ClothDayController extends Controller{

  // Gets all of Days and Clothes associated with User.
  private function getUserDays($user){
    return Day::with('clothes')->where('user_id', $user->id)->get();
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(){
    // Get the user information.
    $user = auth()->user();

    return response()->json([
      'title' => 'Index Successful',
      'message' => 'Cloth and Day index successful',
      'write' => false,
      'data' => $this->getUserDays($user)
    ], 200);
  }

  /**
   * Store a newly created or update existing resource in storage.
   *
   * @param  \Illuminate\Http\DayStoreRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(DayStoreRequest $request){

    // if(Day::count() === 1){
    //   dd($request['clothes']);
    // }

    // Get user data.
    $user = auth()->user();

    // Find or create Day in the DB.
    $day = Day::firstOrCreate([
      'date' => new Carbon($request['date']),
      'user_id' => $user->id
    ]);

    // Will be used during sync.
    $syncData = [];

    // Assign occassion to every Cloth.
    foreach($request['clothes'] as $clothId){
      $syncData[$clothId] = ['ocassion' => 1];
    }

    // Sync - remove/store what is supposed to be removed/stored.
    $day->clothes()->sync($syncData);

    if($request['clothes'][0] == 3){
      dd(Day::with('clothes')->get()->toArray());
    }

    // Return the response.
    return response()->json([
      'title' => 'Store Successful',
      'message' => 'Saved worn clothes.',
      'write' => true,
      'data' => $this->getUserDays($user)
    ], 200);

  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Day  $day
   * @return \Illuminate\Http\Response
   */
  public function destroy(Day $day){
    // Delete day from the DB.
    $day->delete();

    // Get the user information.
    $user = auth()->user();

    return response()->json([
      'title' => 'Delete Successful',
      'message' => 'Removed worn clothes.',
      'write' => true,
      'data' => $this->getUserDays($user)
    ], 200);
  }
}
