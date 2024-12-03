<?php

namespace App\Http\Controllers;

use App\Models\speech_texts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\speech_textsStoreRequest;

class SpeechTextController extends Controller
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
                speech_texts::all()
            );
        }
        $speech_texts = speech_texts::latest()->paginate(10);
        return view('speech_texts.index')->with('speech_texts', $speech_texts);
    }
       /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('speech_texts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function store(speech_textsStoreRequest $request)
    {

        $speech_texts = speech_texts::create([
            'text' => $request->text,
            'language' => $request->language,
        ]);

        if (!$speech_texts) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while creating profession.');
        }
        return redirect()->route('speech_texts.index')->with('success', 'Success, New Speech Text has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(speech_texts $speech_texts)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(speech_texts $speech_texts)
    {
        return view('speech_texts.edit', compact('speech_texts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, speech_texts $speech_texts)
    {
        $speech_texts->text = $request->text;
        $speech_texts->language = $request->language;


        if (!$speech_texts->save()) {
            return redirect()->back()->with('error', 'Sorry, Something went wrong while updating the profession.');
        }
        return redirect()->route('speech_texts.edit', $speech_texts->id)->with('success', 'Success, Speech Text has been updated.');
    }

    public function destroy(speech_texts $speech_texts)
    {
       
        $speech_texts->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
