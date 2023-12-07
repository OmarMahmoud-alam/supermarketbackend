<?php

namespace App\Http\Controllers\Api\Product;

use App\Models\Review;
use App\Models\product;
use App\Models\category;
use App\Models\Favourte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCategoryResource;

class ProductController extends Controller
{
    public function show($id){
            
        $product = product::with(['category' => function ($query) {
            $query->select('id', 'name');
        },'review' => function ($query) {
            $query->where('comment', '!=' , "")->select('id','user_id','product_id', 'comment','rating');
        },
        'review.user' => function ($query) {
            $query->select('id', 'name', 'email')->get(); // Adjust the fields you want to retrieve
        }
        ])->find($id);
       // $user100=Review::with('user')->get();

        $user_id=auth('sanctum')->user()->id;
        $rate = Review::where('user_id',$user_id)->where('product_id',$id)->first('rating');
       // $avgrate = rating::where('seller_id',$request->user_id)->avg('rating');
        $avgrate = Review::where('product_id',$id)->avg('rating');
        if($avgrate===null){
            $avgrate=0;
        }
        else{
            $avgrate= number_format($avgrate, 2);
        }
        if($rate===null){
            $rate['rating']=0;
        }
       $srcimage= $product['image'];
        $product['myrate']=$rate['rating'];
        $product['favourite']=Favourte::where('user_id',$user_id)->where('Product_id',$id)->first()!=null ;
        $product['avergerate']=$avgrate;
        $product['image']=asset("storage/{$srcimage}");
            
        
       // 
        if ($product){
          return response()->json([ 
          //  'user'=>$user100,
          'data'=>$product,
             'status'=>200
          ], 200);
        }
        else{
            return   response()->json('not found');
        }
    }
//all product
    public function index(request $request){

        Log::info('try to get  product');
        $pageSize = $request->page_size ?? 25;
        $currentPage = $request->page??1;

        if((!is_numeric($pageSize)) || $pageSize<2||$pageSize>200 )
        {
            return response()->json(['error'=>'page_size must be number between 1 and 200'], 200);
        }
        if(!is_numeric($currentPage)){
             return response()->json(['error'=>'currentPage must be number '], 200);

        }
    


        $product=Product::where('isvisible', 1)->with(['category' => function ($query) {
            $query->select('id', 'name');}])->get();
          
        $result= ProductCategoryResource::collection($product);
            
      

        $data=[
            'status'=>200,
            'data'=> $result
            
        ];
        return response()->json($data, 200);
        }
    public function productCategory(request $request){
        $pageSize = $request->page_size ?? 25;
        $currentPage = $request->page??1;

        if((!is_numeric($pageSize)) || $pageSize<2||$pageSize>200 )
        {
            return response()->json(['error'=>'page_size must be number between 1 and 200'], 200);
        }
        if(!is_numeric($currentPage)){
             return response()->json(['error'=>'currentPage must be number '], 200);

        }
        Log::info($request->category_id);
    
        $categoryname=Category::where('id',$request->category_id)->select('name')->first()->name;
        Log::info($categoryname);

        $product=Product::where('isvisible', 1)->where('category_id',$request->category_id)->get();
          
        $result= ProductResource::collection($product);
      

        $data=[
            'status'=>200,
            'categoryname'=>$categoryname,
            'data'=> $result
            
        ];
        return response()->json($data, 200);
    }

    public function filterProducts(Request $request)
    {
        // Get filter parameters from the request
        $name = $request->input('name');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $category = $request->input('category_id');
        $type = $request->input('type');
        $brand = $request->input('brand');

        // Query to filter products based on the parameters
        $query = Product::query();

        if ($name !== null) {
            $query->where('name', 'like', '%' . $name . '%');
        }

        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        if ($category !== null) {
            $query->where('category_id', $category);
        }

        if ($type !== null) {
            $query->where('type', $type);
        }

        if ($brand !== null) {
            $query->where('brand', $brand);
        }

    

        // Execute the query
        $filteredProducts = $query->where('isvisible', true)->with(['category' => function ($query) {
            $query->select('id', 'name');}])->get();

         $result= ProductCategoryResource::collection($filteredProducts);

        return response()->json(['products' => $result]);
    }
}
