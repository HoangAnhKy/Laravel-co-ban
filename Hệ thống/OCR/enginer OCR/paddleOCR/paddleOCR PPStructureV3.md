Dùng để  đọc tài liệu có bảng table

# PPStructureV3 hàm xử lý trong predict

| Tham số                                  | Mô tả                                   | Cách dùng                    | Ví dụ                                         |
| ---------------------------------------- | --------------------------------------- | ---------------------------- | --------------------------------------------- |
| `input`                                  | Ảnh hoặc đường dẫn ảnh                  | Truyền path hoặc numpy array | `"./page.png"`                                |
| `use_doc_orientation_classify`           | Tự xoay tài liệu về đúng hướng          | `True/False`                 | `use_doc_orientation_classify=True`           |
| `use_doc_unwarping`                      | Chống cong/méo trang scan               | `True`                       | `use_doc_unwarping=True`                      |
| `use_textline_orientation`               | Phát hiện hướng từng dòng chữ           | `True/False`                 | `use_textline_orientation=True`               |
| `use_seal_recognition`                   | Nhận diện con dấu đỏ                    | `True/False`                 | `use_seal_recognition=True`                   |
| `use_table_recognition`                  | Bật nhận dạng bảng                      | `True/False`                 | `use_table_recognition=True`                  |
| `use_formula_recognition`                | Nhận dạng công thức toán                | `True/False`                 | `use_formula_recognition=True`                |
| `use_chart_recognition`                  | Nhận dạng biểu đồ chart                 | `True/False`                 | `use_chart_recognition=True`                  |
| `use_region_detection`                   | Bật layout: phát hiện vùng nội dung     | `True/False`                 | `use_region_detection=True`                   |
| `layout_threshold`                       | Threshold layout                        | Số 0–1                       | `layout_threshold=0.5`                        |
| `layout_nms`                             | NMS khi detect layout                   | Float nhỏ = gộp nhiều        | `layout_nms=0.2`                              |
| `layout_unclip_ratio`                    | Mở rộng vùng layout                     | Float                        | `layout_unclip_ratio=1.5`                     |
| `layout_merge_bboxes_mode`               | Gộp box layout                          | `"horizontal"` hoặc `"all"`  | `layout_merge_bboxes_mode="all"`              |
| `text_det_limit_side_len`                | Giới hạn resize trước detect            | 960–2000                     | `text_det_limit_side_len=1600`                |
| `text_det_limit_type`                    | Kiểu giới hạn detect                    | `"max"` hoặc `"min"`         | `text_det_limit_type="max"`                   |
| `text_det_thresh`                        | Ngưỡng detect chữ                       | Float 0–1                    | `text_det_thresh=0.3`                         |
| `text_det_box_thresh`                    | Ngưỡng confidence box giữ lại           | Float 0–1                    | `text_det_box_thresh=0.6`                     |
| `text_det_unclip_ratio`                  | Mở rộng khung chữ                       | Float                        | `text_det_unclip_ratio=2.0`                   |
| `text_rec_score_thresh`                  | Threshold để giữ text đã nhận dạng      | 0–1                          | `text_rec_score_thresh=0.5`                   |
| `seal_det_limit_side_len`                | Giới hạn detect con dấu                 | Float                        | `seal_det_limit_side_len=1600`                |
| `seal_det_thresh`                        | Ngưỡng detect con dấu                   | Float                        | `seal_det_thresh=0.3`                         |
| `seal_det_box_thresh`                    | Giữ box con dấu                         | Float                        | `seal_det_box_thresh=0.5`                     |
| `seal_det_unclip_ratio`                  | Mở rộng box con dấu                     | Float                        | `seal_det_unclip_ratio=2.0`                   |
| `seal_rec_score_thresh`                  | Threshold nhận dạng con dấu             | Float                        | `seal_rec_score_thresh=0.5`                   |
| `use_wired_table_cells_trans_to_html`    | Chuyển bảng có đường kẻ sang HTML       | `True/False`                 | `use_wired_table_cells_trans_to_html=True`    |
| `use_wireless_table_cells_trans_to_html` | Chuyển bảng không có đường kẻ sang HTML | `True/False`                 | `use_wireless_table_cells_trans_to_html=True` |
| `use_table_orientation_classify`         | Xoay bảng đúng hướng                    | `True/False`                 | `use_table_orientation_classify=True`         |
| `use_ocr_results_with_table_cells`       | Lấy OCR cho từng ô trong bảng           | `True/False`                 | `use_ocr_results_with_table_cells=True`       |
| `use_e2e_wired_table_rec_model`          | Dùng model bảng end-to-end có line      | `True/False`                 | `use_e2e_wired_table_rec_model=True`          |
| `use_e2e_wireless_table_rec_model`       | Dùng model bảng end-to-end không line   | `True/False`                 | `use_e2e_wireless_table_rec_model=True`       |


# Dữ liệu trả về  sẽ là 

- Đối với dữ liệu cơ bản 

    ```json
    {
        'type': 'text',              # text | table | title | figure | header | footer | paragraph ...
        'bbox': [x1, y1, x2, y2],    # bounding box của block
        'data': {
            'text': '本書は御見積書です',     # chuỗi text đã OCR
            'polys': [...],           # polygon của từng dòng
            'rec_scores': [...],      # độ tự tin
            'line_bboxes': [...],     # bbox từng dòng
        }
    }
    ```

- dữ liệu mà là `table`
  
  Nếu bật:

  - `use_table_recognition=True`
  - `use_ocr_results_with_table_cells=True`
  - `use_wireless_table_cells_trans_to_html=True`
  
    ```json
    {
            'type': 'table',
            'bbox': [x1, y1, x2, y2],
            'data': {
                'html': '<table>...</table>',     # HTML tái tạo bảng
                'cells': [
                    {
                        'bbox': [cx1, cy1, cx2, cy2],   # tọa độ ô
                        'text': '合計',                 # text trong ô
                        'row': 3,
                        'col': 1,
                    },
                    ...
                ],
                'rows': 12,
                'cols': 5
            }
        }
        ```

# Demo

```py
from paddleocr import PPStructureV3
import cv2
from pdf2image import convert_from_path
import numpy as np

pages = convert_from_path("./システム用CSV ワールド御見積書(3)_estimate.pdf", dpi=600)
img = cv2.cvtColor(np.array(pages[0]), cv2.COLOR_RGB2BGR)

pipeline = PPStructureV3(lang="japan")
result = pipeline.predict(
    img,
    use_doc_orientation_classify=True,              # Tự xoay tài liệu về đúng hướng
    use_textline_orientation=True,                  # Phát hiện hướng từng dòng chữ
    use_table_recognition=True,                     # Bật nhận dạng bảng    
    use_ocr_results_with_table_cells=True,          # Lấy OCR cho từng ô trong bảng
    use_wireless_table_cells_trans_to_html=True,    # Chuyển bảng không có đường kẻ sang HTML
    text_det_box_thresh=0.5,                        # Ngưỡng confidence box giữ lại
)


for block in enumerate(result):
    print(block)
    
```