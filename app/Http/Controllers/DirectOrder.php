<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderDetail;
use App\Models\Product;
class DirectOrder extends Controller
{
    public static function directorder(Request $request){
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric',
            'phone' => 'required|string',
            'addressIndetail' => 'required|string',
        ]);

        $user = Auth::user();
        $user_id = $user->id;
        
        $product = Product::where('id', $request->product_id)->first();
        $productquantity = $product->stock;
        if($request->quantity > $productquantity){
            return response()->json(['message' => 'Quantity exceeds available stock.'], 400);
        }
        $product->stock -=  $request->quantity;
        $product->save();
        $orderDetail = new OrderDetail([
            'user_id' => $user_id,
            'product_id' => $request->input('product_id'),
            'quantity' => $request->input('quantity'),
            'price' => $request->input('price'),
            'phone' => $request->input('phone'),
            'addressIndetail' => $request->input('addressIndetail'),
        ]);

        if ($orderDetail->save()) {
            return response()->json(['message' => 'Order created successfully', 'orderDetail' => $orderDetail], 201);
        } else {
            return response()->json(['message' => 'Failed to create order'], 500);
        }
    }
}
