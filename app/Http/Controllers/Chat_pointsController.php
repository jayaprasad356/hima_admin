<?php
namespace App\Http\Controllers;

use App\Http\Requests\Chat_pointsStoreRequest;
use App\Models\Chat_points;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class Chat_pointsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Chat_points::query()
            ->with('user') // Load user relationship for user_id
            ->with('chatUser'); // Load user relationship for chat_user_id

        // Filter by user if user_id is provided
        if ($request->has('user_id')) {
            $user_id = $request->input('user_id');
            $query->where('user_id', $user_id);
        }

        // Filter by chat_user_id if provided
        if ($request->has('chat_user_id')) {
            $chat_user_id = $request->input('chat_user_id');
            $query->where('chat_user_id', $chat_user_id);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                      ->orWhere('points', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($query) use ($search) {
                          $query->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('chatUser', function ($query) use ($search) {
                          $query->where('name', 'like', "%{$search}%");
                      });
            });
        }

           // Check if the request is AJAX
           if ($request->wantsJson()) {
            return response($query->get());

        }

        $chat_points = $query->latest()->paginate(10); // Paginate the results

        $users = Users::all(); // Fetch all users for the filter dropdown

        return view('chat_points.index', compact('chat_points', 'users')); // Pass trips and users to the view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = Users::all(); // Fetch all users
        return view('chat_points.create', compact('users')); // Pass users to the view
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   

    public function destroy(Chat_points $chat_points)
    {
        $chat_points->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}

