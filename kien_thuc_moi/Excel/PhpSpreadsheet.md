# Cài đặt thư viện phpExcel

```sh
composer require phpoffice/phpspreadsheet
```

# Ví dụ code

```php
public function export(array $data, $output_type = "F"){
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Style\Border;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Style\Fill;
    $filePath = public_path('storage/excel/formexport.xlsx');

    $form = IOFactory::load($filePath);
    $sheet = $form->setActiveSheetIndex(0);
    $sheet->setTitle("User"); // lấy sheet

//        $form->getProperties(); // set thông tin cho file
    $i = 4;

    if (!empty($data))
    {
        foreach ($data as $key => $item) {
            $form->getActiveSheet()->setCellValue('A' . $i, $item->id ?? '');
            $form->getActiveSheet()->setCellValue('B' . $i, $item->full_name ?? '');
            $form->getActiveSheet()->setCellValue('C' . $i, $item->email ?? '');
            if ($key < count($data))
                $i++;
        }
    }
    // kẻ border
    $sheet->getStyle("A4:C" . $i-1)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    // set Size
    foreach (range('A', 'C') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // màu nền: sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
    // màu chữ : $sheet->getStyle('A1')->getFont()->getColor()->setARGB('FFFF0000');



    // option save
    $filename = "export_" . now()->format('Y_m_d_H_i_s') . ".xlsx";
    $objWriter = new Xlsx($form);
    if (isset($output_type) && $output_type == 'F') {
        $objWriter->save($filename);
    } else {
        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
    }

}
```

# import

### tạo file chunk

```php
<?php
namespace App\Service;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ChunkReadFilter implements IReadFilter
{
    private $startRow;
    private $chunkSize;

    public function __construct() {
    }

    public function setRow($startRow = 1 , $chunkSize = 1000) {
        $this->startRow = $startRow;
        $this->chunkSize = $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '') {
        return ($row >= $this->startRow && $row < ($this->startRow + $this->chunkSize));
    }

}
```

### Tạo job

```php
<?php
namespace App\Jobs;

use App\Models\Users;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use App\Service\ChunkReadFilter;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ImportExcelChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $startRow;
    protected $chunkSize;
    public function __construct($filePath, $startRow, $chunkSize)
    {
        $this->filePath = $filePath;
        $this->startRow = $startRow;
        $this->chunkSize = $chunkSize;
    }

    public function handle()
    {
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);

        $filter = new ChunkReadFilter();
        $filter->setRow($this->startRow, $this->chunkSize);
        $reader->setReadFilter($filter);

        $spreadsheet = $reader->load($this->filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        foreach ($worksheet->getRowIterator($this->startRow, $this->startRow + $this->chunkSize - 1) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }

            if (!empty($data)) {
                Users::query()->create( [
                    'full_name' => $data[1],
                    'email' => $data[2],
                    "password" => "123"
                ]);
            }
        }
        Log::info("Đã import từ dòng {$this->startRow} đến " . ($this->startRow + $this->chunkSize - 1));
    }
}
```

### function import

```php
function import () {
    $filePath = storage_path("app/public/user.xlsx");

    $reader = new Xlsx();
    $reader->setReadDataOnly(true);
    $sheetInfo = $reader->listWorksheetInfo($filePath); // lấy file nhanh
    $totalRows = $sheetInfo[0]['totalRows']; // Lấy số dòng từ metadata
    $chunkSize = 5000; // Số dòng đọc mỗi lần


     for($start = 1; $start <= $totalRows; $start += $chunkSize){
        dispatch(new ImportExcelChunkJob($filePath, $start, $chunkSize));
     }

    return response()->json(['success' => 'Import đang chạy nền!']);
}
```
