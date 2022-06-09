@extends('template.Master')
@section('body_text')
    <form action="{{route('course.store')}}" method="post">
        @csrf
        Tên:
        <input type="text" name="name" placeholder="Nhập tên của bạn">
        @if($errors->has('name'))
            <span>{{$errors->first('name')}}</span>
        @endif
        <br/>
        <br/>
        <button class="btn btn-success">Xác nhận</button>
    </form>
@endsection
