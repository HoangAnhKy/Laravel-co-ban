upload ảnh thông qua file

<input type="file" name="avatar">

lưu ý thêm enctype ở trên form mới load ảnh lên được

<form action="{{route('student.store')}}" method="post" enctype="multipart/form-data">

để lấy được ảnh ta phải xử lý bên trong back-end

$path = Storage::disk('public')->putFile('avatars', $request->file('avatar'));

vd: 

	public function store(StoreStudentsRequest $request)
    {
        $path = Storage::disk('public')->putFile('avatars', $request->file('avatar'));
        $arr = $request->validated();
        $arr['avatar'] = $path;
        Students::query()->create($arr);
        return redirect()->route('student.index')->with('success', 'Đã thêm thành công');
    }

sau cùng chạy câu lệnh

php artisan storage:link

để tạo shortcut trong public