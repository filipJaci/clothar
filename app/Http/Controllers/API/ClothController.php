<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Cloth;

use App\Http\Requests\ClothStoreRequest;
use App\Http\Requests\ClothUpdateRequest;

class ClothController extends Controller{

  // Gets all of clothes associated with user.
  private function getUserClothes($user){
    return $user->clothes()->get();
  }

  // Validates that Cloth belong to the User.
  private function validateUserCloth($user, $cloth){
    if($user->id != $cloth->user_id){
      // Check failed.
      return false;
    }
    // Check passed.
    return true;
  }

  public function store(ClothStoreRequest $request){
    // Get the user information.
    $user = auth()->user();
    // Create new cloth.
    $cloth = new Cloth($request->validated());
    // Save cloth associated to the user.
    $user->clothes()->save($cloth);

    // Return the response.
    return response()->json([
      'scenario' => 'cloth.store.success',
      'data' => $this->getUserClothes($user)
    ], 200);
  }

  public function update(ClothUpdateRequest $request){
    // Get User.
    $user = auth()->user();
    // Get Cloth.
    $cloth = Cloth::find($request['id']);

    // Cloth does not belong to the User.
    if(! $this->validateUserCloth($user, $cloth)){
      // Return the response.
      return response()->json([
        'scenario' => 'cloth.update.wrong-user',
        'data' => $this->getUserClothes($user)
      ], 422);
    }

    // Update existing cloth.
    $cloth->update($request->validated());

    // Return the response.
    return response()->json([
      'scenario' => 'cloth.update.success',
      'data' => $this->getUserClothes($user)
    ], 200);
  }

  public function destroy(Cloth $cloth){
    // Get User.
    $user = auth()->user();

    // Cloth does not belong to the User.
    if(! $this->validateUserCloth($user, $cloth)){
      // Return the response.
      return response()->json([
        'scenario' => 'cloth.destroy.failed.wrong-user',
        'data' => $this->getUserClothes($user)
      ], 422);
    }
    // Delete the cloth.
    $cloth->delete();

    return response()->json([
      'scenario' => 'cloth.destroy.success',
      'data' => $this->getUserClothes($user)
    ], 200);
  }

  public function index(){
    // Get the user information.
    $user = auth()->user();

    // Return the response.
    return response()->json([
      'scenario' => 'cloth.index.success',
      'data' => $this->getUserClothes($user)
    ], 200);
  }
}
