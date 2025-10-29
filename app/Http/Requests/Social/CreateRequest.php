<?php

namespace App\Http\Requests\Social;

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
            'name' => 'required|alpha_dash|max:255',
            'url' => 'required',
            'image' =>  'required'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'max' => ':attribute không được vượt quá :max ký tự.'
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên mạng xã hội',
            'url' => 'Đường dẫn',
            'image' => 'Ảnh'
        ];
    }
}
