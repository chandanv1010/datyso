<?php

namespace App\Http\Requests\Solution;

use App\Models\Solution;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
            'name' => 'required|max:255|unique:m_solution,name',
            'slug' => 'max:500',
            'content' => 'required',
            'image' =>  'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 2048 KB.',
            'mimes' => ':attribute không đúng định dạng ảnh',
            'image' => ':attribute không đúng định dạng',
            'unique' => ':attribute đã tồn tại.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên tiêu đề',
            'slug' => 'Slug',
            'image' => 'Ảnh',
            'content' => 'Nội dung bài viết'
        ];
    }

    protected function prepareForValidation() {
        $this->request->set('name', trim(strip_tags($this->name)));
        if (strlen(trim($this->slug)) == 0 || !$this->has('slug') || $this->slug == null) {           
            $slug = Str::slug($this->name, '-');
        } else {
            $slug = Str::slug($this->slug, '-');
        }        
        $this->request->set('slug', $slug);       
    }

    protected function withValidator($validator) {               
        $validator->after(function ($validator) {                              
            $article = Solution::where('slug', $this->slug)->first();            
            if ($article != null) {
                $validator->errors()->add('slug', 'Slug đã tồn tại, vui lòng chọn slug khác.');               
            }                 
        });
    }

    protected function failedValidation($validator) {
        $response = redirect($this->getRedirectUrl())
                        ->withErrors($validator->errors(), $this->errorBag)
                        ->withInput($this->request->all());
        throw (new ValidationException($validator, $response));                        
    }
}
