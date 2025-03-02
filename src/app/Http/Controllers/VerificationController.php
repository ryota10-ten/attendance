<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Verified;
use App\Models\User;

class VerificationController extends Controller
{
    public function show()
    {
        return view('verification');
    }

    public function verify(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            return redirect('/')->with('message', 'すでにメール認証が完了しています。');
        }
        $user->markEmailAsVerified();
        Auth::login($user);
        return redirect('/');
    }

    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', '認証メールを再送信しました。');
    }
}
