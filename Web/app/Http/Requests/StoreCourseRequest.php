<?php

namespace App\Http\Requests;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'bail',
                'required',
                'string',
                Rule::unique(Course::class, 'name'),
            ]
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute Không được để trống',
            'string' => ':attribute Phải là một chuỗi',
            'unique' => ':attribute Đã được sử dụng',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Tên',
        ];
    }
}
