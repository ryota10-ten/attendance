<?php

namespace App\Http\Controllers;

use App\Http\Requests\StaffLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffLoginController extends Controller
{
    public function show()
    {
        return view('staff.login');
    }

    public function login(StaffLoginRequest $request)
    {
        Auth::guard('users')->attempt($request->only('email', 'password'));

        return redirect()->route('home.show');;
    }

    public function logout()
    {
        Auth::guard('users')->logout();

        return redirect('/login');
    }
}
