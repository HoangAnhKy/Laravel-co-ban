<form method='post' action='{{ route('Login.login') }}'>
    @csrf
    Email
    <input type='email' class='form-control' name='email'>
    <br />
    <br />
    Mật khẩu
    <input type="password" class='form-control' name='password'>
    <br />
    <br />
    <button>Xác nhận</button>
    <a href="{{ route('resign')}}"> Đăng kí</a>
</form>
