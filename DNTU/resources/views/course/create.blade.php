@extends('template.Master')
@section('body')
    <h1>Thêm lớp</h1>
    @if($errors->has('name'))
        <div class="alert alert-danger">
            <span>{{$errors->first('name')}}</span>
        </div>
    @endif
    <form method="post" action="{{route('Courses.store')}}">
        @csrf
        <input type="text" class="form-control" name="name" placeholder="Tên lớp">
        <br/>
        <button class="btn btn-success form-control">Xác nhận</button>
    </form>
@endsection