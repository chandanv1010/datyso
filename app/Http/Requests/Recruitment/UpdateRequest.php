<?php

namespace App\Http\Requests\Recruitment;

use App\Models\Recruitment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpdateRequest extends FormRequest
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
            'name' => 'required|max:255|unique:m_Recruitment,name,'.$this->route('id').',id',
            'slug' => 'max:500',
            'description' => 'required',
            'type' => 'required',
            'location' => 'required',
            'image' =>  'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'mimes' => ':attribute không đúng định dạng ảnh',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 2048 KB.',
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
            'type' => 'Thể loại',
            'location' => 'Địa chỉ',
            'description' => 'Nội dung bài viết',
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
            $article = Recruitment::where([['slug', $this->slug],['id', '!=' , $this->route('id')]])->first();
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
