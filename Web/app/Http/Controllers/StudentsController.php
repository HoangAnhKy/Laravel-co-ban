<?php

namespace App\Http\Controllers;

use App\Enums\StudentStatus;
use App\Http\Requests\StoreStudentsRequest;
use App\Models\Students;
use App\Http\Requests\UpdateStudentsRequest;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\DataTables;

class StudentsController extends Controller
{
    private $model;

    public function __construct()
    {
        $nameRoute = Route::currentRouteName();
        $arr = explode('.', $nameRoute);
        $arr = array_map('ucfirst', $arr);
        $title = implode(' - ', $arr);

        View::share('title', $title);

        $arrEnum = StudentStatus::arrEnum();
        View::share('arrEnum', $arrEnum);
    }

    public function index()
    {
        return View('Students.index');
    }

    public function api()
    {
        $query = Students::query()
        ->select('students.*')
        ->addselect('courses.name as course_id')
        ->join('courses', 'courses.id', 'students.course_id');

        $data_in_course =  Students::query()->with('course');
        return DataTables::of($query)
            ->editColumn('gender', function ($object) {
                return $object->GetGen;
            })
            ->editColumn('birthdate', function ($object) {
                return $object->GetAge;
            })
            ->editColumn('created_at', function ($object) {
                return $object->GetDay;
            })
            ->editColumn('status', function ($object) {
                return StudentStatus::getkeyByValue($object->status);
            })

            // dùng khi sử dụng with có sẵn của datatable
//             ->editColumn('course_id', function ($object) {
//                 // return $object->GetNameClass;
//                 return $object->course->name;
//             })
            ->addColumn('edit', function ($object) {
                return route('student.edit', $object);
            })
            ->addColumn('delete', function ($object) {
                return route('student.destroy', $object);
            })
            ->filterColumn('course_id', function ($query, $keyword) {
                // sửa dụng tên của relationship
                $query->wherehas('course', function ($q) use ( $keyword){
                   return $q->where('id', $keyword);
                });
            })
            ->filterColumn('status', function ($query, $keyword) {
               if($keyword !== '-1'){
                   $query->where('status', $keyword);
               }
            })
            ->make(true);
    }

    public function apiName(Request $request)
    {
        return Students::query()->where('name', 'like', '%'.$request->get('q').'%')->get(['id', 'name']);
    }
    public function create()
    {
        $course = Course::query()->get();
        return View('Students.create', [
            'courses' => $course,
        ]);
    }

    public function store(StoreStudentsRequest $request)
    {
        Students::query()->create($request->validated());
        return redirect()->route('student.index')->with('success', 'Đã thêm thành công');
    }

    public function edit($students)
    {
        $data = Students::query()->find($students);
        $course = Course::query()->get();
        return View('Students.edit', [
            'courses' => $course,
            'data' => $data,
        ]);
    }

    public function update(Request $request, $students)
    {
        $object = Students::find($students);
        $object->fill($request->except('_token', '_method'));
        $object->save();

        return redirect()->route('student.index')->with('success', 'Đã sửa thành công');
    }

    public function destroy($students)
    {
        Students::destroy($students);

        $arr = [];
        $arr['success'] = true;
        $arr['messages'] = '';

        return response($arr, 200);
    }
}
