<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Users;
use App\Models\avatars;
use App\Models\coins;
use App\Models\speech_texts;   
use App\Models\Transaction;
use App\Models\DeletedUsers; 
use App\Models\Withdrawals;  
use App\Models\UserCalls;
use App\Models\News; 
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



class AuthControllerApi extends Controller{
    public function __construct(){
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
    public function login1(){
        $credentials = request(['first_name', 'password']);
 
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
 
        return $this->generateToken($token);
    }

    public function register(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'mobile' => 'required|digits:10|unique:users',
            'language' => 'required',
            'avatar_id' => 'required',
            'gender' => 'required||in:Male,Female,male,female,MALE,FEMALE',

        ]);
        $mobile = $request->input('mobile'); 
        $language = $request->input('language');
        $name = $request->input('name');
        $avatar_id = $request->input('avatar_id');
        $gender = $request->input('gender');
        $age = $request->input('age');
        $interests = $request->input('interests');
        $describe_yourself = $request->input('describe_yourself');
        $avatar = Avatars::find($avatar_id);

        if (!$avatar) {
            return response()->json([
                'success' => false,
                'message' => 'Avatar not found.',
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

        // Generate a random name for female users if not provided
        if (strtolower($gender) === 'female' && empty($name)) {
            $name = $this->generateRandomFemaleName(); 
        } elseif (empty($name)) {
            // Fallback for male users or unspecified gender
            $name = $this->generateRandomName(); 
        }

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
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
        $imageUrl = ($avatar && $avatar->image) ? asset('storage/app/public/avatars/' . $avatar->image) : '';
        $voicePath = $user && $user->voice ? asset('storage/app/public/voices/' . $user->voice) : '';

        $credentials = request(['mobile']);
        $token = auth('api')->attempt($credentials);
        $userDetails = [
            'id' => $user->id,
            'name' => $user->name,
            'user_gender' => $user->gender,
            'mobile' => $user->mobile,
            'language' => $user->language,
            'avatar_id' => (int) $user->avatar_id,
            'image' => $imageUrl ?? '',
            'gender' => $gender,
            'age' => (int) $user->age ?? '',
            'interests' => $user->interests,
            'describe_yourself' =>  $user->describe_yourself ?? '',
            'voice' =>  $voicePath ?? '',
            'status' => 0,
            'balance' =>(int) $user->balance ?? '',
            'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
            'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Registered successfully.',
            'token' => $token,
            'data' => $userDetails,
        ], 200);
    }
    private function generateRandomFemaleName(){
        // Fetch a random name from female_users table
        $randomFemaleName = DB::table('female_users')->inRandomOrder()->value('name');
        if (!$randomFemaleName) {
            $randomFemaleName = 'User'; // Default name if table is empty
        }

        // Append random 3 digits
        $randomDigits = substr(str_shuffle('0123456789'), 0, 3);
        return $randomFemaleName . $randomDigits;
    }

    private function generateRandomName(){
        $letters = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5);
        $numbers = substr(str_shuffle('0123456789'), 0, 3);
        return $letters . $numbers;
    }

    public function login(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'mobile' => 'required|digits:10',
        ]);
 
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $mobile = request()->mobile;
        $credentials = request(['mobile']);
            if (! $token = auth('api')->attempt($credentials)) { 
                return response()->json(['error' => 'Unauthorized'], 401);
        } 
       
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

        $imageUrl = ($avatar && $avatar->image) ? asset('storage/app/public/avatars/' . $avatar->image) : '';
        $voicePath = $user && $user->voice ? asset('storage/app/public/voices/' . $user->voice) : '';

        return response()->json([
            'token' => $token,
            'success' => true,
            'registered' => true,
            'message' => 'Logged in successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'user_gender' => $user->gender,
                'language' => $user->language,
                'mobile' => $user->mobile,
                'avatar_id' => (int) $user->avatar_id,
                'image' => $imageUrl ?? '',
                'gender' => $gender,
                'age' => (int) $user-> age ?? '',
                'interests' => $user->interests ?? '',
                'describe_yourself' => $user->describe_yourself ?? '',
                'voice' => $voicePath ?? '',
                'status' => $user->status ?? '',
                'balance' =>(int) $user->balance ?? '',
                'audio_status' =>(int) $user->audio_status ?? '',
                'video_status' =>(int) $user->video_status ?? '',
                'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
            ],
        ], 200);
    }

    public function logout(){
        auth('api')->logout();
        return response()->json(['message' => 'You have successfully logged out']);
    }

    public function refresh(){
        // return $this->generateToken(auth('api')->refresh());
        return response()->json([
            'refresh_token' => auth('api')->refresh(),
            'success' => true,
            'registered' => true,
            'message' => 'Token refreshed successfully.',
        ], 200);

    }
    public function userDetails(Request $request){
        $user_id = auth('api')->user()->id;

        if (empty($user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve user details.',
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

        $imageUrl = ($avatar && $avatar->image) ? asset('storage/app/public/avatars/' . $avatar->image) : '';
        $voicePath = $user && $user->voice ? asset('storage/app/public/voices/' . $user->voice) : '';
        return response()->json([
            'success' => true,
            'message' => 'User details retrieved successfully.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'user_gender' => $user->gender,
                'avatar_id' => (int) $user->avatar_id,
                'image' => $imageUrl ?? '',
                'gender' => $gender,
                'language' => $user->language,
                'age' => (int) $user-> age ?? '',
                'mobile' => $user->mobile ?? '',
                'interests' => $user->interests ?? '',
                'describe_yourself' => $user-> describe_yourself ?? '',
                'voice' => $voicePath ?? '',
                'status' => $user->status ?? '',
                'balance' =>(int) $user->balance ?? '',
                'audio_status' =>(int) $user->audio_status ?? '',
                'video_status' =>(int) $user->video_status ?? '',
                'datetime' => Carbon::parse($user->datetime)->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d H:i:s'),
                'created_at' => Carbon::parse($user->created_at)->format('Y-m-d H:i:s'),
            ],
        ], 200);
        
    }
}
