<?php

namespace App\Http\Controllers;

use App\Models\Coins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\coinsStoreRequest;

class CoinsController extends Controller
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
                Coins::all()
            );
        }
        $coins = Coins::latest()->paginate(10);
        return view('coins.index')->with('coins', $coins);
    }
       /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('coins.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function store(coinsStoreRequest $request)
    {

        $coins = Coins::create([
            'price' => $request->price,
            'coins' => $request->coins,
            'save' => $request->save,
            'popular' => $request->popular,
        ]);

        if (!$coins) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating profession.');
        }
        return redirect()->route('coins.index')->with('success', 'Success, New Coins has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(coins $coins)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Coins $coins)
    {
        return view('coins.edit', compact('coins'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coins $coins)
    {
        $coins->price = $request->price;
        $coins->coins = $request->coins;
        $coins->save = $request->save;
        $coins->popular = $request->popular;


        if (!$coins->save()) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the profession.');
        }
        return redirect()->route('coins.edit', $coins->id)->with('success', 'Success, Coins has been updated.');
    }

    public function destroy(Coins $coins)
    {
       
        $coins->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
