<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'name' => 'required|max:255',
            'address' => 'required|max:255',
           'email' => 'required|max:150|email:filter',
            'phone' =>  ['required', 'max:10', 'regex:/^(0?)(3|5|7|8|9)[0-9]{8}$/'],
            'hotline' => 'required|max:11',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'min' => ':attribute không được ít hơn :min ký tự.',
            'email' => ':attribute không hợp lệ.',
            'regex' => 'Số điện thoại không hợp lệ.'
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên cơ sở',
            'email' => 'Email',
            'address' => 'Địa chỉ',
            'phone' => 'Số điện thoai',
            'hotline' => 'Hotline'
        ];
    }
}
