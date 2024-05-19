<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateShopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'shop.name' => 'required|string|max:255',
            'admin.name' => 'required|string|max:255',
            'admin.email' => 'required|email|max:255|unique:users,email,'.Auth::user()->id.'id',
            'admin.password' => 'nullable|confirmed|min:8',
            'admin.password_confirmation' => 'nullable|in:'.$this->input('admin.password'),
        ];
    }
}
