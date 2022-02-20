<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
  /**
   * Get the path the user should be redirected to when they are not authenticated.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return string|null
   */
  protected function redirectTo($request){
    abort(
      response()->json([
        'title' => 'Validation error.',
        'message' => 'There was an validation error.',
        'write' => true,
        'data' => new \stdClass()
      ], 403)
    );
  }
}
