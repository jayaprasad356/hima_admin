<?php

namespace App\Http\Controllers;

use App\Models\speech_texts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function destroy(speech_texts $speech_texts)
    {
       
        $speech_texts->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
