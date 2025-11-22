# Cách cài

```sh
pip install paddlepaddle paddleocr
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