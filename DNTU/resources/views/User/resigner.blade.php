@extends('template.Master')
@section('body')
    <h1>Đăng kí</h1>
    <form action="{{route('checkout')}}" method="post">
        @csrf
        @auth
            <input type="text" class="form-control" name="email" placeholder="Email..." value="{{auth()->user()->email}}">
            <br/>
            <input type="text" class="form-control" name="password" placeholder="************">
            <br/>
            <Button class="btn btn-success form-control"> Xác nhận</Button>
        @endauth
        @guest
            <input type="text" class="form-control" name="email" placeholder="Email...">
            <br/>
            <input type="text" class="form-control" name="password" placeholder="************">
            <br/>
            <Button class="btn btn-success form-control"> Xác nhận</Button>
        @endguest
    </form>
@endsection