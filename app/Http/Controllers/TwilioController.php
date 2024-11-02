<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Cache;

class TwilioController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
    }
    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
        ]);
        $verificationCode = rand(100000, 999999);
        Cache::put($request->input('phone_number'), $verificationCode, now()->addMinutes(10));
        $response = $this->client->messages->create(
            $request->input('phone_number'),
            [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => "Your verification code is: $verificationCode",
            ]
        );

        return response()->json(['message' => 'Verification code sent successfully!', 'data' => $response]);
    }
    public function verifyCode(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'code' => 'required|string',
        ]);
        $cachedCode = Cache::get($request->input('phone_number'));
        if ($cachedCode && $cachedCode == $request->input('code')) {
            $user = User::where('phone_number', $request->input('phone_number'))->first();
            if (!$user) {
                $user = User::create([
                    'phone_number' => $request->input('phone_number'),
                    'password' => Hash::make(str_random(8)),
                ]);
            }
            Auth::login($user);
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['message' => 'User logged in successfully!', 'token' => $token]);
        } else {
            return response()->json(['message' => 'Invalid verification code!'], 400);
        }
    }
}
