<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load('roles'),
        ]);
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found for this email.',
            ], 404);
        }

        OtpCode::where('user_id', $user->id)->whereNull('used_at')->update(['used_at' => now()]);

        $data = self::createAndSendCode($user);

        return response()->json([
            'success' => true,
            'message' => 'Sign-in code sent to your email',
            'data' => $data,
        ]);
    }

    public function resendOtp(Request $request): JsonResponse
    {
        return $this->sendOtp($request);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|digits:6',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found for this email.',
            ], 404);
        }

        $otp = OtpCode::where('user_id', $user->id)
            ->where('code', $validated['otp'])
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired code.',
            ], 422);
        }

        $otp->update(['used_at' => now()]);

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->load('roles');

        return response()->json([
            'success' => true,
            'message' => 'Sign-in code verified successfully',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ]);
    }

    private static function createAndSendCode(User $user): array
    {
        $code = (string) random_int(100000, 999999);
        OtpCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::raw(
                "Your restaurant sign-in code is {$code}. It expires in 10 minutes.",
                function ($message) use ($user) {
                    $message->to($user->email, $user->name)
                        ->subject('Your sign-in code');
                }
            );
        } catch (\Throwable $e) {
            Log::warning('OTP email could not be sent', [
                'user' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        $data = [
            'email' => $user->email,
            'expires_in_minutes' => 10,
        ];

        // Only expose the code in non-production so local development can be tested.
        if (config('app.env') !== 'production') {
            $data['dev_otp'] = $code;
        }

        return $data;
    }
}