<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function edit()
    {
        // Assuming you are editing the News with ID 1
        $news = News::findOrFail(1);
        return view('news.edit', compact('news'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'privacy_policy' => 'required|string',
            'support_mail' => 'required|string',
        ]);

        $news = News::findOrFail(1); // Again, assuming ID 1 for simplicity
        $news->privacy_policy = $request->input('privacy_policy');
        $news->support_mail = $request->input('support_mail');
    

        if ($news->save()) {
            return redirect()->route('news.edit')->with('success', 'Success, Settings has been updated.');
        } else {
            return redirect()->route('news.edit')->with('error', 'Sorry, something went wrong while updating the News.');
        }
    }
}



