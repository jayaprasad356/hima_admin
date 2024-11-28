<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users;
use App\Models\avatars;
use App\Models\coins;   
use App\Models\Transaction; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;


class AuthController extends Controller
{
 
    public function login(Request $request)
    {
        // Retrieve phone number from the request
        $mobile = $request->input('mobile');

        if (empty($mobile)) {
            $response['success'] = false;
            $response['message'] = 'mobile is empty.';
            return response()->json($response, 200);
        }
    
        // Remove non-numeric characters from the phone number
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
    
        // Check if the length of the phone number is not equal to 10
        if (strlen($mobile) !== 10) {
            $response['success'] = false;
            $response['message'] = "mobile number should be exactly 10 digits";
            return response()->json($response, 200);
        }
    
    
        // Check if a customer with the given phone number exists in the database
        $user = Users::where('mobile', $mobile)->first();
    
        // If customer not found, return failure response
        if (!$user) {
            $response['success'] = true;
            $response['registered'] = false;
            $response['message'] = 'mobile not registered.';
            return response()->json($response, 200);
        }


    return response()->json([
        'success' => true,
        'registered' => true,
        'message' => 'Logged in successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'language' => $user->language,
            'mobile' => $user->mobile,
            'avatar_id' => $user->avatar_id,
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 200);
}
public function register(Request $request)
{
    $mobile = $request->input('mobile'); 
    $language = $request->input('language');
    $name = $request->input('name');
    $avatar_id = $request->input('avatar_id');

    if (empty($mobile)) {
        $response['success'] = false;
        $response['message'] = 'mobile is empty.';
        return response()->json($response, 200);
    }

    // Validate mobile number format
    if (empty($mobile) || strlen($mobile) !== 10) {
        return response()->json([
            'success' => false,
            'message' => 'Mobile number should be 10 digits.',
        ], 200);
    }

    if (Users::where('mobile', $mobile)->exists()) {
        return response()->json([
            'success' => false,
            'message' => 'Mobile number is already registered.',
        ], 200); 
    }

    if (empty($language)) {
        return response()->json([
            'success' => false,
            'message' => 'Language is empty.',
        ], 200);
    }

    if (empty($avatar_id)) {
        return response()->json([
            'success' => false,
            'message' => 'avatar_id is empty.',
        ], 200);
    }

    $avatar = Avatars::find($avatar_id);

    if (!$avatar) {
        return response()->json([
            'success' => false,
            'message' => 'avatar not found.',
        ], 200);
    }


    if (empty($name)) {
        $name = $this->generateRandomName(); 
    }

    // Create a new User instance
    $user = new Users();
    $user->name = $name;
    $user->mobile = $mobile;
    $user->language = $language;
    $user->avatar_id = $avatar_id;
    $user->datetime = Carbon::now();
    $user->save(); 

    $userDetails = [
        'id' => $user->id,
        'name' => $user->name,
        'mobile' => $user->mobile,
        'language' => $user->language,
        'avatar_id' => $user->avatar_id,
        'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
        'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
    ];

    return response()->json([
        'success' => true,
        'message' => 'Registered successfully.',
        'data' => $userDetails,
    ], 200);
}
private function generateRandomName()
{
    $letters = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5);
    $numbers = substr(str_shuffle('0123456789'), 0, 3);
    return $letters . $numbers;
}

public function update_profile(Request $request)
{
    $user_id = $request->input('user_id');
    $avatar_id = $request->input('avatar_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'user not found.',
        ], 200);
    }
    if (empty($avatar_id)) {
        return response()->json([
            'success' => false,
            'message' => 'avatar_id is empty.',
        ], 200);
    }

    $user = Users::find($avatar_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'avatar_id not found.',
        ], 200);
    }

    $name = $request->input('name');

    if (!empty($name) && Users::where('name', $name)->where('id', '!=', $user_id)->exists()) {
        return response()->json([
            'success' => false,
            'message' => 'The provided name already exists.',
        ], 200);
    }


    // Update user details
    if ($name !== null) {
        $user->name = $name;
    }

    $user->datetime = now(); 
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'User details updated successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'language' => $user->language,
            'mobile' => $user->mobile,
            'avatar_id' => $user->avatar_id,
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 200);
}
public function userdetails(Request $request)
{
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }
   $avatar = Avatars::find($user->avatar_id);
   $gender = $avatar ? $avatar->gender : '';

    return response()->json([
        'success' => true,
        'message' => 'User details retrieved successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'avatar_id' => $user->avatar_id,
            'gender' => $gender,
            'language' => $user->language,
            'mobile' => $user->mobile ?? '',
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 200);
}


public function coins_list(Request $request)
{
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    $user = Users::find($user_id);
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    $coins = Coins::orderBy('price', 'asc')->get(); 

    // Check if coins data exists
    if ($coins->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No coins data available.',
        ], 200);
    }

    // Format coins data for response
    $coinsData = $coins->map(function ($coins) {
        return [
            'id' => $coins->id,
            'price' => $coins->price,
            'discount_price' => $coins->discount_price,
            'coins' => $coins->coins,
            'updated_at' => Carbon::parse($coins->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($coins->created_at)->format('Y-m-d H:i:s'),
        ];
    });

    // Return response with coin data
    return response()->json([
        'success' => true,
        'message' => 'Coins listed successfully.',
        'data' => $coinsData,
    ], 200);
}

public function transaction_list(Request $request)
{
    $user_id = $request->input('user_id');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    $transactions = Transaction::where('user_id', $user_id)
                 ->orderBy('datetime', 'desc')
                 ->get();

    if ($transactions->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No transactions found for this user.',
        ], 200);
    }

    $transactionsData = [];
    foreach ($transactions as $transaction) {
        $transactionsData[] = [
            'id' => $transaction->id,
            'user_id' => $transaction->user_id,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'coins' => $transaction->coins,
            'payment_type' => $transaction->payment_type,
            'datetime' => $transaction->datetime,
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'User Transaction listed successfully.',
        'data' => $transactionsData,
    ], 200);
}

public function avatar_list(Request $request)
{
    $gender = $request->input('gender'); 

       if (empty($gender)) {
        return response()->json([
            'success' => false,
            'message' => 'gender is empty.',
        ], 200);
    }

    if (empty($gender) || !in_array(strtolower($gender), ['male', 'female'])) {
        return response()->json([
            'success' => false,
            'message' => 'Gender must be either "male" or "female".',
        ], 200);
    }

    $avatars = Avatars::where('gender', strtolower($gender))->get();

    if ($avatars->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No avatars found for the specified gender.',
        ], 200);
    }

    $avatarData = [];
    foreach ($avatars as $avatar) {
        $imageUrl = $avatar->image ? asset('storage/app/public/avatars/' . $avatar->image) : '';
        $avatarData[] = [
            'id' => $avatar->id,
            'gender' => $avatar->gender,
            'image' => $imageUrl,
            'updated_at' => Carbon::parse($avatar->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($avatar->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Avatars listed successfully.',
        'data' => $avatarData,
    ], 200);
}

/*public function send_otp(Request $request)
{
    $mobile = $request->input('mobile'); 
    $country_code = $request->input('country_code');
    $otp = $request->input('otp');

    if (empty($mobile)) {
        $response['success'] = false;
        $response['message'] = 'Mobile is empty.';
        return response()->json($response, 200);
    }

    if (strlen($mobile) !== 10) {
        return response()->json([
            'success' => false,
            'message' => 'Mobile should be 10 digits.',
        ], 200);
    }

    if (empty($country_code)) {
        return response()->json([
            'success' => false,
            'message' => 'Country code is empty.',
        ], 200);
    }

    if (empty($otp)) {
        return response()->json([
            'success' => false,
            'message' => 'OTP is empty.',
        ], 200);
    }

    // Define the API URL and parameters for OTP sending
    $apiUrl = 'https://api.authkey.io/request'; 
    $authKey = '64045a300411033f'; // Your authkey here
    $sid = '14324'; // SID, if applicable

    // Make the HTTP request to the OTP API
    $response = Http::get($apiUrl, [
        'authkey' => $authKey,
        'mobile' => $mobile,
        'country_code' => $country_code,
        'sid' => $sid,
        'otp' => $otp,
    ]);

    // Check if the API request was successful
    if ($response->successful()) {
        // Parse the API response
        $apiResponse = $response->json();
        
        if ($apiResponse['status'] == 'success') {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => $apiResponse['message'] ?? 'Failed to send OTP.',
            ], 200);
        }
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Error communicating with OTP service.',
        ], 500);
    }
}
    */
}
