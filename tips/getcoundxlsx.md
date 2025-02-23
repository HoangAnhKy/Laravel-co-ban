```php
function getExcelRowCount($filePath)
{
    if (!file_exists($filePath)) {
        return response()->json(['error' => 'File không tồn tại'], 404);
    }

    $zip = new ZipArchive();
    if ($zip->open($filePath) === TRUE) {
        // Mở sheet đầu tiên (Excel lưu data trong `sheet1.xml`)
        $index = $zip->locateName('xl/worksheets/sheet1.xml');

        if ($index !== false) {
            $xmlData = $zip->getFromIndex($index);
            $zip->close();

            // Đếm số dòng bằng cách đếm thẻ `<row>`
            $totalRows = substr_count($xmlData, '<row ');

            return $totalRows;
        }

        $zip->close();
    }

    return response()->json(['error' => 'Không thể đọc file Excel'], 500);
}
```
