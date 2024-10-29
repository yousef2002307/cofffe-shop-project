<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Functions\CartFunction;
use Illuminate\Support\Facades\Auth;

/**
 * @group 1 Security
 *
 * APIs for user registration and login and logout
 */
class SecuirtyController extends Controller
{
    /**
     * Register a new user
     *
     * @bodyParam name string required The user's name
     * @bodyParam email string required The user's email
     * @bodyParam password string required The user's password
     * @bodyParam phone string required The user's phone number
     *
     * @response 201 {
     *   "message": "User registered successfully"
     * }
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:15',
        ]);

        $user = new \App\Models\User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        $user->save();

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    /**
     * Login a user
     *
     * @bodyParam email string required The user's email
     * @bodyParam password string required The user's password
     *
     * @response 200 {
     *   "token": "string"
     * }
     * @response 401 {
     *   "message": "Invalid credentials"
     * }
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Assuming you are using Laravel Passport or Sanctum for API authentication
        $token = $user->createToken('authToken')->plainTextToken;
        
        /**
         * Deletes all carts that have passed two days.
         *
         * This function utilizes the CartFunction class to remove all carts
         * that have been inactive for more than two days.
         *
         * @return void
         */
        $cartfunction = (new CartFunction)->deleteallcartsthatpassedtwodayson();
        return response()->json(['token' => $token], 200);
    }

    /**
     * Logout a user
     *
     * @response 200 {
     *   "message": "Logged out"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }


    public function updateprofile(Request $request){
        $id = Auth::user()->id;
        try {
           
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|min:8',
            ]);
           
            $user = User::findOrFail($id);
         
            if(!$user){
                return response()->json(['error' => 'User not found'], 404);
            }
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            if (!empty($validatedData['password'])) {
                $user->password = bcrypt($validatedData['password']);
            }
            $user->save();

            return response()->json(['success' => 'User updated successfully', 'user' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update user'], 500);
        }

    }
}

