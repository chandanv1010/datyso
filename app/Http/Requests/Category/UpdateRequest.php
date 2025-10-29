<?php

namespace App\Http\Requests\Category;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

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
            'name' => 'required|max:255',
            'slug' => 'max:500',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'banner' =>  'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'mimes' => ':attribute không đúng định dạng ảnh',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 2048 KB.',
            'banner.max' => 'Kích thước hình banner không được vượt quá 2048 KB.',
            'image' => ':attribute không đúng định dạng',
            'image' => ':attribute phải là file ảnh',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên danh mục',
            'image' => 'Ảnh',
            'banner' => 'Banner', 
            'slug' => 'Slug',
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
            $article = Category::where([['slug', $this->slug],['id', '!=' , $this->route('id')]])->first();
            if ($article != null) {
                $validator->errors()->add('slug', 'Slug đã tồn tại, vui lòng chọn slug khác.');               
            }                 
        });
    }
}
