<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class coinsStoreRequest extends FormRequest
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
            'price' => 'nullable|string',
            'coins' => 'nullable|string',
            'save' => 'nullable|string',
            'popular' => 'nullable|boolean',
        ];
    }
}
