<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Verify a Google Identity Services ID token (JWT credential) sent
     * from the frontend, then email the user a sign-in OTP.
     */
    public function signIn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'credential' => 'required|string',
        ]);

        $expectedClientId = config('services.google.client_id');

        if (! $expectedClientId || str_starts_with($expectedClientId, 'your-')) {
            return response()->json([
                'success' => false,
                'message' => 'Google sign-in is not configured yet.',
            ], 503);
        }

        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $validated['credential'],
        ]);

        if ($response->failed()) {
            Log::warning('Google token verification failed', ['status' => $response->status()]);

            return response()->json([
                'success' => false,
                'message' => 'Google sign-in could not be verified.',
            ], 422);
        }

        $payload = $response->json();

        if (($payload['aud'] ?? null) !== $expectedClientId) {
            return response()->json([
                'success' => false,
                'message' => 'Google sign-in could not be verified.',
            ], 422);
        }

        if (empty($payload['email']) || empty($payload['email_verified'])) {
            return response()->json([
                'success' => false,
                'message' => 'Your Google account email is not verified.',
            ], 422);
        }

        $user = User::firstOrCreate(
            ['email' => $payload['email']],
            [
                'name' => $payload['name'] ?? $payload['email'],
                'password' => Hash::make(Str::random(32)),
            ]
        );

        // Invalidate any previous unused codes for this user.
        OtpCode::where('user_id', $user->id)->whereNull('used_at')->update(['used_at' => now()]);

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
            Log::warning('OTP email could not be sent (google)', [
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

        return response()->json([
            'success' => true,
            'message' => 'Sign-in code sent to your email',
            'data' => $data,
        ]);
    }
}