<form method='post' action='{{ route('Resign.account') }}' enctype="multipart/form-data">
    @csrf
    Tên
    <input type='name' class='form-control' name='name'>
    <br />
    <br />
    Email
    <input type='email' class='form-control' name='email'>
    <br />
    <br />
    Mật khẩu
    <input type="password" class='form-control' name='password'>
    <br />
    <br />
    avatar
    <input type="file" class="form-control" name="avatar">
    <br />
    <br />
    <button>Xác nhận</button>
</form>
