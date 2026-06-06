<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'age' => 'nullable|string',
            'standard' => 'nullable|string',
            'stream' => 'nullable|string',
            'board' => 'nullable|string',
        ]);

        $otpVerified = Otp::where('email', $request->email)
            ->where('type', 'registration')
            ->whereNotNull('used_at')
            ->where('used_at', '>', now()->subMinutes(5))
            ->exists();

        if (!$otpVerified) {
            return response()->json(['message' => 'Email not verified. Please verify OTP first.'], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'age' => $request->age,
            'standard' => $request->standard,
            'stream' => $request->stream,
            'board' => $request->board,
        ]);

        Otp::where('email', $request->email)->where('type', 'registration')->delete();

        $token = $user->createToken('quiz-app')->plainTextToken;

        return response()->json([
            'message' => 'Account created successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'No account found with this email address.',
            ], 422);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Incorrect password. Please try again.',
                'email_found' => true,
            ], 422);
        }

        $user->tokens()->delete();

        $token = $user->createToken('quiz-app')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $otpVerified = Otp::where('email', $request->email)
            ->where('type', 'change_password')
            ->whereNotNull('used_at')
            ->where('used_at', '>', now()->subMinutes(5))
            ->exists();

        if (!$otpVerified) {
            return response()->json(['message' => 'Please verify OTP before resetting password.'], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'No account found with this email address.'], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        Otp::where('email', $request->email)->where('type', 'change_password')->delete();

        return response()->json(['message' => 'Password reset successfully. Please login with your new password.']);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|string',
            'standard' => 'nullable|string',
            'stream' => 'nullable|string',
            'board' => 'nullable|string',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        $updateData = [
            'name' => $request->name,
            'age' => $request->age,
            'standard' => $request->standard,
            'stream' => $request->stream,
            'board' => $request->board,
        ];

        if ($request->filled('password')) {
            if (!$request->filled('current_password')) {
                return response()->json([
                    'message' => 'Current password is required to set a new password.',
                ], 422);
            }

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect.',
                ], 422);
            }

            $otpVerified = Otp::where('email', $user->email)
                ->where('type', 'change_password')
                ->whereNotNull('used_at')
                ->where('used_at', '>', now()->subMinutes(5))
                ->exists();

            if (!$otpVerified) {
                return response()->json(['message' => 'Please verify OTP before changing password.'], 422);
            }

            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh(),
        ]);
    }
}
