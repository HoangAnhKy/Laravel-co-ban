# cài đặt thư viện

```sh
pip install opencv-python
```

# Các hàm chính

## Đọc / ghi ảnh – video
- `cv2.imread("img.png")` → đọc ảnh.
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
- 
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