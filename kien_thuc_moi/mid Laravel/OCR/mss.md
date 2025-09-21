# Khái niệm

dùng để capture màn hình, chụp nhanh, quay màn hình FPS cao

# Cài đặt

```sh
pip install mss
```

# Các hàm hay dùng 

khai báo 

```py
import mss

sct = mss.mss()
```

## `sct.monitors`

lấy danh sách màn hình

- `sct.monitors[0]`: toàn bộ desktop (bao gồm tất cả màn hình).
- `sct.monitors[1]`: màn hình chính.
- `sct.monitors[2]`: màn hình thứ 2 (nếu có).
  
    ```py
    for i, monitor in enumerate(sct.monitors):
        print(i, monitor)
    ```

## `sct.grab(monitor)`

Chụp ảnh màn hình theo vùng hoặc monitor.
- Trả về MSSImage (kiểu ảnh BGRA).
- Có thể convert sang numpy để dùng với OpenCV.


```py
monitor = {"top": 100, "left": 100, "width": 300, "height": 200}
screenshot = sct.grab(monitor)
# import numpy as np
# img = np.array(screenshot)   # BGRA
```

## `sct.shot()`
Chụp 1 tấm ảnh và lưu thẳng ra file.

Tham số:
- `mon`: chọn màn hình (default = 1).
- `output`: tên file output.
```py
sct.shot(mon=1, output="screenshot.png")
```

## `sct.save(mon, output)`

Giống `shot` nhưng chọn được vùng

```py
monitor = {"top": 50, "left": 50, "width": 400, "height": 300}
sct.save(mon=monitor, output="region.png")
```

## `sct.close()`

giải phóng tài nguyên

```py
sct.close()
```

# Demo

```py
import cv2
import numpy as np
import mss
import pytesseract

# --- Khởi tạo MSS ---
sct = mss.mss()

# In ra danh sách màn hình
print("Danh sách màn hình khả dụng:")
for i, monitor in enumerate(sct.monitors):
    print(i, monitor)

# Chọn màn hình cần OCR (ví dụ monitor[1] là màn hình chính)
monitor_id = 1
monitor = sct.monitors[monitor_id]

while True:
    # Capture màn hình được chọn
    img = np.array(sct.grab(monitor))   # BGRA
    frame = cv2.cvtColor(img, cv2.COLOR_BGRA2BGR)

    # OCR với pytesseract
    text = pytesseract.image_to_string(frame, lang="eng")
    print("\033c", end="")  # clear console
    print("📌 Text OCR:")
    print(text)

    # Hiển thị màn hình đang capture
    cv2.imshow(f"Monitor {monitor_id}", frame)

    # Nhấn ESC để thoát
    if cv2.waitKey(1) == 27:
        break

cv2.destroyAllWindows()
sct.close()
```