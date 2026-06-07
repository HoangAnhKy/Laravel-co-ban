# Khái niệm


là package PHP/Laravel khá mới, dùng để trích xuất nội dung từ:

- PDF
- Word (.docx)
- Excel (.xlsx)
- PowerPoint (.pptx)
- Hình ảnh (OCR)

Nó yêu cầu PHP 8.4, và Parsel bao gồm một lệnh để cài đặt tệp litnhị phân.

# Cài đặt

```bash
composer require shipfastlabs/parsel
```

cài thêm binary đính kèm

```sh
// npm install @llamaindex/liteparse cài ở project
// thêm PARSEL_LIT_BINARY=/home/kyha/Downloads/coursePHP/node_modules/.bin/lit trong .env
vendor/bin/parsel-install-lit
```

Đối với tài liệu và hình ảnh Office, có thể tải thêm các phụ thuộc hệ thống bổ sung:

```sh
vendor/bin/parsel-install-lit --with-system-dependencies
``` 

# Sử dụng

ví dụ

```php
use Shipfastlabs\Parsel;
 
$text = Parsel::file('contract.pdf')->text();
 
$document = Parsel::file('amendment.docx')->parse();
```

## Lấy text

```php
$text = Parsel::file('invoice.pdf')->text();
```
Trả về string. Dùng cho:

- Search
- AI embedding
- Lưu database

## Lấy Parse

```php
$document = Parsel::file('invoice.pdf')->parse();
```

lấy được

```php
$document->text;
$document->metadata;
$document->pages;
$document->pageCount();
```

## OCR file scan

```php
$text = Parsel::file('scan.jpg')->withOcr(language: 'eng')->text();
```

full option

```php
$text = Parsel::file('scan.png')->withOcr(
    language: 'eng',
    tessdataPath: '/usr/share/tessdata',
    serverUrl: 'http://localhost:8828/ocr',
    workers: 8,)
->text();
```

## số  trang

```php
$text = Parsel::file('book.pdf')->page(1)->text(); // đọc 1 trang
$doc = Parsel::file('book.pdf')->pageRange(1, 5)->parse(); // đọc nhiều trang
$text = Parsel::file('book.pdf')->maxPages(50)->text(); // giớ hạn trang
```

với PDF lớn thì nên dùng lazy để đỡ ăn ram
```php
foreach (
    Parsel::file('big.pdf')->lazyPages()
    as $page
) {
    echo $page->text;
}
```

## file có pass

```php
$doc = Parsel::file('secret.pdf')->withPassword('123456')->parse();
```

## Lấy tọa độ text

```php
$document = Parsel::file('contract.pdf')->parse();

foreach ($document->pages as $page) {
    foreach ($page->items as $item) {
        echo $item->text;
        echo $item->x;
        echo $item->y;
        echo $item->width;
        echo $item->height;
    }
}
```