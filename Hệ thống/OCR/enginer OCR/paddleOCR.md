# Cách cài

```sh
pip install paddlepaddle paddleocr
```
# các cờ đã biết 

```py
use_angle_cls=True                              # xoay ảnh cho đúng
lang='japan'                                    # Tiếng nhật

use_doc_orientation_classify=False,             # kiểm tra xem có nghiên không, nếu có xoay lại cho đúng. Dùng flase nếu chắc chắn đúng
use_doc_unwarping=False,                        # làm phẳng tài liệu bị cong
use_textline_orientation=False,                 # phát hiện hướng của từng dòng chữ
```

# demo cơ bản

```py
from paddleocr import PaddleOCR

ocr = PaddleOCR(use_angle_cls=True, lang="vi")  # “vi” = tiếng Việt
result = ocr.ocr("./hoc-tu-vung-tieng-nhat-qua-hinh-anh.jpg")

for line in result:
    for box in line:
        print(box[1][0])  # text
```

# Cách dùng cơ bản

## 1. Khởi tạo PaddleOCR

```py
ocr = PaddleOCR(
    use_angle_cls=True,  # Sử dụng để nhận diện chữ xoay
    lang='en'            # Ngôn ngữ, có thể 'ch' (Chinese), 'en', 'japan', 'korean', 'french', 'german', 'multi'
)
```

Các option cơ bản của nó

| Nhóm                     | Tham số                      | Mô tả                                        | Kiểu dữ liệu | Ví dụ |
| ------------------------ | ---------------------------- | -------------------------------------------- | ------------ | ----- |
| **Document Processing**  | use_doc_orientation_classify | Bật/tắt phát hiện xoay tài liệu              | bool         | True  |
|                          | use_doc_unwarping            | Bật/tắt làm phẳng ảnh (unwarp)               | bool         | False |
| **Text Detection**       | text_det_thresh              | Ngưỡng confidence để xác định box chữ        | float        | 0.5   |
|                          | text_det_box_thresh          | Ngưỡng lọc box nhỏ hoặc không rõ             | float        | 0.6   |
|                          | text_det_limit_side_len      | Giới hạn kích thước ảnh khi detect           | int          | 1024  |
| **Text Recognition**     | text_rec_score_thresh        | Ngưỡng confidence khi nhận dạng chữ          | float        | 0.5   |
|                          | return_word_box              | Trả về box từng chữ riêng hay không          | bool         | True  |
| **Textline Orientation** | use_textline_orientation     | Bật/tắt nhận diện hướng chữ                  | bool         | True  |
| **Ngôn ngữ**             | lang                         | Chọn ngôn ngữ (`en`, `ch`, `japan`, `multi`) | str          | 'en'  |
| **Khác**                 | use_angle_cls                | Bật/tắt nhận diện chữ xoay                   | bool         | True  |


## 2. Đọc ảnh

```py
result = ocr.ocr("image.png")
```

| Key                           | Chứa gì       | Mô tả                                                                                    |
| ----------------------------- | ------------- | ---------------------------------------------------------------------------------------- |
| `input_path`                  | text          | Đường dẫn file ảnh hoặc scan được OCR                                                    |
| `page_index`                  | int           | Chỉ số trang (PDF/multi-page)                                                            |
| `doc_preprocessor_res`        | object        | Kết quả tiền xử lý tài liệu (unwarp, xoay)                                               |
| `dt_polys`                    | polygon list  | **Box chữ/dòng** do detection phát hiện, dạng 4 điểm `[[x1,y1],[x2,y2],[x3,y3],[x4,y4]]` |
| `text_type`                   | str           | Loại text: horizontal / vertical                                                         |
| `text_rec_score_thresh`       | float         | Ngưỡng confidence khi nhận dạng chữ                                                      |
| `return_word_box`             | bool          | True/False, có trả về box từng chữ riêng không                                           |
| `rec_texts`                   | list of str   | Text sau khi OCR (dạng danh sách dòng/chữ)                                               |
| `rec_scores`                  | list of float | Confidence tương ứng với `rec_texts`                                                     |
| `rec_polys`                   | polygon list  | **Box chữ/dòng** sau khi nhận dạng (tinh chỉnh từ `dt_polys`)                            |
| `textline_orientation_angles` | list of float | Góc xoay của mỗi dòng chữ                                                                |
| `rec_boxes`                   | polygon list  | **Box cuối cùng** của chữ/dòng, có thể dùng trực tiếp để vẽ bounding box                 |


# Demo

```py
from paddleocr import PaddleOCR
from pdf2image import convert_from_path
import numpy as np

def pdf_to_image_list(FILE):
    return convert_from_path(FILE, dpi=300)

def convert_to_image(pil_img):
    return np.array(pil_img)

def read_ocr(page, items):
    for text, poly in zip(page["rec_texts"], page["rec_polys"]):
        poly = np.array(poly)
        items.append({
            "text": text,
            "poly": poly,
            "xc": poly[:,0].mean(),
            "yc": poly[:,1].mean(),
        })

def group_by_row(items, rows):
    current = [items[0]]
    for item in items[1:]:
        if abs(item["yc"] - current[-1]["yc"]) < 12:
            current.append(item)
        else:
            rows.append(current)
            current = [item]
    rows.append(current)
    return rows

def group_by_poly(rows):
    merged_rows = []
    for row in rows:
        row.sort(key=lambda x: x["xc"])

        xs, ys = [], []
        for it in row:
            xs.extend(it["poly"][:,0])
            ys.extend(it["poly"][:,1])

        x1, y1 = int(min(xs)), int(min(ys))
        x2, y2 = int(max(xs)), int(max(ys))

        merged_rows.append({
            "text": " ".join(it["text"] for it in row),
            "bbox": [x1, y1, x2, y2]
        })
    return merged_rows

def handle_OCR(FILE = "./システム用CSV ワールド御見積書(3)_estimate.pdf"):
    ocr = PaddleOCR(
        lang='japan',
        use_doc_orientation_classify=False,
        use_doc_unwarping=False,             
        use_textline_orientation=False,
    )

    # chuyển pdf sang ảnh trước
    listImage = pdf_to_image_list(FILE)
    rows = []
    # đọc từng tấm rồi convert qua img sao cho ocr đọc được
    for img in listImage:
        result = ocr.predict(convert_to_image(img))
        for line in result:
            items = []
            read_ocr(line, items)
            if not items:continue
            # sắp xếp theo tọa độ y rồi x
            items.sort(key=lambda x: (x["yc"], x["xc"]))
            # sắp xếp theo hàng
            group_by_row(items, rows)
            # xử lý theo tọa độ
            print(group_by_poly(rows))
        
handle_OCR()
```