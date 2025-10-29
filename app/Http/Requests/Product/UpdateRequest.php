<?php

namespace App\Http\Requests\Product;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
            'description' => 'required',
            'tech_detail' => 'required',
            'accessory_detail' => 'required',
            'category_id' => [
                'required',
                Rule::exists('m_category', 'id')->where(function ($query) { 
                    $query->where('status', 1);
                }),
            ],

            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'attributeContent' => 'required|array|min:1',
            'attributeContent.*' => 'required|string|max:255',
            'slug' => 'max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'min' => ':attribute không được nhỏ hơn :min',
            'image' => ':attribute phải là file ảnh',
            'mimes' => ':attribute không đúng định dạng ảnh',
            'category_id.required' => 'Danh mục không được để trống.',
            'category_id.exists' => 'Danh mục đã chọn không hợp lệ.',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 2048 KB.',
            'attributeContent.required' => 'Thông tin sản phẩm không được để trống.',
            'attributeContent.min' => 'Thông tin sản phẩm phải có ít nhất một mục.',
            'attributeContent.*.required' => 'Mục thông tin sản phẩm không được để trống.',
            'attributeContent.*.max' => 'Mục thông tin sản phẩm không được vượt quá :max ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên sản phẩm',
            'description' => 'Nội dung',
            'image' => 'Ảnh',
            'tech_detail' => 'Chi tiết công nghệ',
            'accessory_detail' => 'Chi tiết phụ kiện',
            'video' => 'Video',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->request->set('price', trim(str_replace('.', '', $this->price)));

        $this->request->set('name', trim(strip_tags($this->name)));
        if (strlen(trim($this->slug)) == 0 || !$this->has('slug') || $this->slug == null) {           
            $slug = Str::slug($this->name, '-');
        } else {
            $slug = Str::slug($this->slug, '-');
        }        
        $this->request->set('slug', $slug);
    }

    protected function failedValidation($validator) {
        $response = redirect($this->getRedirectUrl())
                        ->withErrors($validator->errors(), $this->errorBag)
                        ->withInput($this->request->all());
        throw (new ValidationException($validator, $response));                        
    }

    protected function withValidator($validator) {               
        $validator->after(function ($validator) {                              
            $article = Product::where([['slug', $this->slug],['id', '!=' , $this->route('id')]])->first();
            if ($article != null) {
                $validator->errors()->add('slug', 'Slug đã tồn tại, vui lòng chọn slug khác.');               
            }                 
        });
    }
}
