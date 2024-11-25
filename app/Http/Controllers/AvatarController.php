<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvatarStoreRequest;
use App\Models\avatars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson()) {
            return response(
                avatars::all()
            );
        }
        $avatars = avatars::latest()->paginate(10);
        return view('avatars.index')->with('avatars', $avatars);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('avatars.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AvatarStoreRequest $request)
    {
        $imagePath = $request->file('image')->store('avatars', 'public');

        $avatars = avatars::create([
            'image' => basename($imagePath),
            'gender' => $request->gender,
        ]);

        if (!$avatars) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating shop.');
        }
        return redirect()->route('avatars.index')->with('success', 'Success, New Avatar has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Shops  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(avatars $avatars)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Slides  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(avatars $avatars)
    {
        return view('avatars.edit', compact('avatars'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shops  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, avatars $avatars)
    {
        $avatars->gender = $request->gender;

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('avatars', 'public');
            // Delete old image if it exists
            Storage::disk('public')->delete('avatars/' . $avatars->image);
            $avatars->image = basename($newImagePath);
        }

        if (!$avatars->save()) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the customer.');
        }
        return redirect()->route('avatars.index')->with('success', 'Success, The Avatars has been updated.');
    }

    public function destroy(avatars $avatars)
    {
        if (Storage::disk('public')->exists('avatars/' . $avatars->image)) {
            Storage::disk('public')->delete('avatars/' . $avatars->image);
        }
        $avatars->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
