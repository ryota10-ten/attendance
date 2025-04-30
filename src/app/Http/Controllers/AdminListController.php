<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminListController extends Controller
{
    public function show()
    {
        $admin = Auth::guard('admin')->user();
        $staffs = User::all();

        return view('admin.member',compact('staffs'));
    }
}
