<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function show()
    {
        return view('admin.login');
    }

    public function login(AdminLoginRequest $request)
    {
        Auth::guard('admin')->attempt($request->only('email', 'password'));

        return redirect('/admin/attendance/list');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();

        return redirect('/admin/login');
    }
}
