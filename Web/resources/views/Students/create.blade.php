@extends('template.Master')
@section('body_text')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{route('student.store')}}" method="post">

        @csrf
        Tên:
        <input type="text" name="name" placeholder="Nhập tên của sinh viên">
        {{-- @if($errors->has('name'))
            <span>{{$errors->first('name')}}</span>
        @endif --}}

        <br/><br/>
        Giới tính: &nbsp;

        nam <input type="radio" name="gender" value="0" checked>&nbsp;
        nữ <input type="radio" name="gender" value="1">
        <br/>
        <br/>
        Ngày sinh:
        <input type="date" name="birthdate">
        <br/>
        <br/>
        Trạng thái

        @foreach ($arrEnum as $option => $value)
            &nbsp;
            <input type="radio" name="status" value="{{ $value }}"
                   @if ($loop->first)
                       checked
                @endif
            > {{ $option }}
        @endforeach

        <br/>
        <br/>
        Lớp
        <select name="course_id" class="form-control">
            @foreach ($courses as $course)
                <option value="{{ $course->id }}">{{ $course->name }}</option>
            @endforeach
        </select>

        <br/>
        <button class="btn btn-success">Xác nhận</button>
    </form>
@endsection
