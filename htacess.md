`.htaccess` là một file cấu hình đặc biệt được sử dụng bởi máy chủ Apache. File này cho phép bạn thực hiện các cấu hình tùy chỉnh cho từng thư mục trên server, chẳng hạn như:
- Quản lý đường dẫn URL (URL rewriting).
- Chặn truy cập vào các file hoặc thư mục.
- Thiết lập chuyển hướng URL (301, 302).
- Thiết lập các quy tắc bảo mật (yêu cầu xác thực người dùng).
- Quản lý caching, gzip nén file để tăng tốc độ tải trang.
- Định nghĩa các trang lỗi tùy chỉnh như 404, 403.

Apache sẽ đọc file `.htaccess` khi có yêu cầu truy cập vào thư mục chứa nó, sau đó áp dụng các quy tắc được định nghĩa trong đó

# Cách Dùng

1. Kích hoạt URL Rewriting (định tuyến đường dẫn đẹp)

thay vì `http://yourwebsite.com/index.php?page=about` thì ta có thể dùng `http://yourwebsite.com/about` bằng cách cấu hình `.htacess`

```htacess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /index.php?url=$1 [QSA,L]
```
chú ý: 

RewriteEngine On: Bật chế độ viết lại URL. RewriteEngine chỉ có hai chế độ: On (bật) và Off (tắt).

RewriteCond: Kiểm tra nếu file hoặc thư mục không tồn tại(chỉ thực hiện khi thỏa mãn điều kiện).
- `-f` là kiểm tra file, nếu có `!` ở trước là `false` mới thực thi và ngược lại
- `-d` là kiểm tra thư mục, nếu có `!` ở trước là `false` mới thực thi và ngược lại

 - `=` là so sánh bằng với một chuỗi, ví dụ RewriteCond %{HTTP_USER_AGENT} =Mozilla là HTTP_USER_AGENT bằng "Mozilla" mới thực thi

 - có thể dùng biểu thức chính quy(Regex) để check

 - Chi tiết của `%{Type}`

    | Biến môi trường        | Ý nghĩa                                                   | Ví dụ giá trị                                    |
    |------------------------|-----------------------------------------------------------|--------------------------------------------------|
    | **`REQUEST_URI`**       | Đường dẫn và chuỗi truy vấn từ yêu cầu URL                | `/products/view.php?id=123`                      |
    | **`HTTP_HOST`**         | Tên miền hoặc địa chỉ IP của máy chủ                      | `example.com`                                    |
    | **`QUERY_STRING`**      | Chuỗi truy vấn từ URL                                     | `term=apple&category=fruit`                      |
    | **`REQUEST_FILENAME`**  | Đường dẫn tuyệt đối tới file trên hệ thống                | `/var/www/html/products/view.php`                |
    | **`REMOTE_ADDR`**       | Địa chỉ IP của client (người dùng)                        | `192.168.1.10`                                   |
    | **`HTTP_USER_AGENT`**   | Chuỗi mô tả trình duyệt/hệ điều hành của người dùng       | `Mozilla/5.0 (Windows NT 10.0; Win64; x64)`      |
    | **`SERVER_PORT`**       | Cổng máy chủ mà Apache đang lắng nghe                     | `80` (HTTP) hoặc `443` (HTTPS)                   |
    | **`HTTP_REFERER`**      | URL của trang web dẫn người dùng đến trang hiện tại       | `https://www.google.com/search?q=apache+rewrite` |


RewriteRule: Quy tắc viết lại URL.
- `$1` là tham chiếu tới chuỗi con trong mẫu biểu thức chính quy của RewriteRule. Ví dụ `RewriteRule ^post/([0-9]+)$ post.php?id=$1 [L]`
là đang so sánh nhóm con đầu tiên khớp với chuỗi các số (0-9)

- `[]` là các cờ của nó, các cờ này có tác dụng kiểm soát và điều chỉnh cách Apache xử lý quy tắc.
Gồm: 
    - `L`:  Last rule (quy tắc cuối cùng) nếu một quy tắc được đánh dấu L và khớp, Apache sẽ không kiểm tra các quy tắc khác
    - `QSA`: Query String Append (nối chuỗi truy vấn). Nối các chuỗi truy vấn hiện có từ URL gốc vào URL mới thay vì thay thế hoàn toàn chuỗi truy vấn, ví dụ nếu truy cập URL `http://example.com/search/books?category=fiction`, nó sẽ chuyển thành `search.php?term=books&category=fiction.`
    - `R`: Redirect (chuyển hướng) Mặc định là chuyển hướng 302 (tạm thời), nhưng bạn có thể sử dụng R=301 để chuyển hướng 301 (vĩnh viễn).
    - `NC`: No Case (không phân biệt chữ hoa và chữ thường)
    - `P`: Proxy (định tuyến qua proxy). Ví dụ `RewriteRule ^service/(.*)$ http://example.com/service/$1 [P]` yêu cầu tới `/service/abc` sẽ được chuyển qua proxy tới `http://example.com/service/abc`.
    - `F`: Forbidden (cấm truy cập). Ví dụ `RewriteRule ^private/ - [F]
` Chặn tất cả các yêu cầu tới đường dẫn bắt đầu bằng `/private/`
    - `QSD`: Query String Discard (loại bỏ chuỗi truy vấn) Cờ này cho phép loại bỏ chuỗi truy vấn (query string) hiện tại khỏi URL.
2. Chuyển hướng (Redirect) URL

```htacess
Redirect 301 /old-page.html http://yourwebsite.com/new-page.html

// lưu ý
// Redirect 301: Chuyển hướng vĩnh viễn (301).
// /old-page.html: Đường dẫn trang cũ.
// http://yourwebsite.com/new-page.html: Đường dẫn trang mới.
```

3. Cấm truy cập vào một file hoặc thư mục
```htacess
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>

// lưu ý
// Files "config.php": Quy tắc áp dụng cho file config.php.
// Deny from all: Chặn mọi truy cập vào file này.
// Quy tắc Allow được áp dụng trước, sau đó đến Deny. Kết quả cuối cùng sẽ là Deny nếu không có quy tắc Allow nào khớp.
```

4. Bảo vệ thư mục bằng mật khẩu

- yêu cầu người dùng nhập tên người dùng và mật khẩu khi truy cập vào một thư mục.

```htacess
AuthType Basic
AuthName "Restricted Area"
AuthUserFile /path/to/.htpasswd
Require valid-user


// lưu ý

// AuthType Basic: Xác định loại xác thực là "Basic" (cơ bản).

// AuthName: định nghĩa tên của khu vực được bảo vệ, và sẽ xuất hiện trong hộp thoại xác thực của trình duyệt khi yêu cầu người dùng đăng nhập

// AuthUserFile: Chỉ đường dẫn đến file .htpasswd chứa thông tin người dùng.

// Require valid-user: Yêu cầu tất cả các người dùng trong file .htpasswd phải có thông tin hợp lệ để truy cập.

// File .htpasswd chứa tên người dùng và mật khẩu mã hóa có thể được tạo bằng các công cụ như
```

trong `htpasswd` chứa các thông tin như sau
```htpasswd
admin:$apr1$zG84Fp7X$O0DzXZ5Qwz2eE9zOdxtUJ0
user:$apr1$5TPzU8wA$xqHn83oV2Iwhkn9j.ugHM.

// admin và user là tên người dùng. Các chuỗi mã hóa sau dấu : là mật khẩu được mã hóa.
```

5. Tạo trang lỗi tùy chỉnh (Custom Error Pages)

```htaccess
ErrorDocument 404 /404.html
ErrorDocument 403 /403.html
// lưu ý
// khi xảy ra lỗi 404 sẽ gọi 404.html
```