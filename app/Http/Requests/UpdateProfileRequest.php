<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;  // 認証されたユーザーのみ許可
    }

    public function rules()
    {
        $rules = [];

        switch ($this->input('type')) {
            case 'name':
                $rules = [
                    'name' => 'required|string|max:255',
                ];
                break;

            case 'email':
                $rules = [
                    'email' => 'required|string|email|max:255|unique:admins,email,' .auth('admin')->id(),
                ];
                break;

            case 'password':
                $rules = [
                    'password' => 'required|string|min:8|confirmed',
                ];
                break;
        }

        return $rules;
    }
}

