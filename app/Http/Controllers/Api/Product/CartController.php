<?php

namespace App\Http\Controllers\Api\Product;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Order_product;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class CartController extends Controller
{
    //add item to cart
    public function addToCart(Request $request)
    {
        // Validate the request
       
        $validator =Validator::make( $request->all(),[ 
            'quantity' => 'required|integer|min:1',
            'product_id' => 'required|exists:products,id'
        ]);
        if($validator->fails()){
            return Response()->Json(['error'=>$validator->messages(),'status'=>422], 406);
        }
        // Get the authenticated user
        $user = $request->user();
        $productId=$request->product_id;
        // Get the product
        $product = Product::findOrFail($productId);

        // Check if the product is already in the user's cart
        $existingCartItem = $user->cartItems()->where('product_id', $product->id)->first();

        if ($existingCartItem) {
            // If the product is already in the cart, update the quantity
            $existingCartItem->update([
                //'quantity' => $existingCartItem->quantity + $request->input('quantity'),
                'quantity' => $request->input('quantity'),
            ]);
        } else {
            // If the product is not in the cart, create a new cart item
            $user->cartItems()->create([
                
                'product_id' => $product->id,
                'quantity' => $request->input('quantity'),
            ]);
        }

        // Optionally, you might want to return a response indicating success
        return response()->json(['message' => 'Product added to cart successfully','status'=>200]);
    }
    //get item of cart
    public function getCart()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Load the cart items with product information
        $cartItems = $user->cartItems()->with('product')->get();
        $total=0.0;
        // Calculate the subtotal for each cart item
        $formattedCart = $cartItems->map(function ($cartItem,)use(&$total) {
            $subtotal = $cartItem->quantity * $cartItem->product->price;
            $total=$total+$subtotal;
            return [
                'id' => $cartItem->product->id,
                'name' => $cartItem->product->name,
                'brand' => $cartItem->product->brand,
                'type' => $cartItem->product->type,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->product->price,
                'category_id'=>$cartItem->product->category->id,
                'category'=>$cartItem->product->category->name,
                'image_url' => asset("storage/{$cartItem->product->image}"),
                'total' => $subtotal,
                // Add other product details if needed
            ];
        });

        // Return the formatted cart data
        return response()->json(['cart' => $formattedCart,'totalprice'=>$total]);
    }
    //delete product from cart
    public function deleteProductFromCart($productId)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Find the cart item with the specified product ID for the user
        $cartItem = $user->cartItems()->where('product_id', $productId)->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Product not found in the cart.']);
        }

        // Delete the cart item
        $cartItem->delete();

        return response()->json(['message' => 'Product deleted from the cart successfully.']);
    }
    
    // make new order and move product from cart to new order
   /* public function createOrder()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get the user's cart items with product information
        $cartItems = $user->cartItems()->with('product')->get();

        // Check if the cart is not empty
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty.']);
        }

        // Create a new order
        $order = $user->orders()->create([
            'totalAmount' => 0, // You may update this based on your logic
            'status' => 'pending', // Set the initial status of the order
        ]);

        // Move cart items to order items and calculate the total amount
        $totalAmount = 0;

        foreach ($cartItems as $cartItem) {
            // Create order item
            $orderItem = new Order_product([
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'subtotal' => $cartItem->quantity * $cartItem->product->price,
            ]);

            // Add the order item to the order
            $order->ordersproduct()->save($orderItem);

            // Calculate total amount
            $totalAmount += $orderItem->subtotal;

            // Remove the product from the cart
            $cartItem->delete();
        }

        // Update the total amount in the order
        $order->update(['totalAmount' => $totalAmount]);

        return response()->json(['message' => 'Order created successfully.']);
    }
*/
    // make new order

    public function createOrder(Request $request)
    {
        $validator=Validator::make( $request->all(),[
            'delivery_type' => 'in:delivery,pickup',
            'address_id'=>'exists:Addresses,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=> "422",
           
                'error'=>$validator->errors(),
        ],200);
        }

        $user = Auth::user();
        $cartItems = $user->cartItems()->with('product')->get();
        $shippingprice=$request->shipping_price ;
        $delivery_type=$request->delivery_type??"pickup";
        $adresse_id=$request->input('address_id');
          if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty.']);
        }
        if($user->phone ===null){
            return response()->json(['message' => 'Please add phone to your account.']);

        }
      
        if ($delivery_type === 'delivery') {
            if($adresse_id===null){
            return response()->json(['message' => 'you must add addresse to the delivery order.','status'=>400]);

            }            

        }
        return \DB::transaction(function () use ($user, $cartItems, $shippingprice,$delivery_type,$adresse_id) {
            $id = "OR-" . random_int(100000, 999999);

            $order = $user->orders()->create([
                'total_price' => 0,
                'status' => 'pending',
                'number'=>$id,
                'delivery_type'=>$delivery_type,
                'shipping_price'=> $shippingprice ?? 0
            ]);
            
            if ($delivery_type === 'delivery') {
                // Assuming you have the address_id in the request
                $order->update(['address_id' => $adresse_id]);
            }

            $this->moveCartItemsToOrder($order, $cartItems);

            return response()->json(['message' => 'Order created successfully.',"order"=>$order,'status'=>200]);
        });
        
    }
    //move product from cart to new order
    protected function moveCartItemsToOrder(Order $order, $cartItems)
    {
        $totalAmount = 0;

        foreach ($cartItems as $cartItem) {
            $subtotal= $cartItem->quantity * $cartItem->product->price;

            $orderItem = $order->ordersproduct()->create([
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'unit_price'=>$cartItem->product->price,
               // 'subtotal' => $cartItem->quantity * $cartItem->product->price,
            ]);
            Log::info("subtotal=". $subtotal);

            $totalAmount += $subtotal;
            	Log::info("s".$totalAmount);
            $cartItem->delete();
        }
        $totalAmount+=$order->shipping_price;
        Log::info("end".$totalAmount);

        $order->update(['total_price' => $totalAmount]);
    }


    //cancel order
    public function cancelOrder($orderId)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Find the order with the specified ID for the user
        $order = $user->orders()->findOrFail($orderId);

        // Check if the order can be canceled (status is 'pending' or 'processing')
        if ($order->status === 'pending' || $order->status === 'processing') {
            // Update the order status to 'cancelled'
            $order->update(['status' => 'canceled']);

            return response()->json(['message' => 'Order canceled successfully.']);
        }

        return response()->json(['message' => 'Order cannot be canceled.']);
    }
}
