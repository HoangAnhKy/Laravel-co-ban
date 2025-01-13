Dùng như `method get` bình thường, để trước proprety nào thì proprety đó có thể truy vấn query trên url

# cú pháp

```php
use Livewire\Attributes\Url;
use Livewire\Component;
 
class ShowUsers extends Component
{
    #[Url]
    public $search = '';
 
    // ...
}
```

# props đi kèm

## as 

- Dùng để gán nó thành cái gì đó.
- Ví dụ: `/users?q=bob` <= `?search=bob.`

    ```php
    #[Url(as: 'q')]
    public $search = '';
    ```

## keep

- Hiện thị khi tải trang, nếu không có gì nó sẽ là `?search=""`

    ```php
    #[Url(keep: true)]
    public $search = '';
    ```

## history

- Lưu trữ trong lịch sử khi quay lại sẽ dùng url trước đó

    ```php
     #[Url(history: true)]
    public $search = '';
    ```

# Sử dụng phương thức queryString

Chuỗi truy vấn cũng có thể được định nghĩa là một phương thức trên thành phần. Điều này có thể hữu ích nếu một số thuộc tính có tùy chọn động.

```php
protected function queryString()
{
    return [
        'search' => [
            'as' => 'q',
        ],
    ];
}
```
