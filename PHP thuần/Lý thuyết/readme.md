# OOP

## Khái niệm

OOP (Object-Oriented Programming) là một phương pháp lập trình dựa trên việc sử dụng đối tượng (object) và lớp (class) để tổ chức mã nguồn và xây dựng các ứng dụng. 

Gồm 4 tính chất:

- Encapsulation (Đóng gói): ẩn thông tin nội bộ của đối tượng và chỉ cung cấp các phương thức để truy cập hoặc thay đổi các giá trị của nó

    - private: chỉ một mình sài
    
    - protected: lớp cha với lớp con sài

    - public: mọi người đều sài
    
- Inheritance (Kế thừa): cho phép lớp con kế thừa toàn bộ các logic từ lớp cha

    ```php
    // demo kế thừa và dùng tính đóng gói
    <?php

    class ParentDemo {
        private $private_val = "private";
        protected $protected_val = "protected";

        public function getPrivateVal() {
            return $this->private_val; // chỉ có thể lấy khi dùng public lấy private
        }
    }

    class ChillDemo extends ParentDemo{

        
        public function __construct(){

        }

        public function echoProtected(){
            return $this->protected_val; // chỉ có thể dùng protected khi kế thừa
        }
    }

    $chil = new ChillDemo();

    echo $chil->echoProtected();
    echo $chil->getPrivateVal();
    ```
- Polymorphism (Đa hình): cho phép đối tượng kế thừa và tùy chỉnh lại logic cho phù hợp

    ```php

    <?php
    class Animal {
        public function speak() {
            echo "The animal makes a sound.\n";
        }
    }

    class Dog extends Animal {
        public function speak() {
            echo "The dog barks.\n";
        }
    }

    class Cat extends Animal {
        public function speak() {
            echo "The cat meows.\n";
        }
    }

    $animal = new Animal();
    $dog = new Dog();
    $cat = new Cat();

    $animal->speak(); // "The animal makes a sound."
    $dog->speak();    // "The dog barks."
    $cat->speak();    // "The cat meows."
    ?>
    ```
- Abstraction (Trừu tượng): cho phép ẩn đi những chi tiết thực thi phức tạp và chỉ cung cấp giao diện đơn giản để người dùng sử dụng. Điều này thường được thực hiện thông qua lớp trừu tượng (abstract class) hoặc giao diện (interface).

    **Lưu ý**: Khi khai báo một lớp trừu tượng (abstract class) với các phương thức trừu tượng (abstract methods), lớp con (subclass) buộc phải cài đặt các phương thức này

- ví dụ về abstract

    ```php
    <?php

    abstract class Animal{
        abstract public function sound();
    }

    class Dog extends Animal{
        public function sound(){
            return "Woof";
        }
    }

    $dog = new Dog();

    echo $dog->sound();
    ```

- ví dụ về interface

    ```php
    <?php

    interface Animal{
        public function sound();
    }

    class Dog implements Animal{
        public function sound(){
            return "Woof";
        }
    }

    $dog = new Dog();

    echo $dog->sound();
    ```

- so sánh `abstract` với `interface`

    | Đặc điểm                        | **abstract class**                                                   | **interface**                                                      |
    |----------------------------------|-----------------------------------------------------------------------|--------------------------------------------------------------------|
    | **Khái niệm**                   | Là lớp không thể khởi tạo đối tượng trực tiếp, có thể chứa cả phương thức có thân và phương thức trừu tượng. | Là một hợp đồng yêu cầu các lớp thực thi phải cài đặt các phương thức khai báo trong interface. |
    | **Có thể chứa thuộc tính**       | Có thể chứa thuộc tính (properties).                                 | Không thể chứa thuộc tính (properties).                            |
    | **Có thể chứa phương thức**      | Có thể chứa cả phương thức có thân (đã cài đặt) và phương thức trừu tượng (chưa cài đặt). | Chỉ chứa phương thức khai báo (không có thân).                    |
    | **Có thể có constructor**       | Có thể chứa hàm khởi tạo (constructor).                              | Không thể có constructor.                                         |
    | **Có thể kế thừa từ lớp khác**   | Có thể kế thừa từ một lớp (class) khác (PHP chỉ hỗ trợ kế thừa đơn).  | Không thể kế thừa từ lớp khác. Nhưng một lớp có thể thực thi nhiều interface. |
    | **Có thể thực thi nhiều interface không?** | Không thể thực thi nhiều interface.                                  | Có thể thực thi nhiều interface.                                  |
    | **Sử dụng khi nào?**            | Khi muốn định nghĩa một lớp cơ sở chung cho các lớp con, có thể chứa cả phương thức đã cài đặt và phương thức trừu tượng. | Khi muốn định nghĩa một hợp đồng (contract) mà các lớp thực thi phải tuân theo, mà không quan tâm đến cách các phương thức được cài đặt. |
    | **Kế thừa**                     | Lớp kế thừa từ một lớp abstract có thể kế thừa một số phương thức đã cài đặt trong lớp cha. | Lớp thực thi interface phải cài đặt tất cả các phương thức trong interface. |
    | **Có thể có hằng số**          | Có thể chứa hằng số (constants).                                    | Có thể chứa hằng số (constants).                                  |
    | **Tính linh hoạt**             | Một lớp chỉ có thể kế thừa một lớp abstract (PHP chỉ hỗ trợ kế thừa đơn), nhưng có thể sử dụng các interface. | Lớp có thể thực thi nhiều interface cùng lúc, giúp tăng tính linh hoạt. |
    | **Tính kế thừa và tái sử dụng** | Thích hợp cho trường hợp có sự kế thừa và tái sử dụng mã.             | Thích hợp cho việc chuẩn hóa hành vi giữa các lớp không có quan hệ kế thừa. |


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

Mô tả: dùng để gọi một hàm thông qua biến hoặc tên hàm được lưu trong biến, đặc biệt là khi có danh sách các đối số động.
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