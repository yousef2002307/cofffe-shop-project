<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\ProductResource;
class AdminOptOnProd extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $products = Product::all();
            return response()->json(['products' => ProductResource::collection( $products)], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve products'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Create form not applicable'], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'stock' => 'required|integer',
            'image' => 'required|image',
            'price' => 'required|numeric',
        ]);

        try {
            if ($request->hasFile('image')) {
                $imagePath = time().'.'.$request->image->extension();  
                $request->image->move(public_path('images'), $imagePath);
        
            }
          

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'stock' => $request->stock,
                'image' => $imagePath,
                'price' => $request->price,
            ]);

            return response()->json(['product' => $product, 'message' => 'Product created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create product'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            return response()->json(['product' => new ProductResource( $product)], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve product'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return response()->json(['message' => 'Edit form not applicable'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'stock' => 'required|integer',
            'image' => 'nullable|image',
            'price' => 'required|numeric',
        ]);

        try {
            $product = Product::findOrFail($id);

            if ($request->hasFile('image')) {
                // Delete the old image
                if ($product->image) {
                    
                    if (file_exists(public_path('images/' . $product->image))) {
                       
                        unlink(public_path('images/' . $product->image));
                    }
                }
                $imagePath = $request->file('image')->store('images', 'public');
                $product->image = $imagePath;
            }

            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'stock' => $request->stock,
                'price' => $request->price,
            ]);

            return response()->json(['product' => $product, 'message' => 'Product updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update product'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            // Delete the image
            if ($product->image) {
                if (file_exists(public_path('images/' . $product->image))) {
                       
                    unlink(public_path('images/' . $product->image));
                }
            }
            $product->delete();

            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete product'], 500);
        }
    }
}
