<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\About;
use App\Models\User;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Http\Middleware\AdminMiddleware;
use GuzzleHttp\Psr7\Response;

Route::get("/test",function(){
    return 333;
})->middleware([AdminMiddleware::class]);
Route::post('/register', 'App\Http\Controllers\SecuirtyController@register')->withoutMiddleware('auth:sanctum');
Route::post('/login', 'App\Http\Controllers\SecuirtyController@login')->withoutMiddleware('auth:sanctum');
Route::post('/editprofile', 'App\Http\Controllers\SecuirtyController@updateprofile');
Route::post('/logout', 'App\Http\Controllers\SecuirtyController@logout');
Route::resource('/menu', 'App\Http\Controllers\MenuController')->withoutMiddleware('auth:sanctum');
Route::resource('/order', 'App\Http\Controllers\OrderDetailController');
Route::resource('/cart', 'App\Http\Controllers\CartCon');
Route::resource('/aduser', 'App\Http\Controllers\AdminOptOnUser')->middleware([AdminMiddleware::class]);
Route::resource('/adproduct', 'App\Http\Controllers\AdminOptOnProd')->middleware([AdminMiddleware::class]);
Route::post("/directorder", 'App\Http\Controllers\DirectOrder@directorder');
Route::get('/products', 'App\Http\Controllers\products@index')->withoutMiddleware('auth:sanctum');
Route::get('/about',function(){
    return About::first()->description;
})->withoutMiddleware('auth:sanctum');
Route::post('/about',function(Request $request){
  $text = $request->text;
    $about = About::first();
    $about->description = $text;
    $about->save();
    return response()->json(["message"=>"About us updated successfully"]);
})->middleware([AdminMiddleware::class]);
Route::get('/r', function (Request $request) {
    return 2;
});
//de
Route::get("/allorders",function(){
    return OrderDetail::all();
})->middleware([AdminMiddleware::class]);
Route::get("/specificorder/{id}",function($id){
    return OrderDetail::where("id",$id)->get();
})->middleware([AdminMiddleware::class]);
Route::get("/specificuserorder/{id}",function($id){
    return OrderDetail::where("user_id",$id)->get();
})->middleware([AdminMiddleware::class]);


Route::get("/stat",function(){
    return response()->json([
        "totalusers"=>User::count(),
        "totalorders"=>OrderDetail::count(),
        "totalproducts"=>Product::count()
    ]);
})->middleware([AdminMiddleware::class]);









Route::post("/updproduct2/{id}",function(Request $request,$id){
    $request->validate([
        'image' => 'required|image',
       
    ]);
    $product = Product::findOrFail($id);

    if ($request->hasFile('image')) {
        // Delete the old image
        if ($product->image) {
            
            if (file_exists(public_path('images/' . $product->image))) {
               
                unlink(public_path('images/' . $product->image));
            }
        }
        $imagePath = time().'.'.$request->image->extension();  
        $request->image->move(public_path('images'), $imagePath);
        $product->image = $imagePath;
    }
    $product->save();
return asset("images/".$imagePath);
   
      
    
});