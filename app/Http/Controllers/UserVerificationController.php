<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsersStoreRequest;
use App\Models\Users;
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
             $user = Users::find($verificationId);
             if ($user) {
                 // Update the withdrawal status to Paid (1)
                 $user->verified = 1;
                 $user->save();
             }
         }
 
         return response()->json(['success' => true]);
     }

     public function reject(Request $request)
{
    $verificationIds = $request->input('verification_ids', []);

    foreach ($verificationIds as $verificationId) {
        $user = Users::find($verificationId);
        if ($user) {
            // Change verification_status to Rejected (2)
            $user->verified = 2;
            if (Storage::disk('public')->exists('users/' . $user->selfi_image)) {
                Storage::disk('public')->delete('users/' . $user->selfi_image);
            }
            if (Storage::disk('public')->exists('users/' . $user->proof_image)) {
                Storage::disk('public')->delete('users/' . $user->proof_image);
            }
            $user->selfi_image = ''; // Clear the image fields
            $user->proof_image = '';
            $user->save();
        }
    }

    return response()->json(['success' => true]);
}

     public function index(Request $request)
     {
        $query = Users::whereNotNull('selfi_image')
            ->where('selfi_image', '!=', '')
        ->whereNotNull('proof_image')
           ->where('proof_image', '!=', '')
        ->where('verified', 0); // Filter by verified status

           
         // Apply search filter if provided
         if ($request->has('search') && $request->input('search') !== '') {
             $search = $request->input('search');
             $query->where(function($q) use ($search) {
                 $q->where('name', 'LIKE', "%$search%")
                   ->orWhere('mobile', 'LIKE', "%$search%");
             });
         }
     
         // Check if the request is AJAX and return a JSON response
         if ($request->wantsJson()) {
             return response()->json($query->get());
         }
     
         // Retrieve all users who have both images if there's no search query
         $users = $query->latest()->paginate(10);
     
         return view('user_verifications.index', compact('users'));
     }
     
     
}