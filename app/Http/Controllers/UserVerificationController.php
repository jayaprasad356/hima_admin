<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersStoreRequest;
use App\Models\Users;
use App\Models\UserVerifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserVerificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     */
    public function verify(Request $request)
    {
        $verificationIds = $request->input('verification_ids', []);

        foreach ($verificationIds as $verificationId) {
            $voice_verifications = UserVerifications::find($verificationId);
            if ($voice_verifications) {
                // Update the withdrawal status to Paid (1)
                $voice_verifications->status = 2;
                $voice_verifications->save();
            }
        }

        return response()->json(['success' => true]);
    }

    public function reject(Request $request)
    {
        $verificationIds = $request->input('verification_ids', []);

        foreach ($verificationIds as $verificationId) {
            $voice_verifications = UserVerifications::find($verificationId);
            if ($voice_verifications) {
                // Update the withdrawal status to Paid (1)
                $voice_verifications->status = 3;
                $voice_verifications->save();
            }
        }

        return response()->json(['success' => true]);
    }
 

     public function index(Request $request)
     {
         $query = UserVerifications::query()->with(['user']); // Eager load the user
     
         // Filter by user if user_id is provided
         if ($request->has('user_id')) {
             $user_id = $request->input('user_id');
             $query->where('user_id', $user_id);
         }
     
         // Filter by search
         if ($request->has('search')) {
             $search = $request->input('search');
             $query->where(function ($query) use ($search) {
                 $query->where('id', 'like', "%{$search}%")
                       ->orWhereHas('user', function ($query) use ($search) {
                           $query->where('name', 'like', "%{$search}%");
                       });
             });
         }
        // Filter by verified status with default to 0
        $status = $request->input('status', 1); // Default to 0 if not provided
        $query->where('status', $status);
         // Check if the request is AJAX
         if ($request->wantsJson()) {
             return response()->json($query->get());
         }
     
         $users = $query->latest()->paginate(10);
     
         return view('user_verifications.index', compact('users')); // Pass user_verifications and users to the view
     }
     
    
     
}