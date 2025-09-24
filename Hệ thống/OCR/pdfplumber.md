# Thư viện đọc pdf

# Cách cài

```sh
pip install pdfplumber
```
# Các hàm hỗ  trợ

## Document
- `pdf.pages` → danh sách các trang (Page objects).
- `pdf.metadata` → metadata (title, author, subject...).
- `pdf.open_metadata()` → metadata dạng dictionary chi tiết.
- `pdf.open_outline()` → outline/bookmark (nếu có).

## Page
- `extract_text()` → lấy chữ.
- `extract_words()` → lấy từng từ kèm vị trí (list of dict: {'text', 'x0', 'x1', 'top', 'bottom'...}).
- `extract_table()` → lấy bảng.
- `extract_tables()` → lấy tất cả bảng.
- `extract_text_lines()` → text chia theo dòng (v2.0+).
- `extract_words()` → text chia theo từ.
- `images` → lấy thông tin ảnh.
- page
  - `page.width`, `page.height` → kích thước trang.
  - `page.images` → danh sách ảnh trong trang (list of dict).
  - `page.chars`→ danh sách ký tự, kèm vị trí toạ độ.
  - `page.rects`→ danh sách hình chữ nhật (PDF elements).
  - `page.lines`→ danh sách đường kẻ.
  - `page.curves` → danh sách đường cong.

# Ví dụ

## lấy text

```py
import pdfplumber

# Mở file PDF
with pdfplumber.open("sample_info.pdf") as pdf:
    for i, page in enumerate(pdf.pages):
        text = page.extract_text()
        print(f"--- Page {i+1} ---")
        print(text)
```

## Đọc từng dòng (list)

```py
with pdfplumber.open("sample_info.pdf") as pdf:
    page = pdf.pages[0]   # lấy trang đầu tiên
    lines = page.extract_text().split("\n")
    for line in lines:
        print(line)
```

## Đọc bảng (table) trong PDF

```py
with pdfplumber.open("table.pdf") as pdf:
    page = pdf.pages[0]
    table = page.extract_table()
    for row in table:
        print(row)
```

## lấy ảnh

```py
with pdfplumber.open("sample_info.pdf") as pdf:
    page = pdf.pages[0]
    for image in page.images:
        print(image)   # thông tin vị trí, size ảnh
```