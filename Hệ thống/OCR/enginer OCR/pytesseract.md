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

- Ứng dụng: OCR cơ bản 
  - lấy toàn bộ văn bản trong ảnh.
  - Scan tài liệu, hóa đơn, giấy tờ.
  - Đọc text từ ảnh chụp màn hình.
  - Trích xuất văn bản để dịch tự động hoặc phân tích.

# ký tự + tọa độ `image_to_boxes`

```py
from PIL import Image
import pytesseract

boxes = pytesseract.image_to_boxes(Image.open("sample.png"), lang="eng")
print(boxes)  # "A 50 100 70 120 0\nB 72 100 92 120 0..."
```

- Ứng dụng: 
  - Trả về vị trí từng ký tự trong ảnh (tọa độ bounding box).
  - Dùng để highlight từng ký tự.
  - Nhận diện chữ cái riêng lẻ (ví dụ captcha, viết tay từng ký tự).

# `image_to_data` → Trả về cấu trúc phân tích

- dạng `Dict`
    ```py
    from PIL import Image
    import pytesseract
    from pytesseract import Output

    data = pytesseract.image_to_data(Image.open("sample.png"), lang="eng", output_type=Output.DICT)
    print(data.keys())  # dict có các field: level, page_num, block_num, line_num, word_num, left, top, width, height, conf, text
    print(data["text"][:10])  # in thử 10 từ OCR được
    ```

- dạng `DataFrame`

    ```py
    from PIL import Image
    import pytesseract
    from pytesseract import Output

    df = pytesseract.image_to_data(Image.open("sample.png"), lang="eng", output_type=Output.DATAFRAME)
    print(df.head())  # in 5 dòng đầu
    ```
- so sánh

    | Output      | Kiểu dữ liệu                     | Dùng khi nào                                    |
    | ----------- | -------------------------------- | ----------------------------------------------- |
    | `DICT`      | `dict` Python với list song song | Khi muốn đơn giản, không phụ thuộc pandas       |
    | `DATAFRAME` | `pandas.DataFrame`               | Khi cần phân tích, lọc, thống kê, trực quan hóa |


- Ứng dụng: Trả về chi tiết từng block / đoạn / dòng / từ (kèm tọa độ, độ tin cậy).
  - Làm ứng dụng highlight văn bản.
  - Xây hệ thống tìm kiếm văn bản trong ảnh.
  - Phân tích bố cục (layout analysis).

# Các option cho các hàm trên 

- syntax full option: ví dụ với `image_to_string`
  
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
- `oem #num#` (OCR Engine Mode): xác định engine mà Tesseract dùng để nhận diện:

   | OEM | Engine Mode      | Mô tả                                                                               | Khi dùng                                                 |
   | --- | ---------------- | ----------------------------------------------------------------------------------- | -------------------------------------------------------- |
   | 0   | Legacy Engine    | Engine cũ, dựa trên rule-based (heuristic, pattern matching).                       | Hạn chế, ít dùng trừ khi OCR văn bản đặc thù.            |
   | 1   | LSTM Neural Nets | Engine mới, dùng deep learning (LSTM) cho OCR. Chính xác hơn cho text thông thường. | Khuyên dùng cho đa số trường hợp. mạnh hơn Legacy Engine |
   | 2   | Legacy + LSTM    | Chạy song song cả Legacy và LSTM, rồi chọn kết quả tốt hơn.                         | Dùng khi text phức tạp, muốn tận dụng cả 2.              |
   | 3   | Default (Auto)   | Tesseract tự chọn engine tốt nhất theo dữ liệu và model.                            | Dùng khi không chắc chọn engine nào.                     |

    Thực tế: đa số dùng --oem 1 hoặc --oem 3.

- `--psm #num#` : Xác định cách Tesseract phân tích bố cục trang:
  
| Giá trị | Ý nghĩa                                            | Trường hợp dùng                                                       |
| ------- | -------------------------------------------------- | --------------------------------------------------------------------- |
| 0       | Chỉ phát hiện orientation + script detection (OSD) | Muốn biết ảnh xoay mấy độ, ngôn ngữ gì; không cần OCR text            |
| 1       | Auto + OSD                                         | Scan tài liệu chưa chắc xoay đúng chiều hoặc nhiều script             |
| 2       | Auto (không OSD)                                   | OCR nhanh, ảnh chắc chắn đúng chiều, một trang đầy đủ                 |
| 3       | Auto, coi cả ảnh là 1 trang (mặc định)             | OCR tài liệu chuẩn, nhiều đoạn text (default thường dùng)             |
| 4       | Giả định có 1 block văn bản dọc                    | Trang báo, sách – một khối text chính, không nhiều cột                |
| 5       | Giả định có 1 block văn bản dọc, cho căn thẳng     | Văn bản in thẳng hàng, không layout phức tạp                          |
| 6       | Giả định có block văn bản đơn, không OSD           | OCR đoạn text rõ ràng, đã biết không xoay, chỉ 1 khối                 |
| 7       | Treat image as a single text line                  | OCR một dòng text (vd: captcha, ảnh có 1 dòng số)                     |
| 8       | Treat image as a single word                       | OCR một từ duy nhất (logo, nhãn mác)                                  |
| 9       | Treat image as a single word in circle             | OCR chữ khắc vòng tròn (coin, logo dạng vòng)                         |
| 10      | Treat image as a single character                  | OCR 1 ký tự duy nhất (captcha ký tự, nhận dạng số)                    |
| 11      | Sparse text (nhiều dòng, không cần thứ tự)         | Ảnh chụp nhiều text rời rạc, không cần giữ thứ tự đọc                 |
| 12      | Sparse text + OSD                                  | Giống 11 nhưng có xoay và nhận diện script                            |
| 13      | Raw line (OCR không layout analysis)               | OCR dòng text thô, không phân tích bố cục; dùng cho training/test OCR |


chú thích:
- 0,1,12: liên quan đến OSD (xoay & script).
- 2–6: OCR bố cục theo mức khối văn bản.
- 7–10,13: OCR rất hẹp (line, word, char).
- 11–12: OCR rời rạc, không quan tâm thứ tự.
  

## option Output(Output.DICT)

```python
dict_keys([
    'level',     # cấp độ (block, paragraph, line, word)
    'page_num',  # số trang (nếu ảnh nhiều trang PDF)
    'block_num', # số thứ tự block
    'par_num',   # số thứ tự paragraph trong block
    'line_num',  # số thứ tự dòng trong paragraph
    'word_num',  # số thứ tự từ trong dòng
    'left',      # tọa độ x (bên trái)
    'top',       # tọa độ y (trên cùng)
    'width',     # chiều rộng của box
    'height',    # chiều cao của box
    'conf',      # độ tin cậy (confidence)
    'text'       # chuỗi OCR nhận diện được
])
```