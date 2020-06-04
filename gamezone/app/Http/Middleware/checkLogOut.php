<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class checkLogOut
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user =  Auth::user();
            if($user->activated == 1){
                return $next($request);
            }else{
                $notification = array(
                    'message' => 'Access Denied, You don’t have permission to access on this Server!',
//                    'alert-type' => 'success'
                );
                return redirect('/')->with($notification);
            }
        }
        else
        {
            return redirect('/');
        }
    }
}
