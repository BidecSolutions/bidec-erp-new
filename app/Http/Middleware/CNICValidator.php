<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class CNICValidator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the CNIC is stored in the session
        $cnic = Session::get('login_cnic');
        
        // Example validation: Check if CNIC exists in the session
        if (!$cnic) {
            return redirect()->route('parentLogin')->withErrors(['cnic' => 'CNIC is not provided or session has expired.']);
        }

        // If everything is fine, continue to the next middleware or request handler
        return $next($request);
    }
}
