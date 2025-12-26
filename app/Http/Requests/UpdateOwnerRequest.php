<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOwnerRequest extends FormRequest
{
    public function authorize()
    {
        // Bisa diperketat: misal user harus memiliki store dsb.
        return true;
    }

    public function rules()
    {
        $userId = $this->user()->id;

        return [
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|max:255|unique:users,email,' . $userId,
            'owner_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ];
    }
}
