<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function show()
    {
        return view('staff.verification');
    }

    public function verify(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return redirect('/')->with('message', 'すでにメール認証が完了しています。');
        }
        $user->markEmailAsVerified();
        Auth::login($user);
        return redirect('/attendance');
    }

    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', '認証メールを再送信しました。');
    }
}
