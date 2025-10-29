<?php

namespace App\Http\Requests\Customer;

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
            'username' => 'required|alpha_dash|max:255',
            'email' => 'required|max:150|email:filter|unique:m_customer,email',
            'phone' => 'required|max:11',
            'password' => 'required|nullable|min:8|same:confirm_password',
            'confirm_password' => 'required|same:password',
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
            'same' => 'Mật khẩu và Nhập lại mật khẩu phải giống nhau',
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => 'Tên đăng nhập',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'confirm_password' => 'Nhập lại mật khẩu',
            'phone' => 'Số điện thoai',
        ];
    }
}
