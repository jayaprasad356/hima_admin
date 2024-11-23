<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersStoreRequest;
use App\Models\Users;
use App\Models\Professions;
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
                  ->orWhere('email', 'LIKE', "%$search%")
                  ->orWhere('unique_name', 'LIKE', "%$search%");
            });
        }
          
           // Filter by verified status
    if ($request->filled('verified')) {
        $verified = $request->input('verified');
        $query->where('verified', $verified);
    }
        // Check if the request is AJAX
        if ($request->wantsJson()) {
            return response($query->get());
        }

        // Retrieve all users if there's no search query
        $users = $query->latest()->paginate(10);
        $professions = Professions::all();
     
        return view('users.index', compact('users', 'professions'));
    }
    public function profession()
{
    return $this->belongsTo(Professions::class, 'profession_id');
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
          $professions = Professions::pluck('profession', 'id'); // Pluck only 'profession' field
        return view('users.create', compact('professions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UsersStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    private function generateReferCode()
    {
        // Generate a random string
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
        shuffle($characters);
        $refer_code = implode('', array_slice($characters, 0, 6));

        // Ensure the refer_code is unique
        while (Users::where('refer_code', $refer_code)->exists()) {
            shuffle($characters);
            $refer_code = implode('', array_slice($characters, 0, 6));
        }

        return $refer_code;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users',
           'profession_id' => 'required|integer|exists:professions,id',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'age' => 'required|integer|between:18,60',
            'gender' => 'required|in:male,female,others',
            'profile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'email.unique' => 'The email has already been taken.',
            'age.between' => 'The age must be between 18 and 60.',
            'gender.in' => 'Invalid gender selected.',
            'profile.image' => 'The profile must be an image file.',
            'profile.mimes' => 'The profile must be a file of type: jpeg, png, jpg, gif.',
        ]);
        // Generate a refer_code regardless of whether referred_by is provided or not
        $refer_code = $this->generateReferCode();
    
        // Validate the referred_by field if provided
        if ($request->filled('referred_by')) {
            $existingUser = Users::where('refer_code', $request->referred_by)->first();
    
            if (!$existingUser) {
                return redirect()->back()->with('error', 'Invalid referred_by. Please provide a valid refer code.');
            }
        }
    
   // Check if a file has been uploaded
   if ($request->hasFile('profile')) {
    $imageName = $request->file('profile')->getClientOriginalName(); // Get the original file name
    $imagePath = $request->file('profile')->storeAs('users', $imageName);
} else {
    // Handle the case where no file has been uploaded
    $imagePath = null; // or provide a default image path
}

// Check if a file has been uploaded for cover image
if ($request->hasFile('cover_img')) {
    $coverImageName = $request->file('cover_img')->getClientOriginalName(); // Get the original cover image name
    $coverImagePath = $request->file('cover_img')->storeAs('users', $coverImageName); // Store cover image
} else {
    // Handle the case where no file has been uploaded for cover image
    $coverImagePath = null; // or provide a default cover image path
}
    
        $users = Users::create([
            'name' => $request->name,
            'age' => $request->age,
            'email' => $request->email,
            'address' => $request->address,
            'gender' => $request->gender,
            'state' => $request->state,
            'city' => $request->city,
            'profession_id' => $request->profession_id,
            'refer_code' => $refer_code,
            'referred_by' => $request->referred_by,
            'dummy' => $request->dummy,
            'message_notify' => $request->message_notify,
            'add_friend_notify' => $request->add_friend_notify,
            'view_notify' => $request->view_notify,
            'introduction' => $request->introduction,
            'language' => $request->language,
            'profile' => $imageName, // Save only the image name in the database
            'datetime' => now(),
            'last_seen' => now(),
        ]);

             // Create a new transaction in Fakechats table
             \App\Models\Fakechats::create([
                'user_id' => $users->id,
                'status' => '0',
            ]);
    
        if (!$users) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating user.');
        }
    
        // Generate the unique_name after the user has been saved and has an ID
        $unique_name = $this->generateUniqueName($request->name, $users->id);
        $users->unique_name = $unique_name;
        $users->save();
    
        return redirect()->route('users.index')->with('success', 'Success, New user has been added successfully!');
    }
    // Method to generate a unique name based on the user's name and user_id
private function generateUniqueName($name, $user_id)
{
    // Extract the first part of the user's name
    $parts = explode(' ', $name);
    $firstPart = $parts[0];

    // Generate the unique name by concatenating the first part with the user_id
    $unique_name = $firstPart . $user_id;

    // Check if the generated unique_name is already in use
    $counter = 1;
    while (Users::where('unique_name', $unique_name)->exists()) {
        // If it is, append a counter to make it unique
        $unique_name = $firstPart . $user_id . $counter;
        $counter++;
    }

    return $unique_name;
}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Users $users
     * @return \Illuminate\Http\Response
     */
    public function edit(Users $users)
    {
        $professions = Professions::pluck('profession', 'id'); // Replace 'profession' with the actual field name
        return view('users.edit', compact('users', 'professions'));
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
    
        $validatedData = $request->validate([
            'unique_name' => 'required|string|max:255|unique:users,unique_name,' . $users->id,
            'name' => 'required|string|max:255',
            'age' => 'required|integer|between:18,60',
            'email' => 'required|email|unique:users,email,' . $users->id,
            'gender' => 'required|in:male,female,others',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'profession_id' => 'required|integer|exists:professions,id',
            'refer_code' => 'nullable|string|max:255',
            'referred_by' => 'nullable|string|max:255',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover_img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Corrected field name from 'cover_image' to 'cover_img'
            'verified' => 'required|boolean',
            // other validation rules...
        ], [
            'unique_name.unique' => 'The unique name has already been taken.',
            'email.unique' => 'The email has already been taken.',
        
            'age.between' => 'The age must be between 18 and 60.',
            'gender.in' => 'Invalid gender selected.',
            'profile.image' => 'The profile must be an image file.',
            'profile.mimes' => 'The profile must be a file of type: jpeg, png, jpg, gif.',
            'profile.max' => 'The profile may not be greater than 2 MB.',
            'cover_img.image' => 'The Cover Image must be an image file.', // Corrected field name from 'cover_image' to 'cover_img'
            'cover_img.mimes' => 'The Cover Image must be a file of type: jpeg, png, jpg, gif.', // Corrected field name from 'cover_image' to 'cover_img'
            // custom error messages for other validation rules...
        ]);
    
    
        $users->name = $request->name;
        $users->unique_name = $request->unique_name;
        $users->age = $request->age;
        $users->email = $request->email;
        $users->gender = $request->gender;
        $users->state = $request->state;
        $users->city = $request->city;
        $users->profession_id = $request->profession_id;
        $users->refer_code = $request->refer_code;
        $users->referred_by = $request->referred_by;
        $users->verified = $request->verified;
        $users->dummy = $request->dummy;
        $users->message_notify = $request->message_notify;
        $users->add_friend_notify = $request->add_friend_notify;
        $users->view_notify = $request->view_notify;
        $users->introduction = $request->introduction;
        $users->online_status = $request->online_status;
        $users->profile_verified = $request->profile_verified;
        $users->cover_img_verified = $request->cover_img_verified;
        $users->points = $request->points;
        $users->language = $request->language;
        $users->total_points = $request->total_points;
        $users->verification_end_date = $request->verification_end_date;
        $users->datetime = now();
        $users->last_seen = now();
    
        if ($request->hasFile('profile')) {
            $newImagePath = $request->file('profile')->store('users', 'public');
            Storage::disk('public')->delete('users/' . $users->profile);
            $users->profile = basename($newImagePath);
        }
    
        if ($request->hasFile('cover_img')) {
            $newImagePath = $request->file('cover_img')->store('users', 'public');
            Storage::disk('public')->delete('users/' . $users->cover_img);
            $users->cover_img = basename($newImagePath);
        }
    
        if (!$users->save()) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the customer.');
        }
        return redirect()->route('users.edit', $users->id)->with('success', 'Success, User has been updated.');
    }

    public function addPointsForm($id)
    {
        $user = Users::find($id);

        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User not found.');
        }

        return view('users.add_points', compact('user'));
    }

    public function addPoints(Request $request, $id)
    {
        $request->validate([
            'points' => 'required|integer|min:1',
        ]);
    
        $user = Users::find($id);
    
        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User not found.');
        }
    
        $user->points += $request->input('points');
        $user->total_points += $request->input('points');
        $user->save();
    
        // Create a new transaction
        \App\Models\Transaction::create([
            'user_id' => $user->id,
            'type' => 'recharge',
            'points' => $request->input('points'),
            'datetime' => now(),
        ]);
      
        return redirect()->route('users.index')->with('success', 'Points added successfully.');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Users  $users
     * @return \Illuminate\Http\Response
     */
    public function destroy(Users $users)
    {
        // Check if the profile image exists and delete it
        if (Storage::disk('public')->exists('users/' . $users->profile)) {
            Storage::disk('public')->delete('users/' . $users->profile);
        }
    
        // Check if the cover image exists and delete it
        if (Storage::disk('public')->exists('users/' . $users->cover_img)) {
            Storage::disk('public')->delete('users/' . $users->cover_img);
        }
    
        // Delete the user record
        $users->delete();
    
        // Return a JSON response indicating success
        return response()->json([
            'success' => true
        ]);
    }
}
