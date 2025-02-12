Tính năng phản hồi trực tuyến của Laravel cho phép xử lý hiệu quả các tập dữ liệu lớn bằng cách gửi dữ liệu dần dần khi dữ liệu được tạo, giúp giảm mức sử dụng bộ nhớ và cải thiện thời gian phản hồi.

```php
<?php

Route::get('/stream', function () {
    return response()->stream(function () {
        for ($number = 1; $number <= 10**7; $number++) {
            echo "Line {$number}\n";
            // Xả bộ đệm để gửi nội dung ngay lập tức
            ob_flush();
            flush();
        }
    }, 200, ['Content-Type' => 'text/plain']);
});


// lâu hơn stream và die giữa chừng
Route::get('/no-stream', function () {
    $content = '';
    for ($i = 1; $i <= 10**8; $i++) {
        $content .= "Line {$i}\n";
    }
    return response($content, 200, ['Content-Type' => 'text/plain']);
});

?>
```
