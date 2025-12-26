<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'store_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:1000',
            'store_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ];
    }
}
