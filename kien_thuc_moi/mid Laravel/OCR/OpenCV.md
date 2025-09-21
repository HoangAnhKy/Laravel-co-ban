# cài đặt thư viện

```sh
pip install opencv-python
```

# Các hàm chính

## Đọc / ghi ảnh – video
- `cv2.imread("img.png")` → đọc ảnh. trả về numpy array `[254, 255](bit)`
- `cv2.imwrite("out.png", img)` → lưu ảnh.
- `cv2.imshow("window", img)` → hiển thị ảnh.
- `cv2.VideoCapture(0)` → mở webcam.
- `cv2.VideoWriter(...)` → ghi video.

## Xử lý ảnh cơ bản
- `cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)` → chuyển ảnh sang grayscale.
- `cv2.resize(img, (w, h))`→ thay đổi kích thước.
- `cv2.threshold(img, 127, 255, cv2.THRESH_BINARY)`→ nhị phân hóa ảnh.
- `cv2.GaussianBlur(img, (5,5), 0)`→ làm mờ Gaussian.
- `cv2.Canny(img, 100, 200)`→ phát hiện biên cạnh.
- `cv2.convertScaleAbs(img, alpha=1.0, beta=-50)` → cân bằng sáng
  - alpha = độ tương phản, beta = độ sáng (số âm = tối hơn, số dương = sáng hơn)
  - alpha > 1 → tăng tương phản (contrast).
  - 0 < alpha < 1 → giảm tương phản.
  - beta > 0 → làm sáng ảnh.
  - beta < 0 → làm tối ảnh.
  
## Hình học
- `cv2.line(img, (x1,y1), (x2,y2), color, thickness)` → vẽ đường.
- `cv2.rectangle(img, (x1,y1), (x2,y2), color, thickness)` → vẽ hình chữ nhật.
- `cv2.circle(img, (x,y), r, color, thickness)` → vẽ hình tròn.
- `cv2.putText(img, "Hello", (x,y), font, scale, color, thickness)` → vẽ chữ.

## Xử lý nâng cao
- `cv2.findContours(img, ...)` → tìm biên, contour.
- `cv2.HoughLines(...)` → phát hiện đường thẳng.
- `cv2.HoughCircles(...)` → phát hiện hình tròn.
- `cv2.matchTemplate(...)` → tìm mẫu con trong ảnh lớn.

## Nhận diện
- `cv2.CascadeClassifier("haarcascade_frontalface.xml")` → nhận diện khuôn mặt.
- `cv2.dnn.readNet(...)` → load model deep learning.
- `cv2.face.LBPHFaceRecognizer_create()` → nhận diện khuôn mặt.

## Xử lý video realtime
- `cap = cv2.VideoCapture(0)` → lấy frame từ webcam.
- `ret, frame = cap.read()` → đọc frame.
- `cv2.imshow("Video", frame)` → hiển thị frame.


# Demo

## đọc và show img

```py
import cv2

path = "./image.png"
img = cv2.imread(path)
cv2.imshow("windown", img)
cv2.waitKey(0)  # chờ nhấn phím bất kỳ
cv2.destroyAllWindows() # Đóng các cửa sổ sau khi bấm các nút bất kì
```

## lưu text vào ảnh

```py

import cv2
import numpy as np

# --- 1. Tạo ảnh trắng mới ---
width, height = 800, 600  # chiều rộng x chiều cao
img_new = 255 * np.ones((height, width, 3), dtype=np.uint8)  # ảnh trắng, bỏ 255 đi thì là ảnh đen

# img = cv2.imread("demo2.png")  # ảnh gốc
# img_copy = img.copy()        # bản sao để vẽ

# --- 2. Text muốn vẽ ---
text = "Hello World trên ảnh trắng"

# --- 3. Vẽ text lên ảnh ---
font = cv2.FONT_HERSHEY_SIMPLEX
font_scale = 1
color = (0, 0, 255)  # đỏ
thickness = 3   # Độ dày của text
x, y = 50, 100  # vị trí text

cv2.putText(img_new, text, (x, y), font, font_scale, color, thickness)

# --- 4. Hiển thị và lưu ---
cv2.imshow("White Image with Text", img_new)
cv2.imwrite("white_image_with_text.png", img_new)
cv2.waitKey(0)
cv2.destroyAllWindows()
```

## Sử dụng font khác các font được hỗ  trợ trong opencv, sử  dụng thêm thư viện PIL

```py
from PIL import Image, ImageDraw, ImageFont
import cv2
import numpy as np

# --- 1. Tạo ảnh trắng mới ---
width, height = 800, 600
img_pil = Image.new("RGB", (width, height), (255, 255, 255))  # ảnh trắng
draw = ImageDraw.Draw(img_pil)

# --- 2. Text muốn vẽ ---
text = "Xin chào thế giới! Đây là text tiếng Việt."

# --- 3. Load font Times New Roman ---
font_path = "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf"
 # đường dẫn đến font
font_size = 40
font = ImageFont.truetype(font_path, font_size)

# --- 4. Vẽ text lên ảnh ---
x, y = 50, 100
draw.text((x, y), text, font=font, fill=(0, 0, 255))  # chữ đỏ

# --- 5. Chuyển sang OpenCV để hiển thị hoặc lưu ---
img_cv = cv2.cvtColor(np.array(img_pil), cv2.COLOR_RGB2BGR)

cv2.imshow("Vietnamese Text Image", img_cv)
cv2.imwrite("white_image_with_vietnamese_text.png", img_cv)
cv2.waitKey(0)
cv2.destroyAllWindows()
```