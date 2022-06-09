<?php

namespace App\Http\Controllers;

use App\Enums\StatusStudent;
use App\Models\student;
use App\Http\Requests\StorestudentRequest;
use App\Http\Requests\UpdatestudentRequest;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\DataTables;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $arrEnum = StatusStudent::arrStatus();
        View::share('arrEnum', $arrEnum);
    }
    public function index()
    {
        return View('student.index');
    }
    public function api()
    {
        $data = student::query()->with('course');
        $query = student::query()
            ->select('students.*')
            ->addSelect('courses.name as NameClass')
            ->join('courses', 'students.course_id', 'courses.id' );
        return DataTables::of($query)
            ->editColumn('created_at', function($object){
                return $object->CustomerDay;
            })
            ->addColumn('NameClass', function($object){
                return $object->course->name;
            })->editColumn('status', function($object){
                return StatusStudent::getKeyInValues($object->status);
            })
            ->addColumn('edit', function($object){
                return route('Students.edit', $object);
            })
            ->filterColumn('NameClass', function ($query, $keyword) {
                $query->wherehas('course', function ($q) use ( $keyword){
                    return $q->where('id', $keyword);
                });
            })
            ->filterColumn('status', function ($query, $keyword) {
                if($keyword !== '0'){
                    $query->where('status', $keyword);
                }
            })
            ->make(true);
    }
    public function create()
    {
        //
    }

    public function store(StorestudentRequest $request)
    {
        //
    }

    public function edit(student $student)
    {
        //
    }

    public function update(UpdatestudentRequest $request, student $student)
    {
        //
    }
    public function destroy(student $student)
    {
        //
    }
}
