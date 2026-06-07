<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentViewMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->has('student_view') && auth()->check()) {
            view()->share('studentView', true);
        } else {
            view()->share('studentView', false);
        }
        return $next($request);
    }
}
