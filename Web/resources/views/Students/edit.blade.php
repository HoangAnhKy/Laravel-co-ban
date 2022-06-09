@extends('template.Master')
@section('body_text')
    <form action="{{route('student.update', ['student' => $data->id])}}" method="post">
        @csrf
        @method('PUT')
        Tên:
        <input type="text" name="name" placeholder="Nhập tên của sinh viên" value="{{$data->name}}">
        @if($errors->has('name'))
            <span>{{$errors->first('name')}}</span>
        @endif

        <br/><br/>
        Giới tính: &nbsp;

        nam <input type="radio" name="gender" value="0"
                   @if ($data->gender === 0)
                       echo checked
            @endif>&nbsp;
        nữ <input type="radio" name="gender" value="1" @if ($data->gender === 1)
            echo checked
            @endif>
        <br/>
        <br/>
        Ngày sinh:
        <input type="date" name="birthdate" value="{{$data->birthdate}}"
        <br/>
        <br/>
        Trạng thái
        @foreach ($arrEnum as $option => $value)
            &nbsp;
            <input type="radio" name="status" value="{{ $value }}"
                   @if ($data->status === $value )
                       checked
                @endif
            > {{ $option }}
        @endforeach

        <br/>
        <br/>
        Lớp
        <select name="course_id" class="form-control">
            @foreach ($courses as $course)
                <option value="{{ $course->id }}"
                        @if ($course->id === $data->course_id)
                            selected=”selected”
                    @endif>
                    {{ $course->name }}
                </option>
            @endforeach
        </select>
        <br/>
        <button class="btn btn-success">Xác nhận</button>
    </form>
@endsection
