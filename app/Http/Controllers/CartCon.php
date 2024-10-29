<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

use Illuminate\Support\Facades\Auth;
use App\Functions\CartFunction;
use App\Models\Product;

class CartCon extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $user_id =  $user->id;
        return Cart::where('user_id', $user_id)->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user_id = Auth::user()->id;
        $request->validate([
            "quantity" => 'required|integer|min:1',
            "product_id" => 'required|integer|exists:products,id',
            "price" => 'required|numeric',
        ]);
        $cartuserproduct = Cart::where("user_id", $user_id)->where("product_id", $request->product_id)->first();
        if ($cartuserproduct) {
            return response()->json(['message' => 'Product already in cart.'], 400);
        }
        $cartfunc = new CartFunction();
        $product = Product::where('id', $request->product_id)->first();
        $productquantity = $product->stock;
        if($request->quantity > $productquantity){
            return response()->json(['message' => 'Quantity exceeds available stock.'], 400);
        }

       $product->stock -= $request->quantity;
       $product->save();
        $cart = Cart::create([
            "user_id" => $user_id,
            "product_id" => $request->product_id,
            "quantity" => $request->quantity,
            "price" => $request->price,
        ]);
        return response()->json($cart, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $user_id = $user->id;

        $cartItem = Cart::where('user_id', $user_id)->where('id', $id)->first();

        if ($cartItem) {
            return response()->json($cartItem, 200);
        } else {
            return response()->json(['message' => 'Item not found in cart.'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "quantity" => 'required|integer|min:1',
           
        ]);
       
        $user = Auth::user();
        $user_id = $user->id;

        $cartItem = Cart::where('user_id', $user_id)->where('id', $id)->first();
        // return $cartItem->product_id;
        $cartfunc = new CartFunction();
       if(! $cartfunc->checkquantity( $request->input("quantity"), $id, $cartItem->product_id)){
            return response()->json(['message' => 'Quantity exceeds available stock.'], 400);
       }
      
        if ($cartItem) {
             $cartfunc->updatequantity($request->input("quantity"),$cartItem->quantity ,$id, $cartItem->product_id);
            $cartItem->update($request->all());
          
            return response()->json(['message' => 'Item updated successfully.'], 200);
        } else {
            return response()->json(['message' => 'Item not found in cart.'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $user_id = $user->id;

        $cartItem = Cart::where('user_id', $user_id)->where('id', $id)->first();
        $cartfunc = new CartFunction();
        $cartfunc->updatequantity(0,$cartItem->quantity ,$id, $cartItem->product_id);
        
        if ($cartItem) {
            $cartItem->delete();
            return response()->json(['message' => 'Item removed from cart successfully.'], 200);
        } else {
            return response()->json(['message' => 'Item not found in cart.'], 404);
        }
    }
}
