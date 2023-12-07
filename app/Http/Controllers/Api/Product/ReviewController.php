<?php

namespace App\Http\Controllers\Api\Product;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function store(Request $request){
        $validator=Validator::make( $request->all(),[
         "product_id"=>"required|exists:App\Models\Product,id",
        "rating"=>'required|integer|between:1,5'
        ]
     );
      // $user_id=auth::user();
     $user_id=auth('sanctum')->user()->id;
     $product_id = $request->product_id;
        $rating = $request->rating;

     if($validator->fails()){
        return Response()->Json(['error'=>$validator->messages(),'status'=>422], 200);
     }
 
      

        $addedbefore = Review::where('user_id',$user_id)->where('product_id',$product_id)->get();
        if(count($addedbefore)){ 
        $addedbefore = Review::where('user_id',$user_id)->where('product_id',$product_id) ->update(['rating' => $rating,]);

            return  response()->json([
                'status'=>200,
                'message'=>'the rating update success',
            ], 200);
        }
        $rating=Review::create([
            'user_id'=>$user_id,
            'product_id'=>$product_id,
            'rating'=>$rating,

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

 public function show($id){
    $rating = Review::where('seller_id',$id)->avg('rating');
        if ($rating){
            return response()->json([ 
            'data'=>$rating,
               'status'=>200
            ], 200);
          }

          else{
          return   response()->json([ 
            'message'=>'haven\'t any favourite yet ',
               'status'=>403
            ]);
    }
}
}
