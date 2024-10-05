<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'username' => 'required|max:50|min:5',
            'password' => 'required|max:20',
        ];
    }

    public function messages()
    {
        return [
            'username.max' => 'Tên đăng nhập/Email dài tối đa 50 ký tự',
            'username.min' => 'Tên đăng nhập/Email dài tối thiểu 6 ký tự',
            // 'password.alpha_dash' => 'Mật khẩu chỉ bao gồm chữ cái, số, dấu gạch ngang và dấu gạch dưới',
            'password.max' => 'Mật khẩu dài tối đa 20 ký tự'
        ];
    }
}
