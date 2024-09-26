# khai báo 

- require và require_once: Dùng khi tệp bắt buộc phải có để chương trình hoạt động. Nếu tệp bị thiếu, chương trình sẽ dừng.

- include và include_once: Dùng khi tệp không bắt buộc, chương trình vẫn có thể tiếp tục chạy nếu tệp bị thiếu.

- _once: Đảm bảo tệp chỉ được bao gồm một lần duy nhất, ngay cả khi có nhiều lời gọi trong mã.

# Object
## property_exists()
 
Mô tả: Dùng để kiểm tra nó có tồn tại trong object ko

cách dùng :

```php
property_exists(object, string);
```

## get_object_vars()

Mô tả:  để chuyển object thành mảng

cách dùng:

```php
get_object_vars(object);
```


# function

## call_user_func / call_user_func_array

Mô tả: dùng để gọi một hàm thông qua biến hoặc tên hàm được lưu trong biến, đặc biệt là khi bạn có danh sách các đối số động.
- call_user_func_array (trường hợp có nhiều tham số)
- call_user_func (trường hợp có một số)

ví dụ:

```php
function multiply($a, $b) {
    return $a * $b;
}

$functionName = 'multiply';
$args = [3, 4];

echo call_user_func_array($functionName, $args);  // Output: 12
```

## __get()

Mô tả: là một magic method trong PHP, được tự động gọi khi cố gắng truy cập một thuộc tính không tồn tại hoặc không thể truy cập trực tiếp trong một đối tượng. Hàm này thường được sử dụng khi muốn kiểm soát cách thức truy cập các thuộc tính của một đối tượng, chẳng hạn như khi muốn truy cập các phương thức dưới dạng thuộc tính, hoặc khi muốn triển khai logic truy xuất dữ liệu động.

cú pháp:

```php
public function __get($name) {
    // Logic
}
```

Ví dụ 

```php
// muốn truy cập các phương thức dưới dạng thuộc tính
public function __get($property)
    {
        if (method_exists($this, $property)) {
            return $this->$property();
        }
    }

```