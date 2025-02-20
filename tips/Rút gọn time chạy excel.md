# Nếu chỉ cần lấy giá trị

### dùng DB::table('users') thay vì eloquent

```php
DB::table('users')->get()->toArray();
```

### Sử dụng select

```php
$allData = User::all()->select($select)->toArray();
```

### chunk hoặc lazy

```php
$allData = [];

// Chạy theo chunk nên thoải mái với data lớn
User::query()->chunkById(1000, function ($users) use (&$allData) {
    $chunkArray = $users->toArray(); // Chuyển cả chunk thành mảng
    $allData = array_merge($allData, $chunkArray);
});

// Dù có lazyById 1000 nhưng nó vẫn Tốt hơn vì chỉ giữ 1 bản ghi tại một thời điểm nên muốn tiết kiệm bộ nhớ và load từng record tuần tự mới dùng
User::query()
    ->lazyById(1000)
    ->each(function ($user) use (&$allData) {
        $allData[] = $user->toArray();
    });
```

### Tạo file CSV với 850K record bằng job PhpSpreadsheet

- Tạo đường dẫn vào trang download

  ```php
  // route

  Route::get("/user", function(){
      DB::table("users")->chunkById(1000, function($users){
          ExportExcelMiniSize::dispatch("users.csv", $users);
      });

      return response()->json(["message" => "Đang export"]);
  });
  ```

- Tạo job xử lý với PhpSpreadsheet

  ```php
  <?php

  namespace App\Jobs;

  use Illuminate\Contracts\Queue\ShouldQueue;
  use Illuminate\Foundation\Queue\Queueable;
  use Illuminate\Queue\InteractsWithQueue;
  use Illuminate\Support\Facades\Log;

  class ExportExcelMiniSize implements ShouldQueue
  {
      use Queueable, InteractsWithQueue;

      // protected $timeout = 6000;

      private $name_file;
      private $data;
      /**
      * Create a new job instance.
      */
      public function __construct($name, $data)
      {
          $this->name_file = $name;
          $this->data = $data;
      }

      /**
      * Execute the job.
      */
      public function handle(): void
      {
          $export = "expors";

          if (!is_dir($export)) {
              mkdir($export, 0777, true);
          }
          $file = fopen($export . "/" . $this->name_file, "w");

          if(!file_exists($file)){
              $f = fopen($file, 'w');
              // Ghi header
              fputcsv($f, ['ID', 'Full Name', 'Email']);
              fclose($f);
          }

          $f = fopen($file, 'a');
          foreach ($this->data as $record) {
              fputcsv($f, [
                  $record->id,
                  $record->full_name,
                  $record->email
              ]);
          }
          fclose($f);
      }
  }
  ```

- Với các mode của `fopen`

  | Mode | Mô tả                                                                                                      |
  | :--: | :--------------------------------------------------------------------------------------------------------- |
  | `r`  | "Chỉ đọc. Con trỏ tệp ở đầu file. Nếu file không tồn tại, trả về false."                                   |
  | `w`  | "Chỉ ghi. Xóa sạch nội dung file nếu tồn tại. Nếu file không tồn tại thì tạo mới. Con trỏ tệp ở đầu file." |
  | `a`  | "Chỉ ghi. Giữ nguyên nội dung cũ. Con trỏ tệp ở cuối file. Nếu file không tồn tại thì tạo mới."            |
  | `x`  | "Chỉ ghi, tạo file mới. Nếu file đã tồn tại, fopen sẽ thất bại và trả về false. Con trỏ tệp ở đầu file."   |
  | `r+` | "Đọc/Ghi. Con trỏ ở đầu file. Nếu file không tồn tại, trả về false."                                       |
  | `w+` | "Đọc/Ghi. Xóa sạch nội dung file nếu tồn tại, nếu không có thì tạo mới. Con trỏ tệp ở đầu file."           |
  | `a+` | "Đọc/Ghi. Giữ nguyên nội dung cũ, con trỏ tệp ở cuối file. Nếu file không tồn tại thì tạo mới."            |
  | `x+` | "Đọc/Ghi, tạo file mới. Thất bại nếu file đã tồn tại. Con trỏ tệp ở đầu file."                             |
