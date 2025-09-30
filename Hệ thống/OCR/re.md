# Python `re` Cheat Sheet

## Import
    import re

## Các hàm cơ bản

### 1. re.match(pattern, string)
- Tìm pattern ở **đầu chuỗi**.
    m = re.match(r"Hello", "Hello World")
    print(m.group())  # Hello

### 2. re.search(pattern, string)
- Tìm pattern ở **bất kỳ đâu** trong chuỗi.
    m = re.search(r"World", "Hello World")
    print(m.group())  # World

### 3. re.findall(pattern, string)
- Trả về list tất cả các chuỗi khớp.
    nums = re.findall(r"\d+", "a1 b22 c333")
    print(nums)  # ['1', '22', '333']

### 4. re.finditer(pattern, string)
- Trả về iterator các match object (lấy vị trí).
    for m in re.finditer(r"\d+", "a1 b22 c333"):
        print(m.group(), m.start(), m.end())
    # 1 1 2
    # 22 4 6
    # 333 8 11

### 5. re.sub(pattern, repl, string)
- Thay thế chuỗi khớp pattern.
    text = "abc 123 def 456"
    new = re.sub(r"\d+", "X", text)
    print(new)  # abc X def X

### 6. re.split(pattern, string)
- Cắt chuỗi theo regex.
    parts = re.split(r"\s+", "one   two   three")
    print(parts)  # ['one', 'two', 'three']

## Flags hay dùng
- re.IGNORECASE (re.I) → không phân biệt hoa/thường
- re.MULTILINE  (re.M) → ^ và $ áp dụng cho từng dòng
- re.DOTALL     (re.S) → . match luôn cả newline

    text = "Hello\nWorld"
    m = re.search(r"hello", text, re.I)
    print(m.group())  # Hello

## Regex patterns thường gặp
- Email:
    re.findall(r"[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}", text)

- Số điện thoại (VN, cơ bản):
    re.findall(r"0\d{9,10}", text)

- Ngày (dd/mm/yyyy hoặc dd-mm-yyyy):
    re.findall(r"\d{2}[/-]\d{2}[/-]\d{4}", text)

- Chỉ lấy số:
    re.findall(r"\d+", text)
