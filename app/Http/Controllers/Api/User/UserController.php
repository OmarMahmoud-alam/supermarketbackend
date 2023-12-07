<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function updateProfile(Request $request){
        $validator =Validator::make( $request->all(),[
            'phone' => 'digits:11',
            'name' => 'string',
            'date_of_birth' => 'date',
                
         ]);
         Log::info($request);
     
            if($validator->fails()){
            return Response()->Json(['error'=>$validator->messages(),'status'=>422], 200);
           // return Response()->Json(['error'=>$validator->errors(),'status'=>422], 406);
         }
        
         $user = $request->user();

         if($request->phone){$user->phone=$request->phone;}
        if($request->name){$user->name=$request->name;}
        if($request->date_of_birth){$user->date_of_birth=$request->date_of_birth;}
        
         $user->save();
         return response()->json(['message'=>'update data success','data'=>$user], 200);

    }
    
}
