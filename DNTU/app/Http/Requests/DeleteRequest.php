<?php

namespace App\Http\Requests;

use App\Models\course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'Course' => [
                Rule::exists(course::class, 'id'),
            ],
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge(['Course' => $this->route('Course')]);
    }
    public function messages()
    {
        return [
            'exists' => ':attribute không tồn tại',
        ];
    }
    public function attributes(){
        return [
            'name' => 'Tên lớp',
        ];
    }
}
