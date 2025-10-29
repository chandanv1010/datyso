<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

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
            'username' => 'required',
            'password' => 'required',
        ];
    }

    public function messages() {
        return [
            'required' => ':attribute không được để trống'
        ];
    }

    public function attributes() {
        return [
            'username' => 'Tên đăng nhập',
            'password' => 'Mật khẩu',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->request->set('price', trim(str_replace('.', '', $this->price)));
    }

    protected function failedValidation($validator) {
        $response = redirect($this->getRedirectUrl())
                        ->withErrors($validator->errors(), $this->errorBag)
                        ->withInput($this->request->all());
        throw (new ValidationException($validator, $response));                        
    }
}
