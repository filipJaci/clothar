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

  // Validates that there is no Day data for the selected date.
  private function validateUserDay($user, $date){
    // Date does not belong to the User.
    if(Day::where('date', new Carbon($date))->where('user_id', $user->id)->count() > 0){
      // Check failed.
      return false;
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
      'scenario' => 'cloth-day.index.success',
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
        'scenario' => 'cloth-day.store.failed.wrong-user',
        'data' => $this->getUserDays($user)
      ], 422);
    }

    // User has already saved some data on the sent Day.
    if(! $this->validateUserDay($user, $request['date'])){
      // Return the response.
      return response()->json([
        'scenario' => 'cloth-day.store.failed.wrong-date',
        'data' => $this->getUserDays($user)
      ], 422);
    }

    // Create Day in the DB.
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
      'scenario' => 'cloth-day.store.success',
      'data' => $this->getUserDays($user)
    ], 200);

  }

  /**
   * Updates existing resource in storage.
   *
   * @param  \Illuminate\Http\DayUpdateRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function update(DayUpdateRequest $request){
    // Get User data.
    $user = auth()->user();
    // Get Day.
    $day = Day::find($request['id']);

    // Day does not belong to the User.
    if($day->user_id != $user->id){
      // Return the response.
      return response()->json([
        'scenario' => 'cloth-day.update.failed.wrong-day',
        'data' => $this->getUserDays($user)
      ], 422);
    }

    // There are Clothes that don't belong to the User.
    if(! $this->validateUserCloth($user, $request['clothes'])){
      // Return the response.
      return response()->json([
        'scenario' => 'cloth-day.update.failed.wrong-clothes',
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
      'scenario' => 'cloth-day.update.success',
      'data' => $this->getUserDays($user)
    ], 200);

  }

  /**
   * Remove the specified resource from storage.
   *
   * @return \Illuminate\Http\Response
   */
  public function destroy(Day $day){
    // Get User.
    $user = auth()->user();

    // Day does not belong to the User.
    if($day->user_id != $user->id){
      // Return the response.
      return response()->json([
        'scenario' => 'cloth-day.destroy.failed.wrong-user',
        'data' => $this->getUserDays($user)
      ], 422);
    }

    // Delete day from the DB.
    $day->delete();

    return response()->json([
      'scenario' => 'cloth-day.destroy.success',
      'data' => $this->getUserDays($user)
    ], 200);
  }
}
