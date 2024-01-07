<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Custom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Notifications\EmailVerificationNotificaion;

class AuthCustomController extends Controller
{
    public function register(Request $request)
    {



        $validator=Validator::make( $request->all(),[
            'name' => 'required|string',
            'email'=>'required|string|unique:customs,email|email', 
            'password'=>'required|string',
            'c_password' => 'required|same:password'
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=> "422",
            'message'=> "can't register",
                'error'=>$validator->errors(),
        ],200);
        }

        $user = new Custom([
            'name'  => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if($user->save()){
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;
            $user->notify(new EmailVerificationNotificaion);
            return response()->json([
            'message' => 'Successfully created user!',
            'accessToken'=> $token,
            'status'=>'success'
            ],200);
        }
        else{
            return response()->json(['error'=>'Provide proper details']);
        }
    }

    public function resendEmailVerificationOtp(){
        $user=auth('sanctum')->user();
try{
    $user->notify(new EmailVerificationNotificaion);
    
    return response()->json(['message'=>'the otp send again','code'=>1]);
}
catch(Exception $e){
    return response()->json(['message'=>('their an error happened'.$e)]);

}
      

    }

public function login(Request $request)
{
    $validator=Validator::make( $request->all(),[
    'email' => 'required|string|email',
    'password' => 'required|string',
    'remember_me' => 'boolean'
    ]); 
    
    if ($validator->fails()) {
        return response()->json(['status'=> "403",
        'message'=> "validation error",
            'error'=>$validator->errors(),
    ],200);
    }

if(!Auth::guard('customers')->attempt($request->only(['email' , 'password' ]))){
        //if(!Auth::guard('customers')->attempt(['email' => $request->email, 'password' => $request->password])){
          //  if (!Auth::guard('customers')->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {

 return response()->json(
    [
        'status'=> "206",
        'message'=> "the email or the password is wrong",
           
    ]
    , 200);

}

   /* $credentials = request(['email','password']);
    if(!Auth::attempt($credentials))
    {
    return response()->json([
        'message' => 'Unauthorized'
    ],200);
    }
*/
    //$user = $request->user();
    $user =Auth::guard('customers')->user();

    Log::info($user);
    
    $user_verify =$user->email_verified_at;
    $tokenResult = $user->createToken('Personal Access Token');
    $token = $tokenResult->plainTextToken;

    return response()->json([
    'accessToken' =>$token,
    'verify_at'=>$user_verify,
    'token_type' => 'Bearer',
    'status' => 'success',
    ]);
}
public function user(Request $request)
{
    return response()->json($request->user());
}
public function logout(Request $request)
{
    $request->user()->tokens()->delete();

    return response()->json([
    'message' => 'Successfully logged out'
    ]);

}
public function logoutonly(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
    'message' => 'Successfully logged out'
    ]);

}
}
