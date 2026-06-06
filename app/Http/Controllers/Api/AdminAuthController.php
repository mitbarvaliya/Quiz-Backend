<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        $admin->tokens()->delete();

        $token = $admin->createToken('admin-app')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'admin' => $admin,
            'token' => $token,
        ]);
    }

    public function verify(Request $request)
    {
        return response()->json([
            'admin' => $request->user(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $otpVerified = Otp::where('email', $request->email)
            ->where('type', 'admin_change_password')
            ->whereNotNull('used_at')
            ->where('used_at', '>', now()->subMinutes(5))
            ->exists();

        if (!$otpVerified) {
            return response()->json(['message' => 'Please verify OTP before resetting password.'], 422);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return response()->json(['message' => 'No admin account found with this email address.'], 422);
        }

        $admin->update(['password' => $request->password]);

        Otp::where('email', $request->email)->where('type', 'admin_change_password')->delete();

        return response()->json(['message' => 'Password reset successfully. Please login with your new password.']);
    }

    public function changePassword(Request $request)
    {
        $admin = $request->user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if (!Hash::check($request->current_password, $admin->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $otpVerified = Otp::where('email', $admin->email)
            ->where('type', 'admin_change_password')
            ->whereNotNull('used_at')
            ->where('used_at', '>', now()->subMinutes(5))
            ->exists();

        if (!$otpVerified) {
            return response()->json(['message' => 'Please verify OTP before changing password.'], 422);
        }

        $admin->update(['password' => $request->password]);

        Otp::where('email', $admin->email)->where('type', 'admin_change_password')->delete();

        return response()->json(['message' => 'Password changed successfully.']);
    }
}
