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
- `exists` và `doesntExist`: dùng để kiểm tra xem có bất kỳ bản ghi nào tồn tại dựa trên điều kiện hay không.
  
    ```php
    $exists = User::where('email', 'example@example.com')->exists(); // Trả về true hoặc false
    $notExists = User::where('email', 'nonexistent@example.com')->doesntExist(); // Trả về true nếu không có bản ghi nào
    ```

- `groupBy` và `having`: Dùng để nhóm các bản ghi lại với nhau theo một cột, và có thể sử dụng `having` để thêm điều kiện sau khi nhóm.

    ```php
    $orders = Order::select('status')
               ->selectRaw('count(*) as total')
               ->groupBy('status')
               ->having('total', '>', 100)
               ->get();
    ```

- `orderBy` và `orderByDesc`: sắp xếp dữ liệu tăng dần, giảm dần (desc)

    ```php
    $users = User::orderBy('name')->get(); // Sắp xếp theo tên tăng dần
    $users = User::orderByDesc('created_at')->get(); // Sắp xếp theo thời gian tạo giảm dần

    ```

-   `paginate(số trang)` dùng để phân trang
  
    ```php
    $users = User::paginate(10); // Lấy 10 user trên mỗi trang
    ```

-   `{{ $users->withQueryString()->links() }}` dùng để nối câu truy vấn để hiện số trang tiếp theo của câu truy vấn

-   `select(giá trị cần lấy)` dùng để lấy dữ liệu, `addselect(giá trị cần lấy)` dùng để lấy thêm dữ liệu, `selectRaw(câu truy vấn đã được viết sẵn)` dùng để lấy giá trị đã được viết sẵn

-   `join('tên bảng', 'giá trị 1', 'giá trị 2')` dùng để kết hợp nhiều bảng lại với nhau

-   `joinsub('Tên bảng', 'as tên mới hoặc không', function($join){ $join->on('tên bảng', 'tên bảng join') })` dùng để kết hợp các bảng có sử dụng subquery, các câu truy vấn lồng nhau

-   `latest()` dùng để lấy các giá trị ở cuối hàng

-   `clone()` dùng để tránh bị trùng khi sử dụng nhiều lần `$this->model`
    
    ```php
    // Truy vấn ban đầu
    $query = User::where('active', 1);

    // Sử dụng clone để tạo bản sao của truy vấn
    $queryForAdmins = (clone $query)->where('role', 'admin')->get();
    $queryForEditors = (clone $query)->where('role', 'editor')->get();

    // Truy vấn ban đầu không bị thay đổi
    $originalUsers = $query->get();

    ```
-   `distinct()` dùng để lọc giá trị bị trùng trong databsse

-   `pluck('city')` dùng để lấy giá trị trong cột nào đó thôi, có thể thay thế select

-   `take(LIMIT)` dùng đê lấy giới hạn số dòng cần lấy, tính từ bản ghi đầu tiên

    ```php
    $users = User::take(5)->get();
    ```

-   `setVisible(['id', 'nameCouse'])` dùng để lấy các cột cần lấy
    
    ```php
    $users = $users->setVisible(['id', 'name']);
    ```

-   `setHidden(['id', 'nameCouse'])` dùng để ẩn các cột không cần lấy
    
    ```php
    $users = $users->setHidden(['email', 'password', 'remember_token']);
    ```

-   `setAttribute('key', value)` dùng để thêm mới một giá trị nào đó trong khi xử lý.

    ```php
    $user = new User;
    $user->setAttribute('full_name', 'John Doe');
    echo $user->full_name; // John Doe
    ```
-   `replicate`: một bản ghi giống hệt bản ghi hiện tại mà không có ID.

    ```php
    // tạo một bản ghi giống hệt bản ghi hiện tại nhưng muốn lưu nó như một bản ghi mới.
    $newPost = Post::find(1)->replicate();
    $newPost->title = 'New Title';
    $newPost->save();
    ```
- `tap` : thực hiện các hành động trên model trước khi lưu nó vào cơ sở dữ liệu mà không cần thay đổi bản thân truy vấn.
  
    ```php
    User::find(1)->tap(function ($user) {
        $user->name = 'Updated Name';
    })->save();
    ````
    
# chunk() và chunkById():

là hai phương thức rất hữu ích trong Laravel Eloquent, đặc biệt khi làm việc với số lượng lớn dữ liệu. Cả hai phương thức này đều giúp xử lý dữ liệu theo "từng phần" (chunk) thay vì tải tất cả dữ liệu vào bộ nhớ cùng một lúc, điều này giúp tối ưu hóa hiệu suất và tránh lỗi tràn bộ nhớ.

- cú pháp 

    - chunk

        ```php
        Model::chunk($size, function ($items) {
            // Xử lý từng chunk dữ liệu ở đây
        });
        ```

    - chuckById: trong Laravel được thiết kế để chunk theo cột id mặc định, hoặc một cột mà muốn sử dụng như chỉ số tuần tự để lấy dữ liệu theo từng phần

        ```php
        Model::chunkById($size, function ($items) {
            // Xử lý từng chunk dữ liệu
        }, $column);
        ```
- kết luận
    - `chunkById`là lựa chọn an toàn và ổn định hơn khi xử lý dữ liệu lớn hoặc dữ liệu có khả năng thay đổi trong quá trình chunk. Nó đảm bảo rằng các chunk dữ liệu sẽ không bị trùng lặp hoặc bỏ sót.
    - `chunk` chỉ nên được sử dụng khi không cần lo lắng về việc dữ liệu thay đổi trong quá trình chunk. Trong trường hợp dữ liệu thay đổi, chunk có thể dẫn đến việc trùng lặp hoặc giảm số lượng dữ liệu.


# hàm tính toán count(), sum(), avg(), min(), max()

```php
$count = User::where('active', 1)->count(); // Đếm số người dùng active
$totalPrice = Order::sum('price'); // Tính tổng giá trị của cột price
$avgAge = User::avg('age'); // Tính tuổi trung bình
$maxSalary = Employee::max('salary'); // Lấy lương cao nhất
```

# Điều kiện

-   `when` dùng để thay thế if else. Nó sẽ kiểm tra giá trị cần lấy có tồn tại hay không rồi sau đó kiểm tra lại một lần nữa xem giá trị có trùng với dữ liệu trong database hay không nếu, nếu có thì nó mới lấy

    ```php
    ->when($request->has(giá trị cần lấy), function($q){
        return $q->where(cột cần lấy, giá trị so sánh);
    })
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

-   `orWhere` dùng để tìm kiếm hoặc
  
    ```php
        if (!empty($list_search)){
            $query->where(...array_shift($list_search));
            foreach ($list_search as $search):
                $query->orWhere(...$search);
            endforeach;
        }
    ```

- `whereBetween` và `whereNotBetween`

    Dùng để kiểm tra giá trị của một cột nằm giữa hai giá trị và không nằm giữa hai giá trị.

    ```php
    $users = User::whereBetween('age', [18, 30])->get(); // Lấy người dùng từ 18 đến 30 tuổi
    $users = User::whereNotBetween('age', [18, 30])->get(); // Lấy người dùng không nằm trong độ tuổi 18-30
    ```
- `whereIn` và `whereNotIn`

    Dùng để tìm các bản ghi có giá trị trong một danh sách hoặc không nằm trong một danh sách.

    ```php
    $users = User::whereIn('id', [1, 2, 3])->get(); // Lấy người dùng có ID nằm trong [1, 2, 3]
    $users = User::whereNotIn('id', [1, 2, 3])->get(); // Lấy người dùng có ID không nằm trong [1, 2, 3]
    ```
- `whereNull` và `whereNotNull`

    Dùng để tìm các bản ghi có giá trị là null và không phải null.
    
    ```php
    $users = User::whereNull('email_verified_at')->get(); // Lấy người dùng chưa xác nhận email
    $users = User::whereNotNull('email_verified_at')->get(); // Lấy người dùng đã xác nhận email
    ```

# Chuyển đổi `toArray`và `toJson`
Dùng để chuyển đổi kết quả truy vấn thành mảng hoặc JSON

```php
$users = User::all()->toArray(); // Chuyển tất cả người dùng thành mảng
$usersJson = User::all()->toJson(); // Chuyển kết quả thành JSON
```

# Liên kết bảng

-   `whereHas` dùng để search các cột trong bảng liên kết
  
    ```php
    $query->whereHas('Course', function ($query) use ($key_search){
                $query->where('name_course', 'like', '%'.$key_search.'%');
            })->get();
    ```
- `withDefault`: chỉ định một giá trị mặc định cho mối quan hệ khi không có bản ghi nào liên quan được tìm thấy.

    ```php
    class Post extends Model
    {
        public function user()
        {
            return $this->belongsTo(User::class)->withDefault([
                'name' => 'Guest User',
            ]);
        }
    }

    // Sử dụng
    $post = Post::find(1);
    echo $post->user->name; // Nếu không có user, sẽ trả về "Guest User"
    ```

- `withCount`: Đếm số lượng gì đó ở bảng liên kết

    ```php
    // tạo điều kiện liên kết

    class User extends Model
    {
        public function profile()
        {
            return $this->hasOne(Profile::class);
        }
    }


    $users = User::withCount('profile')->get(); 
    // Kết quả sẽ thêm thuộc tính profile_count, nếu người dùng có hồ sơ (profile), thì giá trị là 1, nếu không có thì giá trị là 0
    ```

- `load` và `loadMissing`: nó sẽ tải sau khi truy vấn
    - `load()`: Luôn tải mối quan hệ, ngay cả khi nó đã được load trước đó.
    
    ```php
    $user = User::find(1); // Lấy người dùng
    $user->load('posts'); // Eager load các bài viết liên quan

    // thêm query
    $user = User::find(1);
    $user->load(['posts' => function ($query) {
        $query->where('published', true); // Chỉ load các bài viết đã xuất bản
    }]);

    ```
    - `loadMissing()`: Chỉ tải các mối quan hệ chưa được load. Nếu mối quan hệ đã được load, nó sẽ bỏ qua việc tải lại mối quan hệ đó.

    ```php
    $user = User::with('posts')->find(1); // Đã load bài viết
    // Load thêm bình luận, nhưng không load lại posts
    $user->loadMissing('comments');
    ```

- `with`: liên kết bảng, sử dụng eager load (nạp trước) mối quan hệ trong lúc thực thi câu truy vấn.
  
    ```php
    $users = User::with('posts')->get();    
    // Có thể sử dụng trong modal
    /* 
        class Post extends Model
        {
            protected $with = ['user'];

            public function user()
            {
                return $this->belongsTo(User::class)->withDefault();
            }
        }

    */
    ```

    - `hasOne()`: Thiết lập quan hệ 1-1.
      
        ```php
        public function phone()
        {
            return $this->hasOne(Phone::class);
        }

        ```

    - `hasMany()`: Thiết lập quan hệ 1-nhiều.

        ```php
        public function posts()
        {
            return $this->hasMany(Post::class);
        }
        ```

    - `belongsTo()`: Thiết lập quan hệ thuộc về.
      
      ```php
        public function user()
        {
            return $this->belongsTo(User::class);
        }
      ```

    - `belongsToMany()`: Thiết lập quan hệ nhiều-nhiều.
      
        ```php
        public function relatedModel()
        {
            return $this->belongsToMany(RelatedModel::class, 'pivot_table', 'foreign_key', 'related_key');
            /*
                Các tham số
                RelatedModel::class: Tên của model liên quan (đối tác trong mối quan hệ nhiều-nhiều).
                pivot_table (Tùy chọn): Tên của bảng trung gian. Nếu không cung cấp, Laravel sẽ mặc định sử dụng tên hai bảng chính nối với nhau, ví dụ: model_a_model_b.
                foreign_key (Tùy chọn): Tên của khóa ngoại trong bảng trung gian tham chiếu đến model hiện tại. Mặc định là model_id.
                related_key (Tùy chọn): Tên của khóa ngoại trong bảng trung gian tham chiếu đến model liên quan. Mặc định là related_model_id.
            */
        }
        ```

    - `hasManyThrough()` và `hasOneThrough()`: Quan hệ 1-Nhiều thông qua một model trung gian và 1-1

        ```php
        class Country extends Model
        {
            public function posts()
            {
                return $this->hasManyThrough(Post::class, User::class);

                /*
                return $this->hasManyThrough(
                    Model::class,   // Model cuối mà muốn lấy dữ liệu
                    ModelThrough::class, // Model trung gian
                    'foreign_key_on_intermediate', // Khóa ngoại trên bảng trung gian
                    'foreign_key_on_target', // Khóa ngoại trên bảng đích
                    'local_key_on_source', // Khóa cục bộ trên bảng nguồn
                    'local_key_on_intermediate' // Khóa cục bộ trên bảng trung gian
                );

                // String based syntax...
                return $this->through('Posts')->has('Users');
                
                // Dynamic syntax...
                return $this->throughPosts()->hasUsers();
                */
            }
        }

        ```

# Truy vấn nâng cao

- `findOrFail` và `firstOrFail`: Dùng để tìm kiếm nến có thì lấy ko có thì báo lỗi về

    - `findOrFail`:  được dùng để tìm một bản ghi trong cơ sở dữ liệu dựa trên ID.

    - `firstOrFail`:  cũng tương tự như `findOrFail, nhưng nó không dựa trên ID mà dựa trên điều kiện tìm kiếm.
    
    ```php
    $user = User::findOrFail(1); // Tìm theo ID, ném lỗi nếu không tìm thấy
    $user = User::where('email', 'example@example.com')->firstOrFail(); // Tìm bản ghi đầu tiên theo điều kiện
    ```
- `firstOrCreate`: dùng để tìm kiếm hoặc tạo mới khi không thấy. Nó sẽ lưu thẳng vào database

    ```php
    $search = ["email" => "testmail2@gmail.com"];
    $save_if_search_null = [
        "full_name" => "KH Dzai",
        "password"=> "123",
        "birth_date" => '2001-04-12'
    ];
    Users::query()->firstOrCreate($search, $save_if_search_null);
    ```
- `firstOrNew`: dùng để tìm kiếm hoặc tạo mới khi không thấy. Nó chỉ tạo mới thôi không có lưu vào database.
    
    ```php
    $search = ["email" => "testmail3@gmail.com"];
    $save_if_search_null = [
        "full_name" => "KH Dzai",
        "password"=> "123",
        "birth_date" => '2001-04-12'
    ];
    $res = Users::query()->firstOrNew($search, $save_if_search_null);
    $res->save(); // Khúc này mới lưu.
    ```
- `firstOr`: Tìm bản ghi đầu tiên thỏa mãn điều kiện. Nếu không tìm thấy, có thể chỉ định một giá trị thay thế hoặc một hành động (callback) được thực thi.
  
    ```php
    $user = User::where('email', 'example@example.com')->firstOr(function () {
        // Thực hiện callback nếu không tìm thấy bản ghi
        return User::create(['email' => 'example@example.com', 'name' => 'John Doe']);
    });
    ```
- `updateOrCreate`:  kiểm tra xem một bản ghi có tồn tại hay không và nếu có, chỉ cần cập nhật nó. Nếu không, tạo mới bản ghi đó.

    ```php
    $search = ["full_name" => "KH Dzai"];
    $save_if_search_null = [
        "email" => "testmail2@gmail.com",
        "password"=> "1234",
        "birth_date" => '2001-04-12'
    ];
    $res = Users::query()->updateOrCreate($search, $save_if_search_null);
    ```

# Query Scopes (Phạm vi truy vấn):

## Local Scopes (Phạm vi cục bộ)

Local Scopes là các hàm được định nghĩa trong model để có thể tái sử dụng chúng trong các truy vấn Eloquent. Khi định nghĩa Local Scopes, chúng chỉ được áp dụng khi gọi phương thức đó.

- `scope_name`: Tạo các phương thức phạm vi để dễ dàng tái sử dụng các truy vấn.

    ```php
    // Trong model User
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    // Gọi phạm vi trong truy vấn
    $activeUsers = User::active()->get();
    ```

## Global Scopes (Phạm vi toàn cục)

Global Scopes là phạm vi áp dụng cho tất cả các truy vấn được thực hiện trên model, không cần phải gọi thủ công như Local Scopes. Chúng thường được dùng để áp dụng các điều kiện mặc định, chẳng hạn như chỉ hiển thị các bản ghi "hoạt động".

Có 2 cách: 

- Sử dụng trực tiếp
- Sử dụng qua file

Một số hàm liên quan đến Query Scopes:

- `addGlobalScope`: Dùng để thêm một phạm vi toàn cục vào model.

    ```php
    static::addGlobalScope('scope_name', function (Builder $builder) {
    // Điều kiện của Global Scope
    });
    ```
- `withGlobalScope`: Thêm một Global Scope vào truy vấn tạm thời (chỉ áp dụng trong một truy vấn cụ thể).

    ```php
    // cú pháp
    $users = User::withGlobalScope('scope_name', $customScope)->get();

    // áp dụng thực tế
    $users = User::withGlobalScope('active', function (Builder $builder) {
        $builder->where('active', 1);
    })->where('role', 'admin')->get();

    ```

- `withoutGlobalScope`: Dùng để loại bỏ một phạm vi toàn cục khỏi một truy vấn.

    ```php
    // cú pháp
    $users = User::withoutGlobalScope('scope_name')->get();
    // Thực tế với câu query
    $users = User::withoutGlobalScope('active')->where('role', 'admin')->get();

    ```
- `withoutGlobalScopes`: Dùng để loại bỏ tất cả phạm vi toàn cục khỏi một truy vấn.

    ```php
    // cú pháp
    $users = User::withoutGlobalScopes()->get();
    // Thực tế với câu query
    $users = User::withoutGlobalScopes()->where('role', 'admin')->get();
    ```
---

Ví dụ và cú pháp sử dụng:

Cách 1: trực tiếp áp dụng Global Scope trong model ví dụ với model User:

```php
class User extends Model
{
    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('active', 1);
        });
    }
}

```

Cách 2: Tạo file
1) Tạo một class Scope mới

    ```sh
    php artisan make:scope ActiveScope
    ```
2) Vào `App\Scopes` để thêm logic
    ```php
    namespace App\Scopes;

    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Scope;

    class ActiveScope implements Scope
    {
        public function apply(Builder $builder, Model $model)
        {
            $builder->where('active', 1);
        }
    }

    ```
3) Áp dụng Global Scope trong model ví dụ với model User:

    ```php
    namespace App\Models;

    use App\Scopes\ActiveScope;
    use Illuminate\Database\Eloquent\Model;

    class User extends Model
    {
        // Bây giờ, mọi truy vấn trên model User sẽ chỉ lấy những người dùng active mà không cần gọi thủ công.
        protected static function booted()
        {
            static::addGlobalScope(new ActiveScope);
        }
    }

    ```
  
# hỗ trợ trong xử lý mảng.

-   `current($value)` dùng để lấy giá trị đầu tiên trong mảng ['key' => 'value'].
-   `next($value)` dùng để lấy giá trị thứ 2 trong mảng ['key' => 'value'].
