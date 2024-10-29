<?php

namespace App\Functions;

use App\Models\Cart;

use App\Models\Product;
use Ramsey\Uuid\Type\Integer;

class CartFunction
{
public function checkquantity($inputed_quantity, $order_id, $product_id)
{
     // Retrieve the cart item based on order_id and product_id
     $cartItem = Cart::where('id', $order_id)
                          ->where('product_id', $product_id)
                          ->first();
    $productitem = Product::where('id', $product_id)->first();
     // Check if the cart item exists
     if (!$cartItem) {
          return false; // or throw an exception if preferred
     }

     // Compare the input quantity with the available quantity
     if ($inputed_quantity > $productitem->stock) {
          return false; // Quantity exceeds available stock
     }

     return true; // Quantity is valid
}
public function updatequantity($inputed_quantity, $original_quantity,$cart_id, $product_id)
{
    // return [$inputed_quantity,$original_quantity];
    // Retrieve the cart item based on cart_id and product_id
    $cartItem = Cart::where('id', $cart_id)
                    ->where('product_id', $product_id)
                    ->first();
                    // return $cartItem;
    $productitem = Product::where('id', $product_id)->first();
// return $productitem;
    // Check if the cart item exists
    if (!$cartItem) {
        return false; // or throw an exception if preferred
    }

    // Check if the input quantity is valid
    if ($inputed_quantity > $original_quantity) {
       
        // Increase in quantity, decrease the stock
        $difference = (int) $inputed_quantity - (int) $original_quantity;
        $productitem->stock -= $difference;
    } else if ($inputed_quantity < $original_quantity) {
       
        // Decrease in quantity, increase the stock
        $difference = $original_quantity - $inputed_quantity;
        $productitem->stock += $difference;
    }

    // Save the updated stock value
    $productitem->save();
   
    // Update the cart item quantity
 


    return true; // Quantity updated successfully
}
public function updatequantity2($inputed_quantity, $original_quantity,$cart_id, $product_id)
{
    
    $productitem = Product::where('id', $product_id)->first();

   

    // Check if the input quantity is valid
    if ($inputed_quantity > $original_quantity) {
       
        // Increase in quantity, decrease the stock
        $difference = (int) $inputed_quantity - (int) $original_quantity;
        $productitem->stock -= $difference;
    } else if ($inputed_quantity < $original_quantity) {
       
        // Decrease in quantity, increase the stock
        $difference = $original_quantity - $inputed_quantity;
        $productitem->stock += $difference;
    }

    // Save the updated stock value
    $productitem->save();
   
    // Update the cart item quantity
 


    return true; // Quantity updated successfully
}
public function deleteallcartsthatpassedtwodayson(){
    $carts = Cart::where('created_at', '<', now()->subDays(2))->get();
    

    foreach ($carts as $cart) {
        $this->updatequantity(0,$cart->quantity ,$cart->id, $cart->product_id);
        $cart->delete();
    }
    return true;
}
}
