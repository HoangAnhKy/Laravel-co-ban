<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\DataTables;

class CourseController extends Controller
{
    private $model;

    public function __construct()
    {
        $this->model = new Course();

        $routeName = Route::currentRouteName();
        $arr = explode('.', $routeName);
        $arr = array_map('ucfirst', $arr);
        $title = implode(' - ', $arr);

        View::share('title', $title);
    }

    public function index()
    {
//        $data = Course::query()->get();
//        return View('course.index',[
//            'data' => $data,
//        ]);
        return View('course.index');
    }

    public function test()
    {
        $data = Course::query()->paginate(2);
        return View('course.test', [
            'data' => $data,
        ]);
    }

    public function api()
    {
//        $data_In_Courses = $this->model->query()->withCount('student');
        $query = $this->model->query()
            ->select('courses.*')
            ->addSelect(DB::raw('COUNT(students.course_id) as countST'))
            ->Join('students', 'students.course_id', 'courses.id')
            ->groupBy('students.course_id');

        return DataTables::of($query)
            ->editColumn('created_at', function ($object) {
                return $object->getDay;
            })
            ->addColumn('edit', function ($object) {
                return route('course.edit', $object);
            })
            ->addColumn('delete', function ($object) {
                return route('course.destroy', $object);
            })
            ->addColumn('student_count', function ($object) {
                return $object->countST;
            })

            ->make(true);
    }

    public function apiName(Request $request)
    {
        return $this->model
            ->where('name', 'like', '%'.$request->get('q').'%')
            ->get(['id', 'name']);
    }

    public function layout()
    {
        return View('template.Master');
    }

    public function create()
    {
        return View('course.create');
    }

    public function store(StoreCourseRequest $request)
    {
        $this->model::query()->create($request->except('_token'));
        return redirect()->route('course.index');
    }

    public function edit($courseID)
    {
        $each = $this->model::query()->find($courseID);
        return View('course.edit', compact('each'));
    }

    public function update(Request $request, $courseID)
    {
//        Course::query()->where('id', $course->id)->update($request->except('_token', '_method'));

        $object = $this->model->query()->find($courseID);
        $object->fill($request->except('_token', '_method'));
        $object->save();
        return redirect()->route('course.index');
    }

    public function destroy($courseID)
    {
        $this->model->destroy($courseID);
        return redirect()->route('course.index');
    }
}
