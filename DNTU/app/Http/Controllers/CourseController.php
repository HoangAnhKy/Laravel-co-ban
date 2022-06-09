<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteRequest;
use App\Models\course;
use App\Http\Requests\StorecourseRequest;
use App\Http\Requests\UpdatecourseRequest;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    private $model;
    public function __construct(){
        $this->model = new course();
    }

    public function index()
    {
        $data = $this->model->query()->get();
        return View('course.index',[
            'data' => $data,
        ]);
    }
    public function apiName(Request $request)
    {
        return $this->model::query()
            ->where('name', 'like', '%'. $request->get('q').'%')
            ->get(['id','name']);
    }
    public function create()
    {
        return View('course.create');
    }
    public function store(StorecourseRequest $request)
    {
        $this->model->query()->create($request->validated());
        return redirect()->route('Courses.index')->with('success', 'Thêm lớp thành công');
    }

    public function edit($courseid)
    {
        $data = $this->model->query()->find($courseid);
        return View('course.edit',[
            'data' => $data,
        ]);
    }

    public function update(UpdatecourseRequest $request, $courseid)
    {
        $this->model->query()->where('id', $courseid)->update($request->validated());
        return redirect()->route('Courses.index')->with('success', 'Cập nhật lớp thành công');
    }
    public function destroy(DeleteRequest $request, $courseid)
    {
        $this->model->destroy($courseid);
        return redirect()->route('Courses.index')->with('success', 'Xóa lớp thành công');
    }
}
