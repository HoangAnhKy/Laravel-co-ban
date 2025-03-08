# Sử dụng

Có thể dùng:

- [spout](https://opensource.box.com/spout/) nó đã không update từ v3
- [openspout](https://github.com/openspout/openspout/blob/4.x/docs/index.md) vẫn update có v4, do cộng đồng làm

Sử dụng khi cần xử lý các tệp bảng tính lớn với hiệu suất cao và không yêu cầu các tính năng nâng cao.

# Dùng cơ bản với spout

### Lệnh cài

```sh
composer require box/spout
```

### Read excel

```php
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

$reader = ReaderEntityFactory::createReaderFromFile('/path/to/file.ext');

$reader->open($filePath);

foreach ($reader->getSheetIterator() as $sheet) { // Đọc qua toàn bộ sheet
    // $sheet->getName() // lấy tên sheet
    foreach ($sheet->getRowIterator() as $row) {
        // do stuff with the row
        $cells = $row->getCells();
        ...
    }
}

$reader->close();
```

### Export excel

```php
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

// Tạo đối tượng Writer dựa trên định dạng được chọn format là xlsx hoặc csv
$writer = WriterEntityFactory::createWriter($format);
$writer->openToFile($filePath);

// Ghi dữ liệu
$headers = ['Column 1', 'Column 2', 'Column 3']; // Thay đổi theo cấu trúc bảng của bạn
$headerRow = WriterEntityFactory::createRowFromArray($headers); // ghi theo mảng
$writer->addRow($headerRow);
// Kết thúc ghi
$writer->close();

```

### ví dụ code Merge file

```php
<?php

namespace App\Jobs;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;


class MergeFile implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    public function __construct()
    {

    }

    public function handle()
    {
        $outputFileName = storage_path('merged_file.xlsx');
        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile($outputFileName);

        for($fileCount = 1; $fileCount <= 91; $fileCount++){
            $file = storage_path("test_$fileCount.xlsx");
            if (!file_exists($file)) {
                echo "Tệp '$file' không tồn tại.\n";
                continue;
            }
            // Tạo một đối tượng Reader để đọc tệp hiện tại
            $reader = ReaderEntityFactory::createXLSXReader();
            $reader->open($file);

            // Ghi dữ liệu vào tệp gộp
            foreach ($reader->getSheetIterator() as $sheet) {
                // Đọc từng hàng trong sheet
                foreach ($sheet->getRowIterator() as $row) {
                    // Ghi hàng vào tệp đầu ra
                    $writer->addRow($row);
                }
            }

            $reader->close();
        }

        $writer->close();
    }
}

```
