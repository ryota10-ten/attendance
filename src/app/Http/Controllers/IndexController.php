<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller; 

class IndexController extends Controller
{
    public function show()
    {
        return view('staff.index');
    }
}
