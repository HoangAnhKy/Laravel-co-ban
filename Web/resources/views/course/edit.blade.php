@extends('template.Master')
@section('body_text')
    <form action="{{route('course.update', ['course' => $each->id])}}" method="post">
        @csrf
        @method('put')
        Tên:
        <input type="text" name="name" value="{{$each->name}}">
        @if($errors->has('name'))
            <span>{{$errors->first('name')}}</span>
        @endif
        <br/>
        <br/>
        <button class="btn btn-success">Xác nhận</button>
    </form>
@endsection
