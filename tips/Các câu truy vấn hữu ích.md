
- [Tạo bảng tạm](#tạo-bảng-tạm)
- [insert chia nhỏ](#insert-chia-nhỏ)
- [Câu lệnh update hoặc insert](#câu-lệnh-update-hoặc-insert)
- [join update](#join-update)
- [INSERT IGNORE](#insert-ignore)
***

# Tạo bảng tạm

- Chỉ có hiệu lực từ khi tạm khi đóng MYSQL hoặc xog tiến trình

  ```php
  DB::statement('CREATE TEMPORARY TABLE temp_users LIKE users');  // tạo bảng tạm
  DB::statement('ALTER TABLE temp_users MODIFY id INT NOT NULL'); // xóa AUTO_INCREMENT
  DB::statement('ALTER TABLE temp_users DROP PRIMARY KEY'); // xóa key

  DB::table('temp_users')->insert([
      ["id"=>1, "name" => "John", "email" => "john@yopmail", "password" => "123"]
  ]);

  dd(DB::table('temp_users')->get());
  ```

# insert chia nhỏ

```php
$batchSize = 10000; // Chèn mỗi lần 10.000 dòng
DB::statement("INSERT INTO table (col1, col2, ...) SELECT col1, col2, ... FROM table_clone LIMIT $batchSize");
```

# Câu lệnh update hoặc insert

- Insert từ bảng phụ qua bảng chính với các `field` chỉ định và `update` nếu trung `key`

  ```php
  <!-- DB::statement("INSERT INTO $TABLE
              SELECT * FROM $TABLE_CLONE
              ON DUPLICATE KEY UPDATE
                  CÁC CỘT CẦN UPDATE
              ") -->

  DB::statement("INSERT INTO $name_table (`product_code`, `product_name`, `product_jan`,
                  `maker_id`, `stock_id`, `maker_cd`, `product_image_url`,
                  `stock_num`, `received_num`, `shipped_num`, `receive_plan_num`,
                  `ship_plan_num`, `free_num`, `quantity_per_case`, `size`, `units`,
                  `in_date`, `in_ope_cd`, `up_date`, `up_ope_cd`)

              SELECT `product_code`, `product_name`, `product_jan`, `maker_id`, `stock_id`,
                      `maker_cd`, `product_image_url`, `stock_num`, `received_num`,
                      `shipped_num`, `receive_plan_num`, `ship_plan_num`, `free_num`,
                      `quantity_per_case`, `size`, `units`, `in_date`, `in_ope_cd`,
                      `up_date`, `up_ope_cd`

              FROM $name_table_clone AS source

              ON DUPLICATE KEY UPDATE
                  ball_to_num = source.ball_to_num,
                  case_to_ball = source.case_to_ball,
                  size = source.size,
                  note = source.note;"
              );

  ```

# join update

```php
function joinUpdateCourseAndUser () {
    $course_update = 6;
    $query = DB::table("courses")
        ->join('users', 'users.course_id', '=', 'courses.id')
        ->update([
            "users.course_id" => $course_update,
            "courses.id" => $course_update,
            "courses.name" => "Course $course_update",
            "courses.description" => "Course $course_update",
        ]);
    return response()->json($query);
}
```

# INSERT IGNORE 

```js
INSERT IGNORE INTO users (id, name) VALUES (1, 'John');
```