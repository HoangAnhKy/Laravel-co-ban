# Cài đặt chứng chỉ SSL vào Windows

- Tìm file: `tên_ứng_dụng.test.crt`, nhấp đúp để mở lên.
- Bấm vào nút "**Install Certificate..**". Sau đó chọn "**Local Machine**" → "**Place all certificates in the following store**"
- Chọn tiếp "**Trusted Root Certification Authorities**" → "**Next**" → "**Finish**"
- Khởi động lại trình duyệt, sau lần này SSL của sẽ được trình duyệt công nhận hoàn toàn

## Openssl [LinkDown](https://slproweb.com/products/Win32OpenSSL.html)

- Tạo file `san.cnf` trong folder đó trước.
- Vì `SAN` cho phép chứng chỉ hoạt động với nhiều tên miền, Nếu không có `SAN`, trình duyệt sẽ cảnh báo lỗi "Subject Alternative Name missing".

  ```ini
  ;Cấu hình yêu cầu chứng chỉ
  [req]
  default_bits = 2048           ;Đặt độ dài của khóa RSA thành 2048-bit
  prompt = no                   ;Tắt chế độ nhập thông tin thủ công khi tạo chứng chỉ.
  default_md = sha256           ;Dùng thuật toán SHA-256 để mã hóa chứng chỉ
  distinguished_name = dn       ;Trỏ đến phần [dn]
  req_extensions = req_ext      ;Thêm các extension đặc biệt vào chứng chỉ, cụ thể là Subject Alternative Name (SAN).

  [dn]
  C = VN                        ;Country
  ST = Hanoi                    ;State
  L = Hanoi                     ;Locality
  O = MyCompany                 ;Organization
  CN = demo.local.com           ;Common Name  tên miền chính thức

  [req_ext]
  subjectAltName = @alt_names   ;Chỉ định rằng danh sách SAN (các tên miền thay thế) sẽ được lấy từ phần [alt_names].

  [alt_names]
  DNS.1 = demo.local.com
  DNS.2 = www.demo.local.com
  DNS.3 = sub.demo.local.com
  ```

- Lệnh tạo khóa và CSR cùng lúc

  ```sh
  openssl req -new -newkey rsa:2048 -nodes -keyout domain.key -out domain.csr -subj "/CN=yourdomain.com"
  ```

- Tạo chứng chỉ tự ký

  ```sh
  openssl x509 -req -days 365 -in domain.csr -signkey domain.key -out domain.crt
  ```

# Cài đặt debug cho phpStorm

## **Bước 1: Cài đặt Xdebug**

- [Kiếm file phù hợp với version php](https://xdebug.org/download/historical)
- Sau đó bỏ vào `php.ini`

  ```ini
  [xdebug]
  zend_extension="đường dẫn tuyệt đối/ext/php_xdebug-3.1.6-7.3-vc15-nts-x86_64.dll"
  xdebug.remote_enable=1
  xdebug.remote_autostart=1
  xdebug.profiler_enable = 1
  xdebug.profiler_enable_trigger = 1
  xdebug.mode=debug,profile ;gỡ lỗi từng bước và thông tin hiệu suất
  xdebug.start_with_request=yes
  xdebug.client_host=127.0.0.1
  xdebug.client_port=9003
  ```

## **Bước 2: Cấu hình PHP Interpreter & Debugger trong PhpStorm:**

- Mở **PhpStorm**, vào menu: `File → Settings → PHP`
- Tab **CLI Interpreter**: Chọn đúng Interpreter _(đúng phiên bản PHP đang sử dụng)_
- Chuyển sang tab **Debugger**: Chọn **Xdebug**, mặc định port `9003`.
  (Nếu đang dùng Xdebug phiên bản 2, đổi sang Port `9000`.)

## **Bước 3: Cấu hình "PHP Servers" trong PhpStorm:**

- Mở menu **File → Settings → PHP → Servers**
- Click **"+"** và thêm mới server:
  - Name: đặt tên dễ nhớ (vd: php_version).
  - Host: Tên domain/dự án (vd: `myapp.test`, hoặc `localhost`).
  - Port: 80 hoặc 443 (tùy vào cấu hình HTTP/HTTPS).
  - Debugger: chọn **Xdebug**.
  - Check mục: **Use path mappings**
  - Bên trái chọn đường dẫn đến thư mục chứa code gốc (dự án PhpStorm của). Bên phải là thư mục root trên server thì set thành `/` (root của server/web).

## **Bước 4: Chạy debugger từ PhpStorm:**

- Đặt Breakpoint (click vào lề trái số dòng)
- Trên thanh công cụ, Click button **“Start Listening for PHP Debug connections”** (biểu tượng điện thoại màu xanh lá trên toolbar ở góc phải trên cùng, hoặc tổ hợp phím `Shift + F9`).

## **Phím tắt debug hữu ích trong PhpStorm:**

    | Shortcut | Mô tả |
    | --- | --- |
    | `Shift + F9` | Bật/tắt tạo "Listening for debug session" |
    | `F9 (Resume)` | Resume chạy tiếp đến breakpoint kế tiếp |
    | `F8 (Step Over)` | Chạy qua dòng code, không vào function call |
    | `F7 (Step Into)` | Chạy từng bước chi tiết vào function |
    | `Shift + F8 (Step Out)` | Trở lại hàm gọi trước đó |
    | `Alt + F9` | Tạo mới Run/Debug config |
