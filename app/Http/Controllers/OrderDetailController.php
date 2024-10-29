<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use App\Http\Requests\StoreOrderDetailRequest;
use App\Http\Requests\UpdateOrderDetailRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Functions\CartFunction;
class OrderDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index()
    {
        $user = Auth::user();
        $user_id =  $user->id;
        return OrderDetail::where('user_id', $user_id)->get();
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
       
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'phone' => 'required|string',
            'addressIndetail' => 'required|string',
        ]);

        $user = Auth::user();
        $user_id = $user->id;

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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $user_id = $user->id;

        $orderDetail = OrderDetail::where('id', $id)->where('user_id', $user_id)->first();

        if ($orderDetail) {
            return response()->json($orderDetail);
        } else {
            return response()->json(['message' => 'Order not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "phone" => 'required',
            "addressIndetail" => 'required',
            "price" => 'required',
            "quantity" => 'required',
        ]);

        if(OrderDetail::where('id', $id)->update($request->all())) {
            return response()->json(['message' => 'Order updated successfully']);
        }else{
            return response()->json(['message' => 'Order not found']);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(OrderDetail::find($id) == null) {
            return response()->json(['message' => 'Order not found']);
        }
        $cartItem = OrderDetail::where('id', $id)->first();
        $cartfunc = new CartFunction();
        $cartfunc->updatequantity2(0,$cartItem->quantity ,$id, $cartItem->product_id);
        if(OrderDetail::destroy($id)) {
            return response()->json(['message' => 'Order deleted successfully']);
        }
    }
}
