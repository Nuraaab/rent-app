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
class UserController extends Controller
{
    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }
    public function getUser($id){
        $user = User::find($id);
        $response=[$user];
        return response($response,200); 
    }
   
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
        $fields = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $fields['email'])->first();
        if (!$user) {
            $user = User::create([
                'email' => $fields['email'],
                'password' => Hash::make($fields['password']),
            ]);
            $message = 'You are registered and logged in';
        } else {
            if (!Hash::check($fields['password'], $user->password)) {
                return response([
                    'message' => 'Credentials not correct',
                ], 401);
            }
            $message = 'You are logged in';
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'message' => $message,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ],
            'token' => $token,
        ];

        return response($response, 200);
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
    
            return response()->json(['message' => 'Login successful', 'user' => $user, 'token' => $token]);
        } catch (\Kreait\Firebase\Exception\Auth\InvalidIdToken $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        } catch (\Kreait\Firebase\Exception\Auth\AuthError $e) {
            return response()->json(['error' => 'Auth error'], 401);
        }
    }


    public function googleAuth(Request $request)
    {
        $firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials'));
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);
    
        $first_name = $validatedData['first_name'];
        $last_name = $validatedData['last_name'];
        $email = $validatedData['email'];
    
        try {
            $user = User::where('email', $email)->first();
    
            if ($user) {
                $token = $user->createToken('myapptoken')->plainTextToken;
    
                return response()->json([
                    'message' => 'User logged in successfully',
                    'user' => $user,
                    'email' => $email,
                    'token' => $token,
                ], 200);
            } else {
                $newUser = User::create([
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                ]);
                $token = $newUser->createToken('myapptoken')->plainTextToken;
    
                return response()->json([
                    'message' => 'User registered successfully',
                    'user' => $newUser,
                    'email' => $email,
                    'token' => $token,
                ], 201);
            }
        } catch (\Kreait\Firebase\Exception\Auth\InvalidIdToken $e) {
            return response()->json([
                'error' => 'Invalid token: ' . $e->getMessage(),
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    
    
    public function logout(Request $request){
    auth()->user()->tokens()->delete();
      $response = [
        'message' => 'Logged Out Successful',
      ];
      return response($response,200);
    }
}
