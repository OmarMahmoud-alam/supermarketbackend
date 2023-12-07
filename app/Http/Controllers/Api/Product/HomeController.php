<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Resources\CategoryResource;
use App\Models\Banner;
use App\Models\product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class HomeController extends Controller
{
    public function index(request $request){

        Log::info('try to get  product');
        $pageSize = $request->page_size ?? 25;
        $currentPage = $request->page??1;

        if((!is_numeric($pageSize)) || $pageSize<2||$pageSize>200 )
        {
            return response()->json([
                'error'=>'page_size must be number between 1 and 200' ], 200);
        }
        if(!is_numeric($currentPage)){
             return response()->json(['error'=>'currentPage must be number '], 200);

        }
        $categories = Category::with(['products' => function ($query) {
            $query->where('isvisible', 1)->take(5); // Retrieve at most 5 products for each category
        }])->get();

        $result = [];
        $banner=Banner::where('isvisible',1)->get();

        foreach ($categories as $category) {
            $result[] = [
                'category' => $category->name,
                'categoryDetails' => ['name'=>$category->name
                ,'image_url'=>  asset("storage/{$category->image}")
                ,'id'=>$category->id],
                'products' => ProductResource::collection($category->products),
            ];
        }

        $data=[
            'status'=>200,
            'banner'=>$banner,
            'data'=> $result
            
        ];
        return response()->json($data, 200);
        }

       

}
