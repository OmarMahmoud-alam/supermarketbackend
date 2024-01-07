<?php

namespace App\Http\Controllers\Api\Product;

use App\Models\Banner;
use App\Models\product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductCategoryResource;

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
        $categories = Category::all();
        $customerId=$request->user()->id;
            $topSellingProducts =product::with('category')->with(['favorites' => function ($hasMany) use ($customerId){
                $hasMany->where('user_id', $customerId);
            }])
            ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
           /* ->leftJoin('favorites', function ($join) use ($customerId) {
                $join->on('products.id', '=', 'favorites.product_id')
                    ->where('favourtes.user_id', '=', $customerId);
            })*/
            ->select('products.*', DB::raw('COALESCE(SUM(order_products.quantity), 0) as total'))
            ->groupBy('products.id' ) 
            ->orderByDesc('total')
            ->where('isvisible',1)
            ->take(5)
            ->get();

        $banner=Banner::where('isvisible',1)->get();

       /* foreach ($categories as $category) {
            $result[] = [
                'category' => $category,
                'categoryDetails' => ['name'=>$category->name
                ,'image_url'=>  asset("storage/{$category->image}")
                ,'id'=>$category->id],
                'products' => ProductResource::collection($category->products),
            ];
        }*/

        $data=[
            'status'=>'success',
            'banner'=>$banner,
            'category'=> $categories,
            'product'=>ProductCategoryResource::collection($topSellingProducts)
            
        ];
        return response()->json($data, 200);
        }

       

}
