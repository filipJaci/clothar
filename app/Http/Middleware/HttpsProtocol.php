<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class HttpsProtocol {

    public function handle($request, Closure $next)
    {
        if (!$request->secure() && App::environment() === 'production') {
            Request::setTrustedProxies([$request->getClientIp()],Request::HEADER_X_FORWARDED_ALL);
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request); 
    }
}
