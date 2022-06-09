@extends('template.Master')
@section('body')
    <form action="{{route('checkout')}}" method="post">
        @csrf
        <input type="text" class="form-control" name="email" placeholder="Email...">
        <br />
        <input type="text" class="form-control" name="password" placeholder="************">
        <br />
        <a href="{{route('auth.redirect',  ['provider' => 'github'])}}">Github</a>
        <a href="{{route('auth.redirect',  ['provider' => 'facebook'])}}">Facebook</a>
        <a href="{{route('auth.redirect',  ['provider' => 'google'])}}">Google</a>
        <br />
        <Button class="btn btn-success form-control"> Xác nhận</Button>
    </form>
@endsection