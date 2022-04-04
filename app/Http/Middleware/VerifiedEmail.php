<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\User;

class VerifiedEmail{

  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next){
    // Get User data.
    $user = User::where('email', $request->email)->first();
    // User is found.
    if($user !== null){
      // User is verified.
      if($user->email_verified){
        // Continue.
        return $next($request);
      }
      // User has not been verified.
      else{
        // Set API response.
        return response()->json([
          'title' => 'Verification error',
          'message' => 'This account is not verified, please verify first to continue.',
          'write' => true,
          'data' => []
          // Set API http code to 403 - Forbidden.
        ], 403);
      }
    }
    // User was not found.
    else{
        // Set API response.
        return response()->json([
          'title' => 'Login error',
          'message' => 'User with this email address was not found.',
          'write' => true,
          'data' => []
          // Set API http code to 400 - Bad request.
        ], 400);
    }
  }
}
