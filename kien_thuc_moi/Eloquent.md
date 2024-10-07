# Một số câu truy vấn Eloquent

-   `create(giá trị cần thêm)` dùng để thêm dữ liệu

    ```php
    $user = User::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => bcrypt('password123')
    ]);
    ```

-   `update(giá trị cần sửa)` dùng để update dữ liệu

    ```php
    $user = User::find(1);
    $user->update(['name' => 'Jane Doe']);
    ```
-   `delete()` hoặc `destroy(Giá trị cần xóa)` để xóa dữ liệu

    ```php
    $user = User::find(1);
    $user->delete();

    User::destroy(2); // Xóa user có ID là 2
    ```
-   `validated()` dùng để validate dữ liệu khi đã validation dữ liệu
-   `find(giá trị cần tìm)` dùng để tìm kiếm dữ liệu
    ```php
    $user = User::find(1); // Tìm kiếm theo ID
    ```
-   `where(cột cần lấy, giá trị so sánh của cột đó)` dùng để kiểm tra dữ liệu
    ```php
     // dùng để tìm kiểm nhiều
     $query->where([
        ['column_1', '=', 'value_1'],
        ['column_2', '<>', 'value_2'],
        [COLUMN, OPERATOR, VALUE],
        ...
    ])
    
    ```
-   `whereHas` dùng để search các cột trong bảng liên kết 
    ```php
    $query->whereHas('Course', function ($query) use ($key_search){
                $query->where('name_course', 'like', '%'.$key_search.'%');
            })->get();
    ```
-   `orWhere` dùng để tìm kiếm hoặc
    ```php
        if (!empty($list_search)){
            $query->where(...array_shift($list_search));
            foreach ($list_search as $search):
                $query->orWhere(...$search);
            endforeach;
        }
    ```
-   `paginate(số trang)` dùng để phân trang
    ```php
    $users = User::paginate(10); // Lấy 10 user trên mỗi trang
    ```
-   `{{ $users->withQueryString()->links() }}` dùng để nối câu truy vấn để hiện số trang tiếp theo của câu truy vấn
-   `select(giá trị cần lấy)` dùng để lấy dữ liệu, `addselect(giá trị cần lấy)` dùng để lấy thêm dữ liệu, `selectRaw(câu truy vấn đã được viết sẵn)` dùng để lấy giá trị đã được viết sẵn
-   `join('tên bảng', 'giá trị 1', 'giá trị 2')` dùng để kết hợp nhiều bảng lại với nhau
-   `joinsub('Tên bảng', 'as tên mới hoặc không', function($join){ $join->on('tên bảng', 'tên bảng join') })` dùng để kết hợp các bảng có sử dụng subquery, các câu truy vấn lồng nhau
-   `when` dùng để thay thế if else. Nó sẽ kiểm tra giá trị cần lấy có tồn tại hay không rồi sau đó kiểm tra lại một lần nữa xem giá trị có trùng với dữ liệu trong database hay không nếu, nếu có thì nó mới lấy

    ```php
    ->when($request->has(giá trị cần lấy), function($q){
        return $q->where(cột cần lấy, giá trị so sánh);
    })
    ```

-   `latest()` dùng để lấy các giá trị ở cuối hàng
-   `clone()` dùng để tránh bị trùng khi sử dụng nhiều lần `$this->model`
-   `distinct()` dùng để lọc giá trị bị trùng trong databsse
-   `pluck('city')` dùng để lấy giá trị trong cột nào đó thôi, có thể thay thế select
-   `take(LIMIT)` dùng đê lấy giới hạn số dòng cần lấy
-   `setVisible(['id', 'nameCouse'])` dùng để lấy các cột cần lấy
-   `setHidden(['id', 'nameCouse'])` dùng để ẩn các cột không cần lấy
-   `setAttribute('key', value)` dùng để thêm mới một giá trị nào đó trong khi xử lý.

# hỗ trợ trong xử lý mảng.

-   `current($value)` dùng để lấy giá trị đầu tiên trong mảng ['key' => 'value'].
-   `next($value)` dùng để lấy giá trị thứ 2 trong mảng ['key' => 'value'].