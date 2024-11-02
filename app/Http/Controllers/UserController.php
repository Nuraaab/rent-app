<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
class UserController extends Controller
{
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

    public function logout(Request $request){
    auth()->user()->tokens()->delete();
      $response = [
        'message' => 'Logged Out Successful',
      ];
      return response($response,200);
    }
}
