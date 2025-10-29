<?php

namespace App\Http\Requests\UserProfile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UserProfileRequest extends FormRequest
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
            'full_name' => 'required|max:255,',
            'email' => 'required|max:150|email:filter|email',
            'file' => 'required|mimes:doc,docx,pdf|max:2048',
            'phone' =>  ['max:10', 'regex:/^(0?)(3|5|7|8|9)[0-9]{8}$/'],
            'level' =>'required|max:255,',
            'school_name' =>'required|max:255,',
            'specialized' =>'required|max:255,',
            'latest_job' =>'required|max:255,',
            'nominee' =>'required|max:255,',
            'years_experience' =>'required|max:255,',
            'desired_salary' =>'required|max:255,',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'email' => ':attribute không hợp lệ.',
            'path.max' => 'Kích thước hình ảnh không được vượt quá 2048 KB.',
            'mimes' => ':attribute không đúng định dạng doc,docx,pdf',
            'regex' => 'Số điện thoại không hợp lệ.'
        ];
    }

    public function attributes(): array
    {
        return [
            'full_name' => 'Họ và tên',
            'email' => 'Email',
            'file' => 'Hồ sơ/CV',
            'level' => 'Cấp bậc đào tạo',
            'school_name' => 'Tên trường',
            'specialized' => 'Chuyên ngành',
            'latest_job' => 'Công việc gần nhất',
            'nominee' => 'Vị trí ứng tuyển',
            'years_experience' => 'Năm kinh nghiệm',
            'desired_salary' => 'Mức lương mong muốn',
            'phone' => 'Số điện thoại'
        ];
    }

    protected function failedValidation($validator) {
        $response = redirect($this->getRedirectUrl())
                        ->withErrors($validator->errors(), $this->errorBag)
                        ->withInput($this->request->all());
        throw (new ValidationException($validator, $response));                        
    }
}
