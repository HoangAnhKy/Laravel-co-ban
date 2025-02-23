# Cài đặt

```sh
composer require maatwebsite/excel
```

# Export data queue

- khởi động queue

  ```sh
  php artisan queue:work
  # php artisan queue:work redis
  ```

- tạo file export

  ```sh
  php artisan make:export UsersExport --model=Users
  ```

- Cấu hình `UsersExport`

  ```php
  <?php

  namespace App\Exports;

  use App\Models\Users;
  use Illuminate\Contracts\Queue\ShouldQueue;
  use Maatwebsite\Excel\Concerns\FromQuery;
  use Maatwebsite\Excel\Concerns\WithChunkReading;
  use Maatwebsite\Excel\Concerns\WithHeadings;
  use Maatwebsite\Excel\Concerns\WithMapping;

  class UsersExport implements FromQuery, WithHeadings, ShouldQueue, WithChunkReading, WithMapping
  {
      /*
      Interfaces sử dụng:
          FromQuery: Lấy dữ liệu từ database thông qua query.
          WithHeadings: Thêm tiêu đề cho các cột trong file Excel.
          ShouldQueue: Cho phép export chạy dưới dạng job trong queue, giúp tránh timeout khi export số lượng lớn dữ liệu.
          WithChunkReading: Phân chia việc đọc dữ liệu thành các chunk nhỏ (ở đây là 1000 record mỗi lần) để giảm tải bộ nhớ.
          WithMapping: Xác định cách chuyển đổi từng record thành một dòng trong file Excel.
      */

      public function query()
      {
          // Lấy user theo query tuỳ ý
          return Users::query();
      }
      // mỗi lần đọc sẽ xử lý số record trong này
      public function chunkSize(): int
      {
          return 1000;
      }

      // Trả về một mảng chứa tiêu đề của các cột
      public function headings(): array
      {
          return ['ID', 'Name', 'Email'];
      }

      // Định nghĩa cách ánh xạ dữ liệu từ model sang một mảng giá trị cho mỗi dòng trong file Excel.
      public function map($user): array
      {
          return [
              $user->id,
              $user->name,
              $user->email,

            // Date::dateTimeToExcel($invoice->created_at),
          ];
      }
  }

  ```

- Chạy export

  ```php
    public function export()
    {
        Excel::queue(new UsersExport, 'users.xlsx');
        return response()->json(['message' => 'Export job đã được dispatch']);
    }

    // sử dụng thêm chain gọi qua job khác
    function exportChain () {
        ini_set('memory_limit', '2G');
        $chunksize = 5000;

        DB::table("users")->chunkById($chunksize, function($users, $loop) use($chunksize) {

            $fileName = "users_$loop.xlsx";
            $path = "app/public/";

            $row = $chunksize *($loop - 1);
            $old_row = 0;
            Log::info("Row: $old_row");
            Excel::queue(new UsersExport($users), $fileName, "public")->chain([
                new MergeExcelFilesJob($path.$fileName, $row, $path."users.xlsx")
            ]);
        });
    }
  ```

- Mere file

  ```php
  <?php

  namespace App\Jobs;

  use Illuminate\Contracts\Queue\ShouldQueue;
  use Illuminate\Foundation\Queue\Queueable;
  use PhpOffice\PhpSpreadsheet\IOFactory;
  use PhpOffice\PhpSpreadsheet\Spreadsheet;


  class MergeExcelFilesJob implements ShouldQueue
  {
      use Queueable;

      /**
       * Create a new job instance.
       */
      private $files;
      private $output;
      private $row_start;

      public function __construct($files, $row_start = 3, $output)
      {
          $this->files = $files;
          $this->row_start = $row_start;
          $this->output = $output;
      }

      /**
       * Execute the job.
       */
      public function handle(): void
      {
          if(isset($this->files)){
              // check file
              if(!file_exists(storage_path($this->output)))
              {
                  $spreadsheet = new Spreadsheet();
                  $mergedSheet = $spreadsheet->getActiveSheet();
                  $mergedSheet->fromArray( ["ID", "Full Name", "Email"], null, "A1");
                  $rowNumber = 3;
              }else{
                  $spreadsheet = IOFactory::load(storage_path($this->output));
                  $mergedSheet = $spreadsheet->getActiveSheet();
                  $rowNumber = $this->row_start;
              }

              $file = storage_path($this->files);
              $sheet_merge = IOFactory::load($file);
              $get_sheet = $sheet_merge->getActiveSheet();
              // merge file
              foreach ($get_sheet->toArray() as $row) {
                  $mergedSheet->fromArray($row, null, "A$rowNumber");
                  $rowNumber++;
              }
              // Remove file after merging
              unlink($file);

              $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
              $writer->save(storage_path($this->output));
          }
      }
  }
  ```

## tùy chỉnh thêm

- **lưu ý**

  - sử dụng thêm `WithStyles` Thêm màu cho đẹp

    ```php
    // ví dụ
    <?php

    namespace App\Exports;

    use App\Models\Course;
    use Faker\Core\Color;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\ShouldAutoSize;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Maatwebsite\Excel\Concerns\WithStyles;
    use PhpOffice\PhpSpreadsheet\Style\Fill;
    use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

    class CoursesExport implements
        FromCollection,
        WithHeadings,
        ShouldAutoSize,
        WithStyles
    {
        /**
        * @return \Illuminate\Support\Collection
        */
        public function collection()
        {
            return Course::all();
        }

        public function headings(): array
        {
            return [
                'NO. ',
                'NameCourse',
                'Created At',
                'Updated At',
                'Del Fag',
                'Active'
            ];
        }

        public function styles(Worksheet $sheet)
        {
            //kẻ đường viền cho tất cả các ô
            $sheet->getStyle('A1:F'.$sheet->getHighestRow())->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ]
            ]);
            return [
                // set riêng chữ và đổ màu giao diện cho hàng đầu tiên
                1 => [
                    // set về chữ
                    'font' => [
                        'bold' => true,
                        'size' => '18',
                        'color' => ['argb' => 'FFFFFF']
                    ],
                    // set về giao diện
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => '008000',
                        ],
                    ],
                ]
            ];
        }

    }

    ```

  - Nếu muốn chỉnh sửa lại cho hợp lý thì ta nên sử dụng FromView. Với cách sửa dụng y như controller bình thường là truyền data rồi gọi tới view

    ```php
    namespace App\Exports;

    use App\Invoice;
    use Illuminate\Contracts\View\View;
    use Maatwebsite\Excel\Concerns\FromView;

    class InvoicesExport implements FromView
    {
        public function view(): View
        {
            return view('exports.invoices', [
                'invoices' => Invoice::all()
            ]);
        }
    }
    ```

# import data

```php
<?php

namespace App\Imports;

use App\Models\Users;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UsersImport implements ToArray, ShouldQueue, WithChunkReading
{

    public function array(array $row)
    {
        foreach ($row as $each) {
            try {
                Users::query()->firstOrCreate([
                    'email' => $each['email'],
                    'full_name' => $each['full_name'],
                    'password' => Hash::make("123"),
                ]);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }
    }
    public function chunkSize(): int
    {
        return 1000;
    }
}
```
