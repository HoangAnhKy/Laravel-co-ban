## 1. `bind()`

- **Cú pháp**:
  ```php
  $this->app->bind(SomeClass::class, function ($app) {
      return new SomeClass(...);
  });
  ```
- **Đặc điểm**:  
  Mỗi lần resolve (lấy service) sẽ tạo **một instance mới** (tương tự “prototype pattern”).
- **Khi dùng**:  
  Khi muốn mỗi lần resolve là một đối tượng “mới hoàn toàn”.

---

## 2. `singleton()`

- **Cú pháp**:
  ```php
  $this->app->singleton(SomeClass::class, function ($app) {
      return new SomeClass(...);
  });
  ```
- **Đặc điểm**:  
  Chỉ tạo **một instance duy nhất** trong vòng đời của ứng dụng (tương tự “singleton pattern”).
- **Khi dùng**:  
  Khi muốn duy trì **một phiên bản** service dùng chung trong toàn ứng dụng.

---

## 3. `instance()`

- **Cú pháp**:
  ```php
  $this->app->instance(SomeClass::class, $object);
  ```
- **Ví dụ sử dụng**

  ```php
  public function register()
  {
      $customLogger = new CustomLogger('MY-APP');

      $this->app->instance(CustomLogger::class, $customLogger);
  }
  ```

- **Đặc điểm**:  
  Đăng ký **một đối tượng đã được khởi tạo sẵn** vào container. Sau đó, mọi lần resolve `SomeClass::class` sẽ trả về **$object** này.
- **Khi dùng**:  
  Khi có sẵn một object (có thể được tạo trước, hoặc từ một package nào đó) và muốn “đưa” nó vào container để dùng chung.

---

## 4. `extend()`

- **Cú pháp**:
  ```php
  $this->app->extend(SomeClass::class, function ($service, $app) {
      // $service là instance đã resolve.
      // có thể “chèn” thêm logic hoặc
      // thay đổi trước khi return.
      return $service;
  });
  ```
- **Đặc điểm**:  
  Cho phép “mở rộng” hoặc “trang trí” (decorate) một service **sau khi** nó đã được resolve.
- **Khi dùng**:  
  Khi cần can thiệp vào quá trình khởi tạo, thêm logic, hoặc gán thêm thông tin cho service.

---

## 5. Contextual Binding (Ràng buộc theo ngữ cảnh)

- **Cú pháp**:

  ```php
  use App\Services\PaymentServiceInterface;
  use App\Services\PaypalPaymentService;
  use App\Services\StripePaymentService;

  $this->app->when(\App\Http\Controllers\PaypalController::class)
            ->needs(PaymentServiceInterface::class)
            ->give(PaypalPaymentService::class);

  $this->app->when(\App\Http\Controllers\StripeController::class)
            ->needs(PaymentServiceInterface::class)
            ->give(StripePaymentService::class);
  ```

- **Đặc điểm**:  
  Cho phép bind **cùng một interface** thành **nhiều implementation khác nhau**, tùy thuộc vào **ngữ cảnh** (class nào đang inject).
- **Khi dùng**:  
  Khi muốn dùng các triển khai khác nhau của cùng một interface ở **những nơi cụ thể**.

---

## 6. Tagging Services

- **Cú pháp**:

  ```php
  // Gán tag cho nhiều class
  $this->app->bind(FirstService::class, fn() => new FirstService());
  $this->app->bind(SecondService::class, fn() => new SecondService());

  $this->app->tag([FirstService::class, SecondService::class], 'my.services');

  // Resolve tất cả services thuộc tag 'my.services'
  $services = $this->app->tagged('my.services');
  ```

- **Đặc điểm**:  
  Cho phép gắn “nhãn” (tag) cho nhiều service khác nhau, sau đó có thể resolve **tất cả** service thuộc tag đó (dưới dạng mảng).
- **Khi dùng**:  
  Khi có một nhóm service cùng loại (ví dụ: các service xử lý Payment Gateway, …) và muốn xử lý tập trung.

---

## 7. Tóm tắt nhanh

1. **`bind()`**: Tạo mới mỗi lần resolve.
2. **`singleton()`**: Tạo duy nhất 1 instance cho cả ứng dụng.
3. **`instance()`**: Đăng ký một đối tượng có sẵn vào container.
4. **`extend()`**: Mở rộng hoặc “trang trí” service sau khi khởi tạo.
5. **Contextual Binding**: Bind 1 interface thành nhiều implementation khác nhau tùy theo ngữ cảnh.
6. **Tagging**: Gom nhóm nhiều service lại và resolve tất cả theo “tag”.

## 8. Lời khuyên chung

- `bind()` và `singleton()`: Hãy xem service có cần tạo nhiều instance hay không.
- `interface + bind`: Thường ta bind interface => class cụ thể để dễ test và thay đổi implement.

- `Contextual Binding`: Rất hữu ích nếu có nhiều biến thể (ví dụ Payment Gateway).
- `Tagging`: Dễ quản lý nhóm service để xử lý hàng loạt (vd: chạy queue, quét virus, …).
