<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Factory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
class UserController extends Controller
{
    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }
    // public function getUser($id){
    //     $user = User::find($id);
    //     $response=[$user];
    //     return response($response,200); 
    // }
   
    public function register(UserRequest $request){
        $user=User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'profile_image_path' =>$request->profile_image_path,
        ]);
    
        $token=$user->createToken('myapptoken')->plainTextToken;
       
    
        $response=[
            'message'=>'User Created Succefully',
            'user' =>[
                "id"=>$user->id,
                'name' => $user->name,
                'phone_number' =>$user->phone_number,
                'email' => $user->email,
                "profile_image_path" => $user->profile_image_path,
            ],
            'token'=>$token,
        ];
        
        return response($response,200);
    }
    public function checkAuth(Request $request)
        {
            // If the user is authenticated, the token is valid
            if (auth()->check()) {
                return response()->json(['message' => 'User is authenticated'], 200);
            } else {
                return response()->json(['error' => 'User not authenticated'], 401);
            }
        }

    // public function login(Request $request)
    // {
    //             $fields=$request->validate([
    //                 'email'=>'required',
    //                 'password'=>'required'
    //             ]);
    //             $user=User::where('email',$fields['email'])->first();

    //             if(!$user){
    //                 return response([
    //                     'message'=>"Unregisterd user"
    //                 ],400);
    //             }
    //             if(!$user || !Hash::check($fields['password'],$user->password) ){
    //             return response([
    //                 'message'=>'credentials not correct',

    //             ],401);
    //             }
    //             $token=$user->createToken('myapptoken')->plainTextToken;


    //             $response=[
    //                 'message'=>'You Are logged In',
    //                 'user'=>[ 
    //                     'id'=> $user->id,
    //                     'name' => $user->name,
    //                     'email' => $user->email,
    //                     ],
    //                 'token'=>$token,
    //             ];

    //             return response($response,200);

    // }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }

        $fields = $validator->validated();
        $user = User::where('email', $fields['email'])->first();

        if (!$user) {
            // Register new user
            $user = User::create([
                'email' => $fields['email'],
                'password' => Hash::make($fields['password']),
            ]);
            $message = 'You are registered and logged in';
        } else {
            // Block Firebase users from password login
            if ($user->firebase_uid) {
                return response()->json([
                    'message' => 'This email is linked with Google login. Please use Google to sign in.',
                ], 400);
            }

            // Check password
            if (!Hash::check($fields['password'], $user->password)) {
                return response()->json(['message' => 'Incorrect email or password'], 401);
            }

            $message = 'You are logged in';
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json([
            'message' => $message,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
            'token' => $token,
            'expires_in' => Carbon::now()->addDays(180),
        ], 200);
    }

    

    public function phoneAuth(Request $request)
    {
        $firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials'));
    
        $auth = $firebase->createAuth();
        $idToken = $request->bearerToken(); // Get the token from the Authorization header
    
        try {
            $verifiedIdToken = $auth->verifyIdToken($idToken);
            $phoneNumber = $verifiedIdToken->claims()->get('phone_number');
    
            // Retrieve or create the user based on the phone number
            $user = User::firstOrCreate(
                ['phone_number' => $phoneNumber]
            );
            $token = $user->createToken('myapptoken')->plainTextToken;
    
            return response()->json(['message' => 'Login successful', 'user' => $user, 'token' => $token, 'expires_in' => Carbon::now()->addDays(180)]);
        } catch (\Kreait\Firebase\Exception\Auth\InvalidIdToken $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        } catch (\Kreait\Firebase\Exception\Auth\AuthError $e) {
            return response()->json(['error' => 'Auth error'], 401);
        }
    }


    // public function googleAuth(Request $request)
    //     {
    //         $firebase = (new Factory)
    //             ->withServiceAccount(config('firebase.credentials'));
    //         $validatedData = $request->validate([
    //             'first_name' => 'required|string|max:255',
    //             'last_name' => 'required|string|max:255',
    //             'email' => 'required|email|max:255',
    //             'id_token' => 'required|string'
    //         ]);
        
    //         $first_name = $validatedData['first_name'];
    //         $last_name = $validatedData['last_name'];
    //         $email = $validatedData['email'];
    //         $firebaseUid = $validatedData['id_token'];
        
    //         try {
    //             // Check if the email already exists in the system
    //             $user = User::where('email', $email)->where('firebase_uid', $firebaseUid)->first();
        
    //             if ($user) {
    //                 // If user exists, log them in
    //                 $token = $user->createToken('myapptoken')->plainTextToken;
        
    //                 return response()->json([
    //                     'message' => 'User logged in successfully',
    //                     'user' => $user,
    //                     'email' => $email,
    //                     'token' => $token,
    //                 ], 200);
    //             } else {
    //                 // If email is not found, check if it exists with a different registration method
    //                 $existingEmail = User::where('email', $email)->exists();
    //                 if ($existingEmail) {
    //                     return response()->json([
    //                         'message' => 'Email already in use. Please use a different method to log in.',
    //                     ], 400);
    //                 }
        
    //                 // Register the new user
    //                 $newUser = User::create([
    //                     'first_name' => $first_name,
    //                     'last_name' => $last_name,
    //                     'email' => $email,
    //                     'firebase_uid' => $firebaseUid,
    //                 ]);
    //                 $token = $newUser->createToken('myapptoken')->plainTextToken;
        
    //                 return response()->json([
    //                     'message' => 'User registered successfully',
    //                     'user' => $newUser,
    //                     'email' => $email,
    //                     'expires_in' => Carbon::now()->addDays(180),
    //                     'token' => $token,
    //                 ], 200);
    //             }
    //         } catch (\Kreait\Firebase\Exception\Auth\InvalidIdToken $e) {
    //             return response()->json([
    //                 'message' => 'Invalid token: ' . $e->getMessage(),
    //             ], 401);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'message' => 'An unexpected error occurred: ' . $e->getMessage(),
    //             ], 500);
    //         }
    //     }

    public function googleAuth(Request $request)
    {
        $firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->createAuth();

        $validatedData = $request->validate([
            'id_token' => 'required|string',
        ]);

        $idToken = $validatedData['id_token'];

        try {
            $verifiedToken = $firebase->verifyIdToken($idToken);
            $claims = $verifiedToken->claims()->all();

            $firebaseUid = $claims['sub'] ?? null; 
            $email = $claims['email'] ?? null;
            $fullName = $claims['name'] ?? '';
            $nameParts = explode(' ', $fullName, 2); 
            $first_name = $nameParts[0] ?? '';
            $last_name = $nameParts[1] ?? '';

            if (!$firebaseUid || !$email) {
                return response()->json(['message' => 'Firebase UID and email are required.'], 400);
            }
            $user = User::where('firebase_uid', $firebaseUid)->first();
            if (!$user) {
                $user = User::where('email', $email)->first();

                if ($user) {
                    $user->firebase_uid = $firebaseUid;
                    $user->save();
                }
            }

            if ($user) {
                $token = $user->createToken('myapptoken')->plainTextToken;

                return response()->json([
                    'message' => 'User logged in successfully',
                    'user' => $user,
                    'token' => $token,
                    'firebaseUID' => $firebaseUid,
                ], 200);
            }
            $newUser = User::create([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'firebase_uid' => $firebaseUid, 
            ]);

            $token = $newUser->createToken('myapptoken')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $newUser,
                'token' => $token,
                'firebaseUID' => $firebaseUid,
                'expires_in' => Carbon::now()->addDays(180),
            ], 200);

        } catch (\Kreait\Firebase\Exception\Auth\InvalidIdToken $e) {
            return response()->json(['message' => 'Invalid Firebase token: ' . $e->getMessage()], 401);
        } catch (\Exception $e) {
            Log::error('Google Auth Error: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }

    public function firebaseEmailAuth(Request $request)
    {
        $firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials'))
            ->createAuth();

        $validatedData = $request->validate([
            'id_token' => 'required|string',
        ]);

        $idToken = $validatedData['id_token'];

        try {
            $verifiedToken = $firebase->verifyIdToken($idToken);
            $claims = $verifiedToken->claims()->all();

            $firebaseUid = $claims['sub'] ?? null; 
            $email = $claims['email'] ?? null;
            $emailVerified = $claims['email_verified'] ?? false;

            if (!$firebaseUid || !$email) {
                return response()->json(['message' => 'Firebase UID and email are required.'], 400);
            }

            // Check if email is verified
            if (!$emailVerified) {
                return response()->json([
                    'message' => 'Please verify your email address before logging in.',
                    'email_verified' => false,
                ], 403);
            }

            // Find or create user
            $user = User::where('firebase_uid', $firebaseUid)->first();
            
            if (!$user) {
                $user = User::where('email', $email)->first();

                if ($user) {
                    // Link existing account with Firebase
                    $user->firebase_uid = $firebaseUid;
                    $user->save();
                }
            }

            if ($user) {
                $token = $user->createToken('myapptoken')->plainTextToken;

                return response()->json([
                    'message' => 'User logged in successfully',
                    'user' => $user,
                    'token' => $token,
                    'firebaseUID' => $firebaseUid,
                ], 200);
            }

            // Create new user
            $newUser = User::create([
                'email' => $email,
                'firebase_uid' => $firebaseUid,
            ]);

            $token = $newUser->createToken('myapptoken')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $newUser,
                'token' => $token,
                'firebaseUID' => $firebaseUid,
                'expires_in' => Carbon::now()->addDays(180),
            ], 200);

        } catch (\Kreait\Firebase\Exception\Auth\InvalidIdToken $e) {
            return response()->json(['message' => 'Invalid Firebase token: ' . $e->getMessage()], 401);
        } catch (\Exception $e) {
            Log::error('Firebase Email Auth Error: ' . $e->getMessage());
            return response()->json(['message' => 'An unexpected error occurred.'], 500);
        }
    }
  
    public function logout(Request $request) {
        if (auth()->check()) {
            auth()->user()->tokens()->delete(); // Revoke all API tokens
            return response()->json(['message' => 'Logged Out Successfully'], 200);
        }
    
        return response()->json(['error' => 'User not authenticated'], 401);
    }

    // user
    public function getUser(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    // Update user profile
    public function updateUser(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|unique:users,phone_number,' . $user->id . '|regex:/^\+?[0-9]{10,15}$/',
            'profile_image_path' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Update user details including profile image
        $user->update($request->only('first_name', 'last_name', 'email', 'phone_number', 'profile_image_path'));

        return response()->json([
            'message' => 'User updated successfully!',
            'user' => $user
        ]);
    }
    
}
