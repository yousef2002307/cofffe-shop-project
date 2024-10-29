<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Routing\Controller as BaseController;
use Ramsey\Uuid\Type\Integer;

/**
 * @group 3 Menus
 *
 * APIs for managing menus
 */
class MenuController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }
    /**
     * Get a list of all menus
     *
     * No auth required
     *
     * @response 200 {
     *   "menus": [
     *     {
     *       "id": 1,
     *       "name": "Menu 1",
     *       "image": "images/menu1.jpg",
     *       "description": "Description of Menu 1"
     *     },
     *     {
     *       "id": 2,
     *       "name": "Menu 2",
     *       "image": "images/menu2.jpg",
     *       "description": "Description of Menu 2"
     *     }
     *   ],
     *   "lastMenu": {
     *     "id": 1,
     *     "name": "Menu 1",
     *     "image": "images/menu1.jpg",
     *     "description": "Description of Menu 1"
     *   }
     * }
     */
    public function index()
    {
        $menus = Menu::all();
        $lastMenu = Menu::latest()->first();

        return response()->json([
            'menus' => MenuResource::collection($menus),
            'lastMenu' => new MenuResource($lastMenu)
        ]);
    }

    /**
     * Create a new menu
     *
     * Admin auth required
     *
     * @bodyParam name string required The menu's name
     * @bodyParam image image required The menu's image
     * @bodyParam description string required The menu's description
     *
     * @response 201 {
     *   "message": "Menu created successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "Menu 1",
     *     "image": "images/menu1.jpg",
     *     "description": "Description of Menu 1"
     *   }
     * }
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'string|max:255|required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string',
        ]);

        // Handle the image upload
        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(public_path('images'), $imageName);
            $validatedData['image'] = $imageName;
        }

        // Assuming you have a Menu model
        $menu = new \App\Models\Menu();
        $menu->name = $validatedData['name'];
        $menu->image = $validatedData['image'];
        $menu->description = $validatedData['description'];
        $menu->save();

        return response()->json(['message' => 'Menu created successfully', "data" => new MenuResource($menu)], 201);
    }

    /**
     * Get a single menu by ID
     *
     * No auth required
     *
     * @param integer $id The menu's ID
     *
     * @response 200 {
     *   "id": 1,
     *   "name": "Menu 1",
     *   "image": "images/menu1.jpg",
     *   "description": "Description of Menu 1"
     * }
     */
    public function show(int $id)
    {
        $menu = Menu::findOrFail($id);
        return response()->json(new MenuResource($menu));
    }

    /**
     * Update a single menu by ID
     *
     * Admin auth required
     *
     * @param integer $id The menu's ID
     * @bodyParam name string required The menu's name
     * @bodyParam image image required The menu's image
     * @bodyParam description string required The menu's description
     *
     * @response 200 {
     *   "message": "Menu updated successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "Menu 1",
     *     "image": "images/menu1.jpg",
     *     "description": "Description of Menu 1"
     *   }
     * }
     */
    public function update(Request $request, string $id)
    {
        // TODO
    }

    /**
     * Delete a single menu by ID
     *
     * Admin auth required
     *
     * @param integer $id The menu's ID
     *
     * @response 200 {
     *   "message": "Menu deleted successfully"
     * }
     */
    public function destroy(int $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return response()->json(['message' => 'Menu deleted successfully']);
    }
}

