<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Resources\ProductResource;
use App\Models\product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    public function addToFavorites(request $request)  
    {
        $product=Product::find($request->product_id);
        if(!$product){
            return response()->json(['message' => 'can\'t find product', 'status '=>404]);

        }
        // Get the authenticated user
        $user = Auth::user();
        if ($user->favoriteProducts()->where('product_id', $product->id)->exists()) {
            return response()->json(['message' => 'Product is already in favorites']);
        }
        Log::info('Workfine1');

        // Add the product to the user's favorites
        $user->favoriteProducts()->attach($product->id);
        Log::info('Workfine2');

        return response()->json(['message' => 'Product added to favorites successfully', 'status '=>200]);
    }
    public function removeFromFavorites(request $request)
    {
        // Get the authenticated user
        $user = Auth::user();
        $productid=$request->product_id;
        // Check if the product is in the user's favorites
        if (!$user->favoriteProducts()->where('product_id', $productid)->exists()) {
            return response()->json(['message' => 'Product is not in favorites']);
        }

        // Remove the product from the user's favorites
        $user->favoriteProducts()->detach($productid);

        return response()->json(['message' => 'Product removed from favorites successfully']);
    }
    public function getAllFavorites()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get all favorite products of the user
        $favoriteProducts =ProductResource::collection( $user->favoriteProducts)  ;

        return response()->json(['favorites' => $favoriteProducts]);
    }
}
