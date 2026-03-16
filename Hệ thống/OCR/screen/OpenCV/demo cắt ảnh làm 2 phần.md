#cắt ảnh ra làm 2 phần 

[img thực hành](./image_demo/image.png)

## cơ bản
```py
import cv2;
import os

os.makedirs('demo', exist_ok=True)
PATH = './image.png';

img = cv2.imread(PATH);

# Lấy kích thước ảnh (Chiều cao, Chiều rộng, Số kênh màu)
height, width, channels = img.shape
mid_point = width // 2

print(f'Chiều cao: {height}, Chiều rộng: {width}, Số kênh màu: {channels}');
print(f'Điểm giữa của ảnh: {mid_point}');
left_page = img[0:height, 0:mid_point]
right_page = img[0:height, mid_point:width]

# 5. Lưu kết quả
cv2.imwrite('demo/trang_218.jpg', left_page)
cv2.imwrite('demo/trang_219.jpg', right_page)
```

## Nâng cao

```py
import cv2
import numpy as np
import os

os.makedirs("demo", exist_ok=True)

img = cv2.imread("image.png")

height, width, _ = img.shape

# chuyển sang grayscale
gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

# lấy vùng trung tâm để tránh nhiễu
center_region = gray[:, width//3: width*2//3]

# tính độ sáng trung bình theo cột
col_mean = np.mean(center_region, axis=0)

# tìm cột tối nhất
split = np.argmin(col_mean)

# cộng offset vì ta cắt vùng center
split = split + width//3

print("Điểm tách:", split)

left_page = img[:, :split]
right_page = img[:, split:]

cv2.imwrite("demo/trang_trai.jpg", left_page)
cv2.imwrite("demo/trang_phai.jpg", right_page)

print("Done")
```


# demo

```py
import cv2
import numpy as np

img = cv2.imread("./demo/trang_phai.jpg")

height, width, _ = img.shape

# ## làm rõ (Sharpen)
# blur = cv2.GaussianBlur(img, (5,5), 0)
# sharp = cv2.addWeighted(img, 1.5, blur, -0.5, 0)

# ## Giảm sáng 
# darker = cv2.convertScaleAbs(img, alpha=1, beta=-50)

# ## tăng sáng 
# bright = cv2.convertScaleAbs(img, alpha=1, beta=50)

# Adaptive Threshold Chuyển ảnh thành đen trắng rõ chữ.

'''
gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

th = cv2.adaptiveThreshold(
    gray,
    255,
    cv2.ADAPTIVE_THRESH_GAUSSIAN_C,
    cv2.THRESH_BINARY,
    11,
    2
)

cv2.imwrite("binary.jpg", th)
'''

img = cv2.imread("image.png")

gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)

blur = cv2.GaussianBlur(gray,(5,5),0)

th = cv2.adaptiveThreshold(
    blur,255,
    cv2.ADAPTIVE_THRESH_GAUSSIAN_C,
    cv2.THRESH_BINARY,
    11,2
)

cv2.imshow("Image", th)
cv2.waitKey(0)
cv2.destroyAllWindows()
print("Done")
```