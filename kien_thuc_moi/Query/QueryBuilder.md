# Xem Tips

- [Tạo bảng tạm](../../tips/tạo%20bảng%20tạm%20và%20insert%20update.md)

# Các Phương Thức Của Query Builder Trong Laravel

Laravel Query Builder cung cấp nhiều hàm mạnh mẽ để xây dựng và thực thi các truy vấn cơ sở dữ liệu. Dưới đây là một số hàm phổ biến của Query Builder trong Laravel:

## 1. Truy Xuất Dữ Liệu (Retrieving Data)

- `select()`: Lựa chọn các cột cần lấy.

  ```php
  DB::table('users')->select('name', 'email')->get();
  ```

- `get()`: Lấy tất cả các bản ghi.

  ```php
  DB::table('users')->get();
  ```

- `first()`: Lấy bản ghi đầu tiên.

  ```php
  DB::table('users')->first();
  ```

- `find()`: Tìm bản ghi dựa trên khóa chính.

  ```php
  DB::table('users')->find(1);
  ```

- `pluck()`: Lấy ra một cột dưới dạng mảng.

  ```php
  DB::table('users')->pluck('name');
  ```

- `value()`: Lấy giá trị của một cột từ bản ghi đầu tiên.

  ```php
  DB::table('users')->value('email');
  ```

- `distinct()`: Lấy các giá trị duy nhất.

  ```php
  DB::table('users')->distinct()->get();
  ```

## 2. Điều Kiện Truy Vấn (Query Conditions)

- `where()`: Thêm điều kiện.

  ```php
  DB::table('users')->where('age', '>', 18)->get();
  ```

- `orWhere()`: Thêm điều kiện `OR`.

  ```php
  DB::table('users')->where('age', '>', 18)->orWhere('name', 'John')->get();
  ```

- `whereIn()`: Kiểm tra giá trị có nằm trong danh sách.

  ```php
  DB::table('users')->whereIn('id', [1, 2, 3])->get();
  ```

- `whereNull()`, `whereNotNull()`: Kiểm tra giá trị null.

  ```php
  DB::table('users')->whereNull('deleted_at')->get();
  ```

- `whereBetween()`: Lọc giá trị trong một khoảng.

  ```php
  DB::table('users')->whereBetween('age', [18, 25])->get();
  ```

- `whereColumn()`: So sánh giá trị của hai cột.

  ```php
  DB::table('users')->whereColumn('created_at', 'updated_at')->get();
  ```

## 3. Sắp Xếp Và Giới Hạn Kết Quả (Ordering and Limiting Results)

- `orderBy()`: Sắp xếp kết quả.

  ```php
  DB::table('users')->orderBy('name', 'asc')->get();
  ```

- `limit()`: Giới hạn số lượng bản ghi.

  ```php
  DB::table('users')->limit(10)->get();
  ```

- `skip()`: Bỏ qua một số bản ghi đầu tiên.

  ```php
  DB::table('users')->skip(5)->take(10)->get();
  ```

- `inRandomOrder()`: Sắp xếp ngẫu nhiên các bản ghi.

  ```php
  DB::table('users')->inRandomOrder()->get();
  ```

## 4. Gộp Nhóm Và Tổng Hợp (Grouping and Aggregation)

- `groupBy()`: Nhóm các bản ghi.

  ```php
  DB::table('orders')->groupBy('user_id')->get();
  ```

- `having()`: Thêm điều kiện sau khi gộp nhóm.

  ```php
  DB::table('orders')->groupBy('user_id')->having('total', '>', 100)->get();
  ```

- `count()`, `sum()`, `avg()`, `min()`, `max()`: Các hàm tổng hợp.

  ```php
  DB::table('orders')->count();
  ```

## 5. Kết Nối Bảng (Joins)

- `join()`: Kết nối bảng.

  ```php
  DB::table('users')
      ->join('posts', 'users.id', '=', 'posts.user_id')
      ->select('users.*', 'posts.title')
      ->get();
  ```

- `leftJoin()`, `rightJoin()`: Kết nối bảng kiểu LEFT JOIN hoặc RIGHT JOIN.

  ```php
  DB::table('users')
      ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
      ->get();
  ```

- `crossJoin()`: Kết nối kiểu CROSS JOIN.

  ```php
  DB::table('sizes')
      ->crossJoin('colors')
      ->get();
  ```

- `joinSub()`: Kết nối với một truy vấn con.

  ```php
  DB::table('users')
      ->joinSub(DB::table('posts')->select('user_id', 'title'), 'latest_posts', function ($join) {
          $join->on('users.id', '=', 'latest_posts.user_id');
      })
      ->get();
  ```

## 6. Thực Hiện Chỉnh Sửa Dữ Liệu (Updating Data)

- `insert()`: Thêm bản ghi mới.

  ```php
  DB::table('users')->insert([
      'name' => 'John Doe',
      'email' => 'john@example.com'
  ]);
  ```

- `update()`: Cập nhật bản ghi.

  ```php
  DB::table('users')->where('id', 1)->update(['name' => 'Jane Doe']);
  ```

- `delete()`: Xóa bản ghi.

  ```php
  DB::table('users')->where('id', 1)->delete();
  ```

- `increment()`, `decrement()`: Tăng hoặc giảm giá trị của một cột.

  ```php
  DB::table('users')->where('id', 1)->increment('votes');
  ```

## 7. Khóa Giao Dịch (Transactions)

- `transaction()`: Thực hiện các thao tác trong một giao dịch.

  ```php
  DB::transaction(function () {
      DB::table('users')->update(['votes' => 1]);
      DB::table('posts')->delete();
  });
  ```

## 8. Kiểm Tra Sự Tồn Tại (Existence Check)

- `exists()`: Kiểm tra xem có bản ghi nào tồn tại hay không.

  ```php
  $exists = DB::table('users')->where('email', 'john@example.com')->exists();
  ```

- `doesntExist()`: Kiểm tra xem không có bản ghi nào tồn tại.

  ```php
  $notExists = DB::table('users')->where('email', 'john@example.com')->doesntExist();
  ```

## 9. Phân Trang (Pagination)

- `paginate()`: Phân trang kết quả.

  ```php
  $users = DB::table('users')->paginate(15);
  ```

- `simplePaginate()`: Phân trang đơn giản hơn.

  ```php
  $users = DB::table('users')->simplePaginate(15);
  ```

## 10. Kết Hợp (Union)

- `union()`: Kết hợp kết quả từ nhiều truy vấn.

  ```php
  $first = DB::table('users')->whereNull('first_name');
  $users = DB::table('users')->whereNull('last_name')->union($first)->get();
  ```

## 11. Thực Thi

- **`DB::statement()`**: Được sử dụng để thực thi các câu lệnh SQL không trả về kết quả trực tiếp (ví dụ: `CREATE TABLE`, `DROP TABLE`, `ALTER TABLE`, `INSERT`, `UPDATE`, `DELETE`)

  ```php
    DB::statement('DROP TABLE IF EXISTS users');
    DB::statement('CREATE TABLE users (id INT, name VARCHAR(255))');
  ```

- **`DB::raw()`**: Được sử dụng để chèn một biểu thức SQL trực tiếp vào một truy vấn

  ```php
      $users = DB::table('users')
                  ->select(DB::raw('COUNT(*) as user_count, status'))
                  ->groupBy('status')
                  ->get();
  ```
