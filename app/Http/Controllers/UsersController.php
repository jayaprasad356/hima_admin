<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersStoreRequest;
use App\Models\Users;
use App\Models\avatars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Users::query();

        if ($request->has('search') && $request->input('search') !== '') {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('mobile', 'LIKE', "%$search%");
            });
        }
          
        // Check if the request is AJAX
        if ($request->wantsJson()) {
            return response($query->get());
        }

        // Retrieve all users if there's no search query
        $users = $query->latest()->paginate(10);
        $avatars = Avatars::all();
     
        return view('users.index', compact('users', 'avatars'));
    }
    public function avatars()
{
    return $this->belongsTo(Avatars::class, 'avatar_id');
}


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Users $users
     * @return \Illuminate\Http\Response
     */
    public function edit(Users $users)
    {
        $avatars = Avatars::pluck('gender', 'id'); // Replace 'profession' with the actual field name
        return view('users.edit', compact('users', 'avatars'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Users $users)
    {
        $users->name = $request->name;
        $users->avatar_id = $request->avatar_id;
        $users->mobile = $request->mobile;
        $users->language = $request->language; 
        $users->age = $request->age;
        $users->interests = $request->interests;
        $users->describe_yourself = $request->describe_yourself;
        $users->voice = $request->voice; 
        $users->audio_status = $request->audio_status;
        $users->video_status = $request->video_status; 
        $users->datetime = now();
   
    
        if (!$users->save()) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the customer.');
        }
        return redirect()->route('users.edit', $users->id)->with('success', 'Success, User has been updated.');
    }

    public function addCoinsForm($id)
    {
        $user = Users::find($id);

        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User not found.');
        }

        return view('users.add_coins', compact('user'));
    }
    
    public function addCoins(Request $request, $id)
    {
        $request->validate([
            'coins' => 'required|integer|min:1',
        ]);
    
        $user = Users::find($id);
    
        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User not found.');
        }
    
        $user->coins += $request->input('coins');
        $user->total_coins += $request->input('coins');
        $user->save();
    
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'type' => 'add_coins',
            'coins' => $request->input('coins'),
            'payment_type' => 'Credit',
            'datetime' => now(),
        ]);
      
        return redirect()->route('users.index')->with('success', 'Coins added successfully.');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function destroy(Users $users)
    {
        // Delete the user record
        $users->delete();
    
        // Return a JSON response indicating success
        return response()->json([
            'success' => true
        ]);
    }
}
