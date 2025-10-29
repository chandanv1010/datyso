<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'username' => 'required|alpha_dash|max:255|unique:m_user,username',
            'email' => 'required|max:150|email:filter|unique:m_user,email',
            'phone' =>  'required', 'max:10', 'regex:/^(0?)(3|5|7|8|9)[0-9]{8}$/',
            'password' => 'required|min:6|same:repassword',
            'repassword' => 'required|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'alpha_dash' => ':attribute không hợp lệ.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'min' => ':attribute không được ít hơn :min ký tự.',
            'unique' => ':attribute đã tồn tại.',
            'email' => ':attribute không hợp lệ.',
            'same' => 'Mật khẩu và Nhập lại mật khẩu phải giống nhau.',
            'regex' => 'Số điện thoại không hợp lệ.',
            'unique' => ':attribute đã tồn tại.',
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => 'Tên đăng nhập',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'phone' => 'Số điện thoai',
            'repassword' => 'Nhập lại mật khẩu',
        ];
    }
}
