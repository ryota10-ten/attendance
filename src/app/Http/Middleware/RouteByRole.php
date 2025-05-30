<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RouteByRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();
        $method = strtoupper($request->method());

        if ($path === 'stamp_correction_request/list') {
            if (Auth::guard('admin')->check()) {
                return redirect()->route('admin.request');
            } elseif (Auth::guard('users')->check()) {
                return redirect()->route('staff.request');
            }
        }

        if (preg_match('#^attendance/\d+$#', $path)) {
            $id = $request->route('id');

            if (Auth::guard('admin')->check()) {
                $routeName = ($method === 'POST') ? 'admin.update' : 'admin.detail';
                return redirect()->route($routeName, ['id' => $id]);
            } elseif (Auth::guard('users')->check()) {
                $routeName = ($method === 'POST') ? 'staff.application' : 'staff.detail';
                return redirect()->route($routeName, ['id' => $id]);
            } else {
                return redirect('/login');
            }
        }
        return $next($request);
    }
}