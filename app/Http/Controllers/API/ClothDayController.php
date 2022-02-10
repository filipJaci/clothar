<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\Models\Day;
use App\Models\Cloth;

use Illuminate\Http\Request;

use App\Http\Requests\DayStoreRequest;
use App\Http\Requests\DayUpdateRequest;

class ClothDayController extends Controller{

  // Gets all of Days and Clothes associated with User.
  private function getUserDays($user){
    return Day::with('clothes')->where('user_id', $user->id)->get();
  }

  // Validates that all Clothes belong to the User.
  private function validateUserCloth($user, $clothes){
    // Loop through all Clothes.
    foreach ($clothes as $clothId) {
      // clothId does not belong to the User.
      if(Cloth::find($clothId)->where('user_id', $user->id)->count() === 0){
        // Check failed.
        return false;
      }
    }
    // Check passed.
    return true;
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
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\DayStoreRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(DayStoreRequest $request){

    // Get User data.
    $user = auth()->user();

    // There are Clothes that don't belong to the User.
    if(! $this->validateUserCloth($user, $request['clothes'])){
      // Return the response.
      return response()->json([
        'title' => 'Store Failed',
        'message' => 'Submited Clothes do not belong to the User.',
        'write' => true,
        'data' => $this->getUserDays($user)
      ], 422);
    }

    // Find or create Day in the DB.
    $day = Day::create([
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

    // Return the response.
    return response()->json([
      'title' => 'Store Successful',
      'message' => 'Saved worn clothes.',
      'write' => true,
      'data' => $this->getUserDays($user)
    ], 200);

  }

  /**
   * Updates existing resource in storage.
   *
   * @param  \App\Models\Day  $day
   * @param  \Illuminate\Http\DayUpdateRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function update(Day $day, DayUpdateRequest $request){
    // Get User data.
    $user = auth()->user();

    // There are Clothes that don't belong to the User.
    if(! $this->validateUserCloth($user, $request['clothes'])){
      // Return the response.
      return response()->json([
        'title' => 'Update Failed',
        'message' => 'Submited Clothes do not belong to the User.',
        'write' => true,
        'data' => $this->getUserDays($user)
      ], 422);
    }

    // Will be used during sync.
    $syncData = [];

    // Assign occassion to every Cloth.
    foreach($request['clothes'] as $clothId){
      $syncData[$clothId] = ['ocassion' => 1];
    }

    // Sync - remove/store what is supposed to be removed/stored.
    $day->clothes()->sync($syncData);

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
