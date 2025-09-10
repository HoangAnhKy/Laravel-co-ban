# Một số  hàm cơ bản

```py
# kiểm tra version
print(pytesseract.get_tesseract_version()) # 5.3.4

# liệt kê languages đã cài
print(pytesseract.get_languages(config='')) # ['eng', 'jpn', 'osd', 'vie']
```

# Lấy text cơ bản `image_to_string`

```py
from PIL import Image
text = pytesseract.image_to_string(Image.open('sample.png'), lang='eng+vie')
print(text) # nội dung ảnh
```

## chú thích 

- syntax `image_to_string`
  
  ```py
    pytesseract.image_to_string(image, lang=None, config='', nice=0, output_type=Output.STRING, timeout=0)
    ```
- giải nghĩa 
    - `image`: có thể là ảnh hay là dữ liệu đọc từ open cv
    - `lang`: Ngôn ngữ OCR.
    - `config`:
    - `output_type`: kiểu dữ liệu trả về
        - `Output.STRING` → chuỗi text.
        - `Output.BYTES` → kết quả raw bytes.
        - `Output.DICT` → dictionary (mỗi block/line/word).
        - `Output.DATAFRAME` → pandas DataFrame (mỗi từ kèm toạ độ, độ tin 
    - `timeout` thời gian chạy tính bằng giây

## option của config

- `--psm #num#` : Xác định cách Tesseract phân tích bố cục trang:
    - 0 : Chỉ dùng OSD (Orientation & Script Detection).
    - 1 : Auto page segmentation + OSD. (tự động tìm bố cục văn bản trong ảnh (chia thành dòng, đoạn, cột) mà không cần chỉ định.)
    - 3 : Auto page segmentation, không OSD (phổ biến mặc định).
    - 4 : Xem ảnh như 1 khối văn bản duy nhất (single column).
    - 5 : Xem ảnh là 1 block text duy nhất, chỉ 1 cột
    - 6 : Xem ảnh như 1 block đơn dòng (single uniform block).
    - 7 : OCR 1 dòng duy nhất.
    - 8 : OCR 1 word.
    - 9 : OCR 1 word có số trong đó.
    - 11 : OCR 1 dòng số.