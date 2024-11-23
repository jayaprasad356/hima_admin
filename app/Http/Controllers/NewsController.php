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
            'telegram' => 'required|string|max:255',
            'instagram' => 'required|string|max:255',
            'upi_id' => 'required|string|max:255',
            'privacy_policy' => 'required|string',
            'terms_conditions' => 'required|string',
            'refund_policy' => 'required|string',
            'recharge_points' => 'required|string',
            'verification_cost' => 'required|string',
            'without_verification_cost' => 'required|string',
        ]);

        $news = News::findOrFail(1); // Again, assuming ID 1 for simplicity
        $news->telegram = $request->input('telegram');
        $news->instagram = $request->input('instagram');
        $news->upi_id = $request->input('upi_id');
        $news->privacy_policy = $request->input('privacy_policy');
        $news->terms_conditions = $request->input('terms_conditions');
        $news->refund_policy = $request->input('refund_policy');
        $news->recharge_points = $request->input('recharge_points');
        $news->verification_cost = $request->input('verification_cost');
        $news->without_verification_cost = $request->input('without_verification_cost');
    

        if ($news->save()) {
            return redirect()->route('news.edit')->with('success', 'Success, Settings has been updated.');
        } else {
            return redirect()->route('news.edit')->with('error', 'Sorry, something went wrong while updating the News.');
        }
    }
}



