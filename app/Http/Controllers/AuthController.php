<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users;
use App\Models\avatars;
use App\Models\coins;
use App\Models\speech_texts;   
use App\Models\Transaction;
use App\Models\DeletedUsers; 
use App\Models\Withdrawals;  
use Carbon\Carbon;
use App\Models\News; 
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
        $avatar = Avatars::find($user->avatar_id);
        $gender = $avatar ? $avatar->gender : '';
     
        $imageUrl = $avatar->image ? asset('storage/app/public/avatars/' . $avatar->image) : '';
        $voicePath = $user && $user->voice ? asset('storage/app/public/voices/' . $user->voice) : '';

    return response()->json([
        'success' => true,
        'registered' => true,
        'message' => 'Logged in successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'user_gender' => $user->gender,
            'language' => $user->language,
            'mobile' => $user->mobile,
            'avatar_id' => $user->avatar_id,
            'image' => $imageUrl,
            'gender' => $gender,
            'interests' => $user->interests ?? '',
            'describe_yourself' => $user->describe_yourself ?? '',
            'voice' => $voicePath ?? '',
            'status' => $user->status ?? '',
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
    $gender = $request->input('gender');
    $age = $request->input('age');
    $interests = $request->input('interests');
    $describe_yourself = $request->input('describe_yourself');

    // Check if mobile number is empty or invalid
    if (empty($mobile)) {
        return response()->json([
            'success' => false,
            'message' => 'Mobile is empty.',
        ], 200);
    }

    if (strlen($mobile) !== 10) {
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

    // Check if language is empty
    if (empty($language)) {
        return response()->json([
            'success' => false,
            'message' => 'Language is empty.',
        ], 200);
    }

    // Check if avatar ID is empty
    if (empty($avatar_id)) {
        return response()->json([
            'success' => false,
            'message' => 'Avatar ID is empty.',
        ], 200);
    }

    $avatar = Avatars::find($avatar_id);

    if (!$avatar) {
        return response()->json([
            'success' => false,
            'message' => 'Avatar not found.',
        ], 200);
    }

    if (empty($gender)) {
        return response()->json([
            'success' => false,
            'message' => 'Gender is empty.',
        ], 200);
    }

    if (!in_array(strtolower($gender), ['male', 'female'])) {
        return response()->json([
            'success' => false,
            'message' => 'Gender must be either male or female.',
        ], 200);
    }

    if (strtolower($gender) === 'female') {
        if (empty($age)) {
            return response()->json([
                'success' => false,
                'message' => 'Age is required for female users.',
            ], 200);
        }
        if (empty($interests)) {
            return response()->json([
                'success' => false,
                'message' => 'Interests are required for female users.',
            ], 200);
        }
        if (empty($describe_yourself)) {
            return response()->json([
                'success' => false,
                'message' => 'Describe Yourself is required for female users.',
            ], 200);
        }
    }

    // Generate a random name if not provided
    if (empty($name)) {
        $name = $this->generateRandomName(); 
    }

    // Create a new User instance
    $user = new Users();
    $user->name = $name;
    $user->mobile = $mobile;
    $user->language = $language;
    $user->avatar_id = $avatar_id;
    $user->gender = $gender;
    $user->age = $age;
    $user->interests = $interests;
    $user->describe_yourself = $describe_yourself;
    $user->datetime = Carbon::now();
    $user->save(); 

    $avatar = Avatars::find($user->avatar_id);
    $imageUrl = $avatar ? asset('storage/app/public/avatars/' . $avatar->image) : '';
    $voicePath = $user && $user->voice ? asset('storage/app/public/voices/' . $user->voice) : '';

    $userDetails = [
        'id' => $user->id,
        'name' => $user->name,
        'user_gender' => $user->gender,
        'mobile' => $user->mobile,
        'language' => $user->language,
        'avatar_id' => $user->avatar_id,
        'image' => $imageUrl,
        'gender' => $gender,
        'age' => $user->age,
        'interests' => $user->interests,
        'describe_yourself' =>  $user->describe_yourself ?? '',
        'voice' =>  $voicePath ?? '',
        'status' => 0,
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
    $interests = $request->input('interests');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    if (empty($interests)) {
        return response()->json([
            'success' => false,
            'message' => 'interests is empty.',
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

    $avatar = Avatars::find($avatar_id);

    if (!$avatar) {
        return response()->json([
            'success' => false,
            'message' => 'avatar not found.',
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
    $user->interests = $interests;
    $user->avatar_id = $avatar_id;
    $user->datetime = now(); 
    $user->save();

    $avatar = Avatars::find($user->avatar_id);
   $gender = $avatar ? $avatar->gender : '';

   $imageUrl = $avatar->image ? asset('storage/app/public/avatars/' . $avatar->image) : '';
   $voicePath = $user && $user->voice ? asset('storage/app/public/voices/' . $user->voice) : '';

    return response()->json([
        'success' => true,
        'message' => 'User details updated successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'user_gender' => $user->gender,
            'language' => $user->language,
            'mobile' => $user->mobile,
            'avatar_id' => $user->avatar_id,
            'image' => $imageUrl,
            'gender' => $gender,
             'age' => $user-> age ?? '',
            'interests' => $user->interests,
            'describe_yourself' => $user-> describe_yourself ?? '',
             'voice' => $voicePath ?? '',
             'status' => $user->status ?? '',
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

   $imageUrl = $avatar->image ? asset('storage/app/public/avatars/' . $avatar->image) : '';
   $voicePath = $user && $user->voice ? asset('storage/app/public/voices/' . $user->voice) : '';
    return response()->json([
        'success' => true,
        'message' => 'User details retrieved successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'user_gender' => $user->gender,
            'avatar_id' => $user->avatar_id,
            'image' => $imageUrl,
            'gender' => $gender,
            'language' => $user->language,
            'age' => $user-> age ?? '',
            'mobile' => $user->mobile ?? '',
            'interests' => $user->interests ?? '',
            'describe_yourself' => $user-> describe_yourself ?? '',
            'voice' => $voicePath ?? '',
            'status' => $user->status ?? '',
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
            'amount' => $transaction->amount ?? '', 
            'coins' => $transaction->coins,
            'payment_type' => $transaction->payment_type ?? '',
            'datetime' => $transaction->datetime, // Full datetime (with time)
            'date' => Carbon::parse($transaction->datetime)->format('M d'), // Format: "Nov 29"
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

public function send_otp(Request $request)
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

    if ($response->successful()) {
        // Parse the API response
        $apiResponse = $response->json();
    
        if ($apiResponse['Message'] == 'Submitted Successfully') {
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => $apiResponse['Message'] ?? 'Failed to send OTP.',
            ], 200);
        }
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Error communicating with OTP service.',
        ], 500);
    }
}
public function settings_list(Request $request)
{
    // Retrieve all news settings
    $news = News::all();

    if ($news->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No settings found.',
        ], 200);
    }

    $newsData = [];
    foreach ($news as $item) {
        $newsData[] = [
            'id' => $item->id,
            'privacy_policy' => $item->privacy_policy,
            'support_mail' => $item->support_mail,
            'demo_video' => $item->demo_video,
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Settings listed successfully.',
        'data' => $newsData,
    ], 200);
}
public function delete_users(Request $request)
{
    $user_id = $request->input('user_id');
    $delete_reason = $request->input('delete_reason');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    if (empty($delete_reason)) {
        return response()->json([
            'success' => false,
            'message' => 'delete_reason is empty.',
        ], 200);
    }


    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    $deletedUser = new DeletedUsers();
    $deletedUser->user_id = $user->id;
    $deletedUser->name = $user->name;
    $deletedUser->mobile = $user->mobile;
    $deletedUser->language = $user->language;
    $deletedUser->avatar_id = $user->avatar_id;
    $deletedUser->coins = $user->coins;
    $deletedUser->total_coins = $user->total_coins;
    $deletedUser->datetime = Carbon::now();
    $deletedUser->delete_reason = $delete_reason;
    $deletedUser->save();

    $user->delete();

    return response()->json([
        'success' => true,
        'message' => 'User deleted successfully.',
    ], 200);
}

public function user_validations(Request $request)
{
    $user_id = $request->input('user_id');
    $name = $request->input('name');

    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    if (empty($name)) {
        return response()->json([
            'success' => false,
            'message' => 'name is empty.',
        ], 200);
    }

    if (strlen($name) < 4 || strlen($name) > 10) {
        return response()->json([
            'success' => false,
            'message' => 'Name must be between 4 and 10 characters.',
        ], 200);
    }

    if (!preg_match('/^[a-zA-Z0-9]+$/', $name)) {
        return response()->json([
            'success' => false,
            'message' => 'Name can only contain letters (a-z) and numbers (0-9).',
        ], 200);
    }

    if (preg_match('/\d{3,}/', $name)) {
        return response()->json([
            'success' => false,
            'message' => 'Name cannot contain 3 or more consecutive numbers.',
        ], 200);
    }

    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    if (Users::where('name', $name)->where('id', '!=', $user_id)->exists()) {
        return response()->json([
            'success' => false,
            'message' => 'The provided name already exists.',
        ], 200);
    }


    $user->name = $name;
    $user->datetime = now(); 
    $user->save();


    $avatar = Avatars::find($user->avatar_id);
    $gender = $avatar ? $avatar->gender : '';


    $imageUrl = $avatar && $avatar->image 
        ? asset('storage/app/public/avatars/' . $avatar->image) : '';
    $voicePath = $user && $user->voice 
        ? asset('storage/app/public/voices/' . $user->voice) : '';

    return response()->json([
        'success' => true,
        'message' => 'User details updated successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'user_gender' => $user->gender,
            'avatar_id' => $user->avatar_id,
            'image' => $imageUrl,
            'gender' => $gender,
            'language' => $user->language,
            'age' => $user-> age ?? '',
            'mobile' => $user->mobile ?? '',
            'interests' => $user->interests ?? '',
            'describe_yourself' => $user-> describe_yourself ?? '',
            'voice' => $voicePath ?? '',
            'status' => $user->status ?? '',
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 200);
}

public function update_voice(Request $request)
{
    $user_id = $request->input('user_id');
    $voice = $request->file('voice'); 

    // Validate user_id and voice
    if (empty($user_id)) {
        return response()->json([
            'success' => false,
            'message' => 'user_id is empty.',
        ], 200);
    }

    if (empty($voice)) {
        return response()->json([
            'success' => false,
            'message' => 'voice is empty.',
        ], 200);
    }

    if ($voice->getClientOriginalExtension() !== 'mp3') {
        return response()->json([
            'success' => false,
            'message' => 'The voice file must be an MP3.',
        ], 200);
    }

    $user = Users::find($user_id);

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not found.',
        ], 200);
    }

    $voicePath = $voice->store('voices', 'public');

    $user->voice = basename($voicePath);
    $user->status = 1; 
    $user->datetime = now(); 
    $user->save();

    $avatar = Avatars::find($user->avatar_id);
    $gender = $avatar ? $avatar->gender : '';

    $imageUrl = $avatar && $avatar->image 
        ? asset('storage/app/public/avatars/' . $avatar->image) : '';
    $voicePath = $user && $user->voice 
        ? asset('storage/app/public/voices/' . $user->voice) : '';

    return response()->json([
        'success' => true,
        'message' => 'User details updated successfully.',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'user_gender' => $user->gender,
            'avatar_id' => $user->avatar_id,
            'image' => $imageUrl,
            'gender' => $gender,
            'language' => $user->language,
            'age' => $user-> age ?? '',
            'mobile' => $user->mobile ?? '',
            'interests' => $user->interests ?? '',
            'describe_yourself' => $user-> describe_yourself ?? '',
            'voice' => $voicePath, 
            'status' => $user->status ?? '',
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
        ],
    ], 200);
}


public function speech_text(Request $request)
{
    // Get the 'language' parameter from the request
    $language = $request->input('language');

    // Check if 'language' is empty
    if (empty($language)) {
        return response()->json([
            'success' => false,
            'message' => 'Language is empty.',
        ], 200);
    }

    // Fetch one random speech text related to the specified language
    $speech_text = Speech_texts::where('language', $language)->inRandomOrder()->first();

    // Check if any record was found
    if (!$speech_text) {
        return response()->json([
            'success' => false,
            'message' => 'No Speech Text found for the specified language.',
        ], 200);
    } 

    // Prepare the response data
    $speech_textData = [
        [
            'id' => $speech_text->id,
            'text' => $speech_text->text,
            'language' => $speech_text->language,
        ]
    ];

    return response()->json([
        'success' => true,
        'message' => 'Speech Text listed successfully.',
        'data' => $speech_textData,
    ], 200);
}

public function female_users_list(Request $request)
{
    // Retrieve offset and limit from the request, with default values
    $offset = $request->input('offset', 0);
    $limit = $request->input('limit', 10); // Default limit to 10 if not provided

    // Count total female users
    $totalCount = Users::where('gender', 'female')->count();

    // Retrieve paginated female users
    $Users = Users::where('gender', 'female')
        ->skip($offset)
        ->take($limit)
        ->with('avatar') // Only eager load the avatar relationship if necessary
        ->get();

    if ($Users->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No female users found.',
            'total' => $totalCount, // Include total count even if no data found
        ], 200);
    }

    $UsersData = [];
    foreach ($Users as $User) {
        $avatar = $User->avatar; // Use the avatar relationship to get the avatar
        $gender = $avatar ? $avatar->gender : '';
        $imageUrl = $avatar && $avatar->image ? asset('storage/app/public/avatars/' . $avatar->image) : '';
        $voicePath = $User->voice ? asset('storage/app/public/voices/' . $User->voice) : '';

        $UsersData[] = [
            'id' => $User->id,
            'name' => $User->name,
            'user_gender' => $User->gender,
            'avatar_id' => $User->avatar_id,
            'image' => $imageUrl,
            'gender' => $gender,
            'language' => $User->language,
            'age' => $User->age ?? '',
            'mobile' => $User->mobile ?? '',
            'interests' => $User->interests ?? '',
            'describe_yourself' => $User->describe_yourself ?? '',
            'voice' => $voicePath ?? '',
            'status' => $User->status ?? '',
            'datetime' => Carbon::parse($User->datetime)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($User->updated_at)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($User->created_at)->format('Y-m-d H:i:s'),
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Female users listed successfully.',
        'total' => $totalCount, // Include total count in the response
        'data' => $UsersData,
    ], 200);
}

public function withdrawals_list(Request $request)
{
    // Retrieve user_id, offset, and limit from request
    $user_id = $request->input('user_id');
    $offset = $request->input('offset', 0);  // Default offset to 0 if not provided
    $limit = $request->input('limit', 10);  // Default limit to 10 if not provided

        // Check if user_id is provided
        if (empty($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'user_id is empty.',
            ], 200);
        }
    
    // Retrieve the total count of withdrawals for the given user_id
    $totalCount = Withdrawals::where('user_id', $user_id)->count();

    // Retrieve paginated withdrawals for the given user_id
    $withdrawals = Withdrawals::where('user_id', $user_id)
                 ->orderBy('datetime', 'desc')
                 ->skip($offset)  // Apply offset for pagination
                 ->take($limit)   // Apply limit for pagination
                 ->get();

    // Check if any withdrawals exist for this user
    if ($withdrawals->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'No withdrawals found for this user.',
            'total' => $totalCount, // Include total count even if no data found
        ], 200);
    }

    // Prepare the withdrawal data
    $withdrawalsData = [];
    foreach ($withdrawals as $withdrawal) {
        $withdrawalsData[] = [
            'id' => $withdrawal->id,
            'user_id' => $withdrawal->user_id,
            'amount' => $withdrawal->amount,
            'status' => $withdrawal->status,
            'datetime' => $withdrawal->datetime, // Assuming this field exists
        ];
    }

    return response()->json([
        'success' => true,
        'message' => 'Withdrawals listed successfully.',
        'total' => $totalCount, // Include total count in the response
        'data' => $withdrawalsData,
    ], 200);
}


}