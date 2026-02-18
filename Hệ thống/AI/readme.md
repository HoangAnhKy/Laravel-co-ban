# Hướng dẫn cài đặt và demo cơ bản vể Laravel AI
## Laravel AI SDK Integration (Gemini Edition)

1. Cài đặt (Installation)
Cài đặt gói SDK chính thức của Laravel:

```sh
composer require laravel/ai
```

2. Cấu hình (Configuration)

Lấy API Key tại [Google AI Studio](https://aistudio.google.com/) và thêm vào file `.env`:

```env
AI_DRIVER=gemini
GEMINI_API_KEY=AIzaSyB-Mã_Của_Sếp_Ở_Đây
```
*Lưu ý: Sau khi sửa .env, nhớ chạy php artisan config:clear để cập nhật.*

3. Tạo Agent (Create an Agent)


```sh
php artisan make:agent ChatBot
```

4. Thiết lập Agent (app/Ai/Agents/ChatBot.php)

```php
public function driver(): string
{
    return 'gemini'; // Phải là driver(), không phải drive()
}

public function instructions(): string
{
    return 'Bạn là một trợ lý ảo thông minh, trả lời bằng tiếng Việt.';
}
```


5. Sử dụng trong Code (Usage)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Ai\Agents\ChatBot;
use Laravel\Ai\Exceptions\AiException;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-ai', function () {
    try {
        $agent = new ChatBot();
        $response = $agent->prompt('Chào bạn!');

        // ⚠️ LƯU Ý: text là thuộc tính (Property), không phải phương thức (Method)
        echo $response->text; 

        // Hoặc sử dụng phương thức content() chuẩn của SDK
        echo $response->content();
    } catch (AiException $e) {
        return 'Lỗi rồi sếp ơi: ' . $e->getMessage();
    }
});
````   