<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\Wallets;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletsController extends Controller
{
    public function index(Request $request)
    {
        $query = Wallets::query()->with('user'); // Eager load the user relationship

         // Filter by user if user_id is provided
         if ($request->has('user_id')) {
            $user_id = $request->input('user_id');
            $query->where('user_id', $user_id);
        }
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                      ->orWhere('balance', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($query) use ($search) {
                          $query->where('name', 'like', "%{$search}%");
                      });
            });
        }
        $wallets = $query->latest()->paginate(10); // Paginate the results

        $users = Users::all(); // Fetch all users for the filter dropdown

        return view('wallets.index', compact('wallets', 'users')); // Pass friends and users to the view
    }

    public function destroy(Wallets $wallets)
    {
        $wallets->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}

