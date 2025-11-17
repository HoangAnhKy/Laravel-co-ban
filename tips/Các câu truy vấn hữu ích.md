
- [Tạo bảng tạm](#tạo-bảng-tạm)
- [insert chia nhỏ](#insert-chia-nhỏ)
- [Câu lệnh update hoặc insert](#câu-lệnh-update-hoặc-insert)
- [join update](#join-update)
- [INSERT IGNORE](#insert-ignore)
- [Xoay bảng tự động theo cột](#xoay-bảng-tự-động-theo-cột)
  - [Nguyên lý](#nguyên-lý)
    - [Tạo pivot columns động](#tạo-pivot-columns-động)
    - [Tạo query chính](#tạo-query-chính)
    - [Thực thi](#thực-thi)
  - [Logic vận hành tổng thể](#logic-vận-hành-tổng-thể)
  - [SQL Demo](#sql-demo)
  - [Laravel Demo](#laravel-demo)
- [insertUsing](#insertusing)
- [so sánh và tìm hiểu `cursorPaginate` và `paginate`](#so-sánh-và-tìm-hiểu-cursorpaginate-và-paginate)
- [join bảng theo union và cross join](#join-bảng-theo-union-và-cross-join)
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

# Xoay bảng tự động theo cột

ví dụ: 

- Dữ liệu gốc (dt_delivery_data + mst_customer) có dạng:

| real_date  | prefecture | company_name | customer_id | goods_price |
| ---------- | ---------- | ------------ | ----------- | ----------- |
| 2025-07-01 | Tokyo      | A Corp       | 1           | 1000        |
| 2025-07-01 | Tokyo      | B Corp       | 2           | 2000        |


- khi xoay 

| ym      | prefecture | A Corp_customer_id | A Corp_goods_price | B Corp_customer_id | B Corp_goods_price |
| ------- | ---------- | ------------------ | ------------------ | ------------------ | ------------------ |
| 2025/07 | Tokyo      | 1                  | 1000               | 1                  | 2000               |


## Nguyên lý

### Tạo pivot columns động

```sql
SET @sql = NULL;

SELECT GROUP_CONCAT(
    DISTINCT CONCAT(
        'Count(CASE WHEN t.company_name = ''', company_name, ''' THEN t.customer_id END) AS `', company_name, '`, ',
        'SUM(CASE WHEN t.company_name = ''', company_name, ''' THEN t.goods_price ELSE 0 END) AS `', company_name, '`'
    )
) INTO @sql
FROM (
    SELECT DISTINCT c.company_name
    FROM dt_delivery_data d
    LEFT JOIN mst_customer c ON c.customer_id = d.customer_id
) AS company_list;
```
Giải thích:

- `SELECT DISTINCT c.company_name ...` Lấy tất cả tên công ty duy nhất từ dữ liệu.

- `GROUP_CONCAT(CONCAT(...))` Tạo ra một chuỗi SQL gồm các cột pivot

    ```sql
    Count(CASE WHEN t.company_name = 'A Corp' THEN t.customer_id END) AS `A Corp`,
    SUM(CASE WHEN t.company_name = 'A Corp' THEN t.goods_price ELSE 0 END) AS `A Corp`,
    Count(CASE WHEN t.company_name = 'B Corp' THEN t.customer_id END) AS `B Corp`,
    SUM(CASE WHEN t.company_name = 'B Corp' THEN t.goods_price ELSE 0 END) AS `B Corp`
    ```
- `CASE WHEN ... THEN ... END` là cách tính có điều kiện: Chỉ tính customer_id hoặc goods_price khi company_name trùng với công ty hiện tại.

- `GROUP_CONCAT` nối tất cả các cột pivot thành một chuỗi dài để dùng trong query cuối cùng.
- `@sql` sẽ giữ chuỗi SQL này, ví dụ:`@sql = "Count(CASE WHEN t.company_name = 'A Corp' THEN t.customer_id END) AS `A Corp`, SUM(CASE WHEN t.company_name = 'A Corp' THEN t.goods_price ELSE 0 END) AS `A Corp`, ..."`

### Tạo query chính

```sql
SET @sql = CONCAT(
'SELECT DATE_FORMAT(t.real_date, ''%Y/%m'') AS ym,
        t.prefecture, ',
@sql, '
 FROM (
    SELECT d.real_date,
           c.prefecture,
           c.company_name,
           d.customer_id,
           d.goods_price
    FROM dt_delivery_data d
    LEFT JOIN mst_customer c ON c.customer_id = d.customer_id
) AS t
GROUP BY ym, t.prefecture
ORDER BY ym, t.prefecture;'
);
```

- `CONCAT(...)` nối chuỗi SQL để tạo full query.
- `@sql` Chèn các cột pivot đã tạo.

### Thực thi

```sql
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
```


## Logic vận hành tổng thể
- Xác định danh sách cột cần pivot (từ công ty).
- Tạo chuỗi SQL cho các cột pivot bằng GROUP_CONCAT.
- Chèn chuỗi pivot vào query chính, dùng CASE WHEN để tính COUNT và SUM.
- Nhóm dữ liệu theo tháng và tỉnh.
- Thực thi query động bằng PREPARE → EXECUTE.

## SQL Demo

```sql
-- 1. Tạo các cột pivot cho customer_id và goods_price
SET @sql = NULL;

SELECT GROUP_CONCAT(
    DISTINCT CONCAT(
        'Count(CASE WHEN t.company_name = ''', company_name, ''' THEN t.customer_id END) AS `', company_name, '`, ',
        'SUM(CASE WHEN t.company_name = ''', company_name, ''' THEN t.goods_price ELSE 0 END) AS `', company_name, '`'
    )
) INTO @sql
FROM (
    SELECT DISTINCT c.company_name
    FROM dt_delivery_data d
    LEFT JOIN mst_customer c ON c.customer_id = d.customer_id
) AS company_list;

-- 2. Chuẩn bị query chính
SET @sql = CONCAT(
'SELECT DATE_FORMAT(t.real_date, ''%Y/%m'') AS ym,
        t.prefecture, ',
@sql, '
 FROM (
    SELECT d.real_date,
           c.prefecture,
           c.company_name,
           d.customer_id,
           d.goods_price
    FROM dt_delivery_data d
    LEFT JOIN mst_customer c ON c.customer_id = d.customer_id
) AS t
GROUP BY ym, t.prefecture
ORDER BY ym, t.prefecture;'
);

-- 3. Thực thi dynamic SQL
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
```

## Laravel Demo

```php
Route::get('/pivot', function () {
    // Cho phép GROUP_CONCAT dài hơn mặc định
    DB::statement("SET SESSION group_concat_max_len = 1000000");

    // Lấy phần SQL động — ép về string rõ ràng
    $sqlDynamic = DB::table('course as c')
        ->join('users as u', 'u.course_id', '=', 'c.id')
        ->selectRaw("
            GROUP_CONCAT(
                DISTINCT CONCAT(
                    'COUNT(CASE WHEN c.name = ''', c.name, ''' THEN u.id END) AS `', c.name, '_total`, ',
                    'COUNT(CASE WHEN c.name = ''', c.name, ''' AND u.sex = ''female'' THEN u.id END) AS `', c.name, '_female`'
                )
                SEPARATOR ', '
            ) as cols
        ")
        ->first();

    // Nếu không có dữ liệu → báo lỗi
    if (!$sqlDynamic || !$sqlDynamic->cols) {
        return response()->json(['error' => 'Không có dữ liệu pivot.'], 400);
    }

    // Convert Expression thành string
    $cols = (string) $sqlDynamic->cols;

    // Ghép query động
    $query2 = "
        SELECT
            DATE_FORMAT(c.created_at, '%Y-%m') AS month,
            {$cols}
        FROM users u
        JOIN course c ON u.course_id = c.id
        GROUP BY DATE_FORMAT(c.created_at, '%Y-%m')
        ORDER BY month;
    ";

    // Thực thi SQL
    $result = DB::select($query2);

    // Trả kết quả
    return response()->json($result);
});
```

# insertUsing

Dùng để chèn dữ liệu vào một bảng bằng kết quả của truy vấn SELECT `(tức là INSERT INTO ... SELECT ... trong SQL)`, phải đặt theo thứ tự

- kiểu `query builder`
  
    ```php
    DB::table('orders')->insertUsing(
        ['user_id', 'status', 'total_price', 'created_by', 'created_at', 'updated_at'],
        DB::table('carts')->select(
                'user_id',
                DB::raw(config('define.ORDERS_STATUS.PENDING') . ' as status'),
                'total_price',
                DB::raw(Auth::id() . ' as created_by'),
                DB::raw('NOW() as created_at'),
                DB::raw('NOW() as updated_at')
            )
            ->where('id', $cartID)
    );
    ```

- Kiểu query thuần

    ```php
    $status = config('define.ORDERS_STATUS.PENDING');
    $userId = Auth::id();
    $sql = "
        INSERT INTO orders (user_id, status, total_price, created_by, created_at, updated_at)
        SELECT user_id, ?, total_price, ?, NOW(), NOW()
        FROM carts
        WHERE id = ?
    ";
    return DB::statement($sql, [$status, $userId, $cartID]);
    ```

# so sánh và tìm hiểu `cursorPaginate` và `paginate`
- `paginate`: khi page càng lớn, OFFSET càng lớn → query chậm, scan nhiều bản ghi.

    ```php
    $users = User::paginate(10); // SELECT * FROM users LIMIT 10 OFFSET 100;
    ```
-  `cursorPaginate`: Query sẽ dùng WHERE id > last_seen_id LIMIT 10 thay vì OFFSET → rất nhanh. **CursorPaginate chỉ an toàn khi cột dùng làm con trỏ là unique**

    ```php
    $users = User::orderBy('id')->cursorPaginate(10); // SELECT * FROM users WHERE id > 10 ORDER BY id ASC LIMIT 10;
    ```

# join bảng theo union và cross join

điều kiện: lấy toàn bộ tên với product trong năm, db chỉ có năm 2025 không có 2024 nhưng vẫn phải lấy

```sql
SELECT 
    y.year,
    p.product_code,
    COALESCE(
        SUM(CASE 
            WHEN d.month BETWEEN 1 AND 3 THEN d.amount 
            ELSE 0 
        END), 0
    ) AS q1
FROM mst_product p
CROSS JOIN (SELECT 2024 AS year UNION ALL SELECT 2025) y
LEFT JOIN dt_statistics_cus d 
    ON d.product_code = p.product_code 
   AND d.year = y.year
WHERE p.product_code = '01AJA40720MC21-A-T'
GROUP BY y.year, p.product_code  
ORDER BY y.year ASC;
```

cách 2:


```sql
SELECT 
    2024 AS year,
    p.product_code,
    COALESCE(SUM(CASE WHEN d.month BETWEEN 1 AND 3 THEN d.amount ELSE 0 END), 0) AS q1
FROM mst_product p
LEFT JOIN dt_statistics_cus d 
    ON d.product_code = p.product_code AND d.year = 2024
WHERE p.product_code = '01AJA40720MC21-A-T'
GROUP BY p.product_code

UNION ALL

SELECT 
    2025 AS year,
    p.product_code,
    COALESCE(SUM(CASE WHEN d.month BETWEEN 1 AND 3 THEN d.amount ELSE 0 END), 0) AS q1
FROM mst_product p
LEFT JOIN dt_statistics_cus d 
    ON d.product_code = p.product_code AND d.year = 2025
WHERE p.product_code = '01AJA40720MC21-A-T'
GROUP BY p.product_code
ORDER BY year;
```

Code demo bằng laravel

```php

Route::get("/test", function () {
    $year = 2025;
    $years = [$year, $year - 1];

    $query = \App\Models\MstProduct::query()
        ->select([
            DB::raw("y.year AS year"),
            "mst_product.product_code",
            "mst_product.product_name",
            DB::raw("COALESCE(SUM(CASE WHEN dt_statistics_cus.month BETWEEN 1 AND 3 THEN dt_statistics_cus.count ELSE 0 END), 0) AS count_q1"),
            DB::raw("COALESCE(SUM(CASE WHEN dt_statistics_cus.month BETWEEN 4 AND 6 THEN dt_statistics_cus.count ELSE 0 END), 0) AS count_q2"),
            DB::raw("COALESCE(SUM(CASE WHEN dt_statistics_cus.month BETWEEN 7 AND 9 THEN dt_statistics_cus.count ELSE 0 END), 0) AS count_q3"),
            DB::raw("COALESCE(SUM(CASE WHEN dt_statistics_cus.month BETWEEN 10 AND 12 THEN dt_statistics_cus.count ELSE 0 END), 0) AS count_q4"),
            DB::raw("COALESCE(SUM(CASE WHEN dt_statistics_cus.month BETWEEN 1 AND 12 THEN dt_statistics_cus.count ELSE 0 END), 0) AS count_total"),
        ])
        ->crossJoin(DB::raw('(SELECT ' . implode(' AS year UNION ALL SELECT ', $years) . ' AS year) y'))
        ->leftJoin('dt_statistics_cus', function ($join) {
            $join->on('dt_statistics_cus.product_code', '=', 'mst_product.product_code')
                ->on('dt_statistics_cus.year', '=', DB::raw('y.year'));
        })
        ->groupBy('y.year', 'mst_product.product_code', 'mst_product.product_name')
        ->orderBy('mst_product.product_code')
        ->orderBy('y.year', 'desc');

    $data = $query->paginate(10)->getCollection()->toArray();

    dd($data);
});
```