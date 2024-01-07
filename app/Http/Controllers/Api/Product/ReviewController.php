<?php

namespace App\Http\Controllers\Api\Product;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function store(Request $request){
        $validator=Validator::make( $request->all(),[
         "product_id"=>"required|exists:App\Models\Product,id",
        "rating"=>'required|integer|between:1,5',
        "comment"=>'string'
        ]
     );
      // $user_id=auth::user();
     $user_id=auth('sanctum')->user()->id;
     $product_id = $request->product_id;
        $rating = $request->rating;
        $comment = $request->comment;

     if($validator->fails()){
        return Response()->Json(['error'=>$validator->messages(),'status'=>422], 200);
     }
 
      

        $addedbefore = Review::where('user_id',$user_id)->where('product_id',$product_id)->get();
        if(count($addedbefore)){ 
            if($comment !==null){
             $addedbefore = Review::where('user_id',$user_id)->where('product_id',$product_id) ->update(['rating' => $rating,
                    'comment'=>$comment]);
            }
            else{
                 $addedbefore = Review::where('user_id',$user_id)->where('product_id',$product_id) ->update(['rating' => $rating,
                 ]);
            }
       

            return  response()->json([
                'status'=>200,
                'message'=>'the rating update success',
            ], 200);
        }
        Log::info('11');
        $rating=Review::create([
            'user_id'=>$user_id,
            'product_id'=>$product_id,
            'rating'=>$rating,
            'comment'=>$comment

        ]);

    
        if ($rating) {
            return  response()->json([
                'status'=>200,
                'message'=>'Success rating done',
                'rating'=>$rating
            ], 200);
        }else{
            return  response()->json([
                'status'=>404,
                'message'=>'Their is an error happened',
            ], 404);
           }
     

 }
 public function showreview($id){
    $userid=Auth::user()->id;
    $rating = Review::where('product_id',$id)->with('user')->get();
    $myreview = Review::where('product_id',$id)->where('user_id', $userid)->first();
        if (count($rating)>0){
            return response()->json([ 
            'data'=>$rating,
            'myreview'=>$myreview,
               'status'=>200
            ], 200);
          }

          else{
          return   response()->json([ 
            'message'=>'haven\'t writen any review  yet ',
               'status'=>403
            ]);
    }
}
public function showmyreview($id){
    $userid=Auth::user()->id;
    $myreview = Review::where('product_id',$id)->where('user_id', $userid)->first();
        if ($myreview){
            return response()->json([ 
           
            'myreview'=>$myreview,
               'status'=>200
            ], 200);
          }

          else{
          return   response()->json([ 
            'message'=>'haven\'t writen any review  yet ',
               'status'=>403
            ]);
    }
}
}
