<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:registration,change_password,admin_change_password',
        ]);

        $email = $request->email;

        Otp::where('email', $email)->where('type', $request->type)->delete();

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Otp::create([
            'email' => $email,
            'otp' => $otp,
            'type' => $request->type,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($email)->send(new SendOtpMail($otp));

        return response()->json(['message' => 'OTP sent to your email.']);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'type' => 'required|in:registration,change_password,admin_change_password',
        ]);

        $record = Otp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('type', $request->type)
            ->valid()
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }

        $record->update(['used_at' => now()]);

        return response()->json(['message' => 'OTP verified successfully.']);
    }
}
