<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsersStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'unique_name' => 'required|string',
            'age' => 'required|string|max:100',
            'email' => 'nullable|email',
            'gender' => 'nullable|string',
            'state' => 'nullable|string',
            'city' => 'nullable|string',
            'avatar' => 'nullable|profile',
            'refer_code' => 'nullable|string',
            'referred_by' => 'nullable|string',
            'profession_id' => 'nullable|integer',
            'points' => 'required|integer',
            'datetime' => 'nullable|datetime',
            'verified' => 'nullable|boolean',
            'online_status' => 'nullable|boolean',
            'dummy' => 'nullable|boolean',
            'message_notify' => 'nullable|boolean',
            'add_friend_notify' => 'nullable|boolean',
            'view_notify' => 'nullable|boolean',
            'profile_verified' => 'nullable|boolean',
            'cover_img_verified' => 'nullable|boolean',
            'last_Seen' => 'nullable|datetime',
            'introduction' => 'nullable|string',
            'language' => 'nullable|string',
        ];
    }
}
