<?php

namespace App\Http\Requests\Slide;

use Illuminate\Foundation\Http\FormRequest;

class SlideRequest extends FormRequest
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
        $rules = [];
        $rules['images'] = 'required|numeric|min:1';
        for ($key = 0; $key < $this->images; $key++) {
            $rules['image_' . $key] = 'required|image|dimensions:min_width=1400,min_height=480|mimes:jpg,png,jpeg';
        }
        return $rules;
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'images.min' => ':attribute không được để trống.',
            'max' => ':attribute không được vượt quá :max kb.',
            'min' => ':attribute không được ít hơn :min kb.',
            'numeric' => ':attribute không hợp lệ.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $attr = [];
        $attr['images'] = 'Hình ảnh';
        for ($key = 0; $key < $this->images; $key++) {
            $attr['image_' . $key] = 'Hình ảnh vị trí số ' . $key;
        }
        return $attr;
    }

    protected function failedValidation($validator)
    {
        return response()->json(['errors' => $validator->errors()->all()]);
    }
}
