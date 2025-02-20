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
