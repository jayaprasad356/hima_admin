<?php
namespace App\Http\Controllers;

use App\Http\Requests\ChatsStoreRequest;
use App\Models\Chats;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class ChatsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Chats::query()->with('user'); // Eager load the user relationship

        // Filter by user if user_id is provided
        if ($request->has('user_id')) {
            $user_id = $request->input('user_id');
            $query->where('user_id', $user_id);
        }

        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%$search%");
                  });
            });
        }

           // Check if the request is AJAX
           if ($request->wantsJson()) {
            return response($query->get());

        }

        $chats = $query->latest()->paginate(10); // Paginate the results

        $users = Users::all(); // Fetch all users for the filter dropdown

        return view('chats.index', compact('chats', 'users')); // Pass trips and users to the view
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = Users::all(); // Fetch all users
        return view('chats.create', compact('users')); // Pass users to the view
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ChatsStoreRequest $request)
    {
        $chats = Chats::create([
            'latest_message' => $request->latest_message,
            'user_id' => $request->user_id,
            'datetime' => now(),
        ]);

        if (!$chats) {
            return redirect()->back()->with('error', 'Sorry, something went wrong while creating the chat.');
        }

        return redirect()->route('chats.index')->with('success', 'Success, new chat has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Chats  $chats
     * @return \Illuminate\Http\Response
     */
    public function show(Chats $chats)
    {
        // Implement show logic if needed
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Chats  $chats
     * @return \Illuminate\Http\Response
     */
    public function edit(Chats $chats)
    {
        $users = Users::all(); // Fetch all users
        return view('chats.edit', compact('chats', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chats  $chats
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chats $chats)
    {
        $chats->latest_message = $request->latest_message;
        $chats->user_id = $request->user_id;
        $chats->datetime = now();

        if (!$chats->save()) {
            return redirect()->back()->with('error', 'Sorry, something went wrong while updating the chat.');
        }

        return redirect()->route('chats.edit', $chats->id)->with('success', 'Success, Chats has been updated.');
    }

    public function destroy(Chats $chats)
    {
        $chats->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}

