<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ContactRequest extends FormRequest
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
            'username' => 'required|max:255,',
            'email' => 'required|max:150|email:filter|email',
            'product_id' => 'nullable',
            'phone' =>  ['required', 'max:10', 'regex:/^(0?)(3|5|7|8|9)[0-9]{8}$/'],
            'content' => 'required'
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
            'username' => 'Tên',
            'email' => 'Email',
            'content' => 'Nội dung',
            'phone' => 'Số điện thoai',
        ];
    }

    protected function failedValidation($validator) {
        $response = redirect($this->getRedirectUrl())
                        ->withErrors($validator->errors(), $this->errorBag)
                        ->withInput($this->request->all());
        throw (new ValidationException($validator, $response));                        
    }
}
