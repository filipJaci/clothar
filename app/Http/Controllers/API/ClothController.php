<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Cloth;

use App\Http\Requests\ClothStoreRequest;

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
      'title' => 'Create Successful',
      'message' => 'Cloth has been created.',
      'write' => true,
      'data' => $this->getUserClothes($user)
    ], 200);
  }

  public function update(ClothStoreRequest $request, Cloth $cloth){
    // Get user information.
    $user = auth()->user();

    // Cloth does not belong to the User.
    if(! $this->validateUserCloth($user, $cloth)){
      // Return the response.
      return response()->json([
        'title' => 'Update Failed',
        'message' => 'Cloth does not belong to the User.',
        'write' => true,
        'data' => $this->getUserClothes($user)
      ], 422);
    }

    // Update existing cloth.
    $cloth->update($request->validated());

    // Return the response.
    return response()->json([
      'title' => 'Update Successful',
      'message' => 'Cloth has been updated.',
      'write' => true,
      'data' => $this->getUserClothes($user)
    ], 200);
  }

  public function destroy(Cloth $cloth){
    // Get the user information.
    $user = auth()->user();

    // Cloth does not belong to the User.
    if(! $this->validateUserCloth($user, $cloth)){
      // Return the response.
      return response()->json([
        'title' => 'Delete Failed',
        'message' => 'Cloth does not belong to the User.',
        'write' => true,
        'data' => $this->getUserClothes($user)
      ], 422);
    }
    // Delete the cloth.
    $cloth->delete();

    return response()->json([
      'title' => 'Delete Successful',
      'message' => 'Cloth has been deleted.',
      'write' => true,
      'data' => $this->getUserClothes($user)
    ], 200);
  }

  public function index(){
    // Get the user information.
    $user = auth()->user();

    // Return the response.
    return response()->json([
      'title' => 'Index Successful',
      'message' => 'Cloth index successful.',
      'write' => false,
      'data' => $this->getUserClothes($user)
    ], 200);
  }
}
