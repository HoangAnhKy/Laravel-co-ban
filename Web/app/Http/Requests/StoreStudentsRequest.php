<?php

namespace App\Http\Requests;

use App\Enums\StudentStatus;
use App\Models\Course;
use App\Models\Students;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentsRequest extends FormRequest
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
                Rule::unique(Students::class, 'name'),
            ],
            'gender' => [
                'required',
                'boolean',
            ],
            'birthdate' => [
                'required',
                'date',
                'before:today',
            ],
            'status' => [
                'required',
                Rule::in(StudentStatus::asArray()),
            ],
            'course_id' => [
                'required',
                Rule::exists(Course::class, 'id')
            ]
        ];
    }
}
