<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
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
            'password' => 'required|min:6|same:repassword',
            'repassword' => 'required|same:password',
        ];
    }

    public function messages() {
        return [
            'required' => ':attribute không được để trống',
            'min' => ':attribute không được ít hơn :min ký tự',
            'same' => 'Mật khẩu và Nhập lại mật khẩu phải giống nhau',
        ];
    }

    public function attributes() {
        return [
            'password' => 'Mật khẩu mới',
            'repassword' => 'Nhập lại mật khẩu',
        ];
    }
}
