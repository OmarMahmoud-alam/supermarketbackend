<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class EmailVerificationController extends Controller
{
    private $otp;
    public function __construct() {
        $this->otp =new Otp;
    }
   public function email_verificationOtp(Request    $request){
    $validator=Validator::make( $request->all(),[
        'email'=>['required','email','exists:App\Models\Custom,email'],
        'otp'=>['required','max:6'],
        ]); 
        
        if ($validator->fails()) {
            return response()->json(['status'=> "403",
            'message'=> "verfiy go wrong",
            'result'=>0,
                'error'=>$validator->errors(),
        ],200);
        }
        Log::info($request->email);
        Log::info($request->otp);
    $otpVal=$this->otp->validate($request->email,$request->otp);
    Log::info($otpVal->status);

    if(!$otpVal->status){
    Log::info("50000");

        return response()->json(['error'=> $otpVal ,'message'=> "verfiy go wrong",], 200);
    
    }
    $user=User::where('email',$request->email)->first();


    if ($user) {

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
    return response()->json(['status' => 'success','message'=>'the email is verified'], 200);

        }

    }

    return response()->json(['message'=>'Verify go wrong'], 200);


   }

    public function sendVerificationEmail(Request $request)
    {
        
        if ($request->user()->hasVerifiedEmail()) {
            return [
                'message' => 'Already Verified'
            ];
        }

        $request->user()->sendEmailVerificationNotification();

        return ['status' => 'verification-link-sent'];
    }

    public function verify(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return [
                'message' => 'Email already verified'
            ];
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return [
            'message'=>'Email has been verified'
        ];
    }
}