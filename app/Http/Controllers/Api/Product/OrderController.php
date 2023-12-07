<?php

namespace App\Http\Controllers\Api\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Order_product;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function createOrderForProduct(Request $request)
    {
        $validator=Validator::make( $request->all(),[
            'quantity' => 'required|numeric',
            'product_id'=>'required|exists:products,id', 
            'delivery_type' => 'in:delivery,pickup',

        
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=> "422",
           
                'error'=>$validator->errors(),
        ],200);
        }

        // Get the authenticated user
        $user = Auth::user();
        if($user->phone ===null){
            return response()->json(['message' => 'Please add phone to your account.']);

        }
        $productId=$request->product_id;
        $delivery_type=$request->delivery_type??"pickup";
        $quantity=$request->quantity ;
        Log::info('quantity' . $quantity);
        // Find the product
        $product = Product::findOrFail($productId);
        $shippingprice=$request->shipping_price?? 0 ;
        // Calculate subtotal for the order item
        $subtotal = $quantity * $product->price;
        Log::info('delivery_type =' . $delivery_type);

        $id = "OR-" . random_int(100000, 999999);
        $adresse_id=$request->input('address_id');
        if ($delivery_type === "delivery") {
            if($adresse_id===null){
            return response()->json(['message' => 'you must add addresse to the delivery order.','status'=>400]);

            }   
        }
        // Create a new order
        $order = $user->orders()->create([
                'status' => 'pending',
                'number'=>$id,
                'delivery_type'=>$delivery_type,
                'shipping_price'=> $shippingprice ,
                'total_price' => $subtotal+$shippingprice,
             // Set the initial status of the order
        ]);

        // Create an order item for the product
        $orderItem = new Order_product([
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_price'=>$product->price,

            'subtotal' => $subtotal,
        ]);
        if ($delivery_type === 'delivery') {
            // Assuming you have the address_id in the request
            $order->update(['address_id' => $adresse_id]);
            $order->save();
        }
        // Add the order item to the order
        $order->ordersproduct()->save($orderItem);

        return response()->json(['message' => 'Order created successfully.']);
    }
}
