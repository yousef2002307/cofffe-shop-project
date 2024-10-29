<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;
/**
 * @group 2 Products
 *
 * APIs for viewing products
 */
class products extends Controller
{
    /**
     * Get a paginated list of products
     *
     * No auth required.
     * If the quantity of a product is 0 or less, you cannot buy or add it to the cart.
     * Pagination: add ?page parameter to the request.
     *
     * @response 200 
     *   [
     *    {
     *      "name": "Product 1",
     *      "description": "Description of Product 1",
     *      "price": "10.00 $",
     *      "quantity": 100
     *    },
     *    {
     *      "name": "Product 2",
     *      "description": "Description of Product 2",
     *      "price": "20.00 $",
     *      "quantity": 50
     *    }
     *  ],
     *
     * @response 404 {
     *  "message": "No products found"
     * }
     */
    public function index()
    {
        $products = Product::paginate(10);

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No products found'], 404);
        }

        return response()->json(ProductResource::collection($products));
    }
}
