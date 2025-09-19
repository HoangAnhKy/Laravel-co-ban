# Các module chính trong PIL (Pillow)
## Image

Module chính để mở, tạo, và xử lý ảnh.

Các hàm chính:

- `Image.open(path)` → mở ảnh
- `Image.new(mode, size, color)` → tạo ảnh mới
- `Image.save(path)` → lưu ảnh
- `Image.show()` → mở ảnh
- `Image.convert(mode)` → đổi mode (RGB, L, CMYK, …)
- `Image.resize((w,h))`, Image.rotate(angle), Image.crop(box)
- `Image.filter(...)` → áp dụng bộ lọc
- `Image.transpose()` → lật ảnh

## ImageDraw

Vẽ trực tiếp lên ảnh.

Ví dụ:

```py
from PIL import Image, ImageDraw
img = Image.new("RGB", (200,200), "white")
draw = ImageDraw.Draw(img)
draw.rectangle([50, 50, 150, 150], outline="red", width=3)
draw.text((60,60), "Hello", fill="blue")
img.show()
```

- hàm hay dùng

    | Tên hàm          | Cách dùng                                                                | Mô tả                                               |
    | ---------------- | ------------------------------------------------------------------------ | --------------------------------------------------- |
    | `point`          | `draw.point((x, y), fill="red")`                                         | Vẽ một hoặc nhiều pixel tại tọa độ                  |
    | `line`           | `draw.line((x1, y1, x2, y2), fill="blue", width=3)`                      | Vẽ đường thẳng hoặc nhiều đoạn nối tiếp             |
    | `rectangle`      | `draw.rectangle((x1, y1, x2, y2), outline="black", fill="yellow")`       | Vẽ hình chữ nhật (có thể có viền + màu nền)         |
    | `ellipse`        | `draw.ellipse((x1, y1, x2, y2), outline="blue", fill="lightblue")`       | Vẽ hình elip hoặc hình tròn                         |
    | `polygon`        | `draw.polygon([(x1,y1), (x2,y2), (x3,y3)], outline="red", fill="green")` | Vẽ đa giác tùy ý                                    |
    | `arc`            | `draw.arc((x1, y1, x2, y2), start=0, end=180, fill="purple", width=2)`   | Vẽ cung tròn (chỉ viền)                             |
    | `pieslice`       | `draw.pieslice((x1, y1, x2, y2), start=30, end=300, fill="pink")`        | Vẽ hình quạt (cung có nền tô màu)                   |
    | `text`           | `draw.text((10, 10), "Hello", fill="black", font=font)`                  | Vẽ 1 dòng chữ                                       |
    | `multiline_text` | `draw.multiline_text((10, 50), "Line1\nLine2", fill="blue", font=font)`  | Vẽ nhiều dòng chữ                                   |
    | `textbbox`       | `draw.textbbox((0, 0), "Hello", font=font)`                              | Trả về bounding box của chữ (để canh lề, canh giữa) |

- option của các hàm trên

    | Thuộc tính     | Dùng ở                                                                          | Ví dụ                                      | Ý nghĩa                                                                       |
    | -------------- | ------------------------------------------------------------------------------- | ------------------------------------------ | ----------------------------------------------------------------------------- |
    | `fill`         | Hầu hết các hàm (`point`, `line`, `rectangle`, `ellipse`, `polygon`, `text`, …) | `fill="red"`                               | Màu tô bên trong (hoặc màu pixel/đường)                                       |
    | `outline`      | `rectangle`, `ellipse`, `polygon`, `pieslice`                                   | `outline="black"`                          | Màu viền                                                                      |
    | `width`        | `line`, `arc`, `rectangle`                                                      | `width=3`                                  | Độ dày nét vẽ                                                                 |
    | `start`, `end` | `arc`, `pieslice`                                                               | `start=0, end=180`                         | Góc bắt đầu/kết thúc (theo độ, ngược kim đồng hồ)                             |
    | `font`         | `text`, `multiline_text`                                                        | `font=ImageFont.truetype("arial.ttf", 20)` | Font chữ                                                                      |
    | `spacing`      | `multiline_text`                                                                | `spacing=5`                                | Khoảng cách giữa các dòng                                                     |
    | `align`        | `multiline_text`                                                                | `align="center"`                           | Căn trái, phải, giữa cho text nhiều dòng                                      |
    | `anchor`       | `text`, `multiline_text`                                                        | `anchor="mm"`                              | Xác định điểm neo (ví dụ `mm` = middle-middle)                                |
    | `direction`    | `text`, `multiline_text`                                                        | `direction="rtl"`                          | Hướng chữ (ltr = trái sang phải, rtl = phải sang trái, ttb = trên xuống dưới) |
    | `features`     | `text`, `multiline_text`                                                        | `features=["-liga"]`                       | Bật/tắt OpenType features (như ligatures)                                     |

## ImageFont

Quản lý font chữ để vẽ text. có thể dùng các option ở bên `ImageDraw`

Dùng với `ImageDraw.text()`.

Ví dụ:

```py
from PIL import ImageFont
font = ImageFont.truetype("arial.ttf", 20)
```

| Hàm / Thuộc tính                 | Ví dụ                                               | Mô tả                                                 |
| -------------------------------- | --------------------------------------------------- | ----------------------------------------------------- |
| `truetype()`                     | `ImageFont.truetype("arial.ttf", 24)`               | Tải font TrueType/OTF với kích thước                  |
| `load_default()`                 | `ImageFont.load_default()`                          | Font mặc định (built-in)                              |
| `getsize(text)` *(cũ)*           | `w, h = font.getsize("Hello")`                      | Lấy kích thước chữ (deprecated → dùng `getbbox`)      |
| `getbbox(text)`                  | `bbox = font.getbbox("Hello")`                      | Trả về (x0, y0, x1, y1) của chữ                       |
| `getmask(text)`                  | `mask = font.getmask("Hello")`                      | Trả về bitmap mask của text (dùng cho xử lý nâng cao) |
| `getmetrics()`                   | `ascent, descent = font.getmetrics()`               | Thông tin cao/thấp của font                           |
| `getlength(text)` (Pillow ≥8.0)  | `length = font.getlength("Hello")`                  | Chiều dài text chính xác (hữu ích cho canh giữa)      |
| `getsize_multiline(text)` *(cũ)* | `font.getsize_multiline("Line1\nLine2")`            | Kích thước nhiều dòng (deprecated)                    |
| `getbbox_multiline(text)`        | `font.getbbox_multiline("Line1\nLine2", spacing=4)` | Bounding box cho nhiều dòng                           |
| `getmask2(text)` *(mới)*         | `mask, offset = font.getmask2("Hello")`             | Mask nâng cao, trả thêm offset                        |

## ImageFilter

Các bộ lọc xử lý ảnh:

BLUR, CONTOUR, DETAIL, EDGE_ENHANCE, SHARPEN, …

Ví dụ:

```py
from PIL import ImageFilter
img = img.filter(ImageFilter.BLUR)
```

| Filter                 | Cách dùng                                                                 | Mô tả                                       |
| ---------------------- | ------------------------------------------------------------------------- | ------------------------------------------- |
| `BLUR`                 | `img.filter(ImageFilter.BLUR)`                                            | Làm mờ ảnh (blur cơ bản)                    |
| `CONTOUR`              | `img.filter(ImageFilter.CONTOUR)`                                         | Vẽ đường viền (contour)                     |
| `DETAIL`               | `img.filter(ImageFilter.DETAIL)`                                          | Tăng chi tiết                               |
| `EDGE_ENHANCE`         | `img.filter(ImageFilter.EDGE_ENHANCE)`                                    | Làm nổi bật cạnh                            |
| `EDGE_ENHANCE_MORE`    | `img.filter(ImageFilter.EDGE_ENHANCE_MORE)`                               | Nổi bật cạnh mạnh hơn                       |
| `EMBOSS`               | `img.filter(ImageFilter.EMBOSS)`                                          | Hiệu ứng dập nổi                            |
| `FIND_EDGES`           | `img.filter(ImageFilter.FIND_EDGES)`                                      | Phát hiện cạnh                              |
| `SHARPEN`              | `img.filter(ImageFilter.SHARPEN)`                                         | Làm nét ảnh                                 |
| `SMOOTH`               | `img.filter(ImageFilter.SMOOTH)`                                          | Làm mịn ảnh                                 |
| `SMOOTH_MORE`          | `img.filter(ImageFilter.SMOOTH_MORE)`                                     | Làm mịn mạnh hơn                            |
| `GaussianBlur(radius)` | `img.filter(ImageFilter.GaussianBlur(5))`                                 | Làm mờ Gaussian với bán kính                |
| `UnsharpMask()`        | `img.filter(ImageFilter.UnsharpMask(radius=2, percent=150, threshold=3))` | Làm nét bằng unsharp mask                   |
| `Kernel()`             | `img.filter(ImageFilter.Kernel((3,3), kernel_values, scale))`             | Tạo filter tùy chỉnh bằng ma trận tích chập |


## ImageEnhance

- Tăng giảm chất lượng ảnh:
    - Độ sáng (Brightness)
    - Độ tương phản (Contrast)
    - Màu sắc (Color)
    - Độ sắc nét (Sharpness)
Ví dụ:

```py
from PIL import ImageEnhance
enhancer = ImageEnhance.Contrast(img)
img = enhancer.enhance(2.0)  # tăng gấp đôi contrast
```

| Enhancer     | Cách dùng                                      | Ý nghĩa                       |
| ------------ | ---------------------------------------------- | ----------------------------- |
| `Brightness` | `ImageEnhance.Brightness(img).enhance(factor)` | Điều chỉnh **độ sáng**        |
| `Contrast`   | `ImageEnhance.Contrast(img).enhance(factor)`   | Điều chỉnh **độ tương phản**  |
| `Color`      | `ImageEnhance.Color(img).enhance(factor)`      | Điều chỉnh **độ bão hòa màu** |
| `Sharpness`  | `ImageEnhance.Sharpness(img).enhance(factor)`  | Điều chỉnh **độ nét**         |

- Tham số factor:

    - `factor = 1.0` → ảnh gốc (không thay đổi)
    - `factor < 1.0` → giảm hiệu ứng (ví dụ Brightness = tối đi, Sharpness = mờ hơn)
    - `factor = 0.0` → ảnh “cực đoan” (ví dụ Brightness = đen hoàn toàn, Color = ảnh trắng đen, Contrast = xám đồng đều)
    - `factor > 1.0` → tăng hiệu ứng (sáng hơn, nét hơn, rực màu hơn…)

## ImageChops

Các phép toán ảnh (Channel Operations):

add, subtract, multiply, invert, difference, …

Ví dụ:

```py
from PIL import ImageChops
diff = ImageChops.difference(img1, img2)
```

## ImageOps

Các thao tác cao cấp:

grayscale, invert, equalize, mirror, posterize, solarize, …

Ví dụ:

```py
from PIL import ImageOps
img = ImageOps.invert(img)
```

## ImageSequence

Làm việc với ảnh nhiều frame (GIF, TIFF).

Ví dụ:

```py
from PIL import ImageSequence
for frame in ImageSequence.Iterator(img):
frame.show()
```

## ImageStat

Tính toán thống kê trên ảnh (mean, stddev, sum).

Ví dụ:

```py
from PIL import ImageStat
stat = ImageStat.Stat(img)
print(stat.mean)  # trung bình màu
```

# Tóm gọn lại

from `PIL` có thể import các module chính sau:
- `Image` → mở, tạo, lưu, xử lý ảnh
- `ImageDraw` → vẽ hình, chữ lên ảnh
- `ImageFont` → quản lý font
- `ImageFilter` → bộ lọc
- `ImageEnhance` → chỉnh sáng, màu, tương phản
- `ImageChops` → phép toán ảnh (add, subtract, invert…)
- `ImageOps` → thao tác tiện ích (grayscale, invert, mirror…)
- `ImageSequence` → duyệt frame GIF/TIFF
- `ImageStat` → thống kê ảnh

# Demo 

```py
from PIL import Image, ImageDraw, ImageFont

# Tạo ảnh trắng
img = Image.new("RGB", (400, 200), color="white")
draw = ImageDraw.Draw(img)

# Chọn font (Arial hoặc DejaVuSans có sẵn trên Linux)
font = ImageFont.truetype("DejaVuSans.ttf", 40)  # 40 = size chữ

text = "Hello, OCR!"
# Lấy kích thước chữ
bbox = draw.textbbox((0, 0), text, font=font)
text_width = bbox[2] - bbox[0]
text_height = bbox[3] - bbox[1]

# Tính tọa độ để căn giữa
x = (img.width - text_width) // 2
y = (img.height - text_height) // 2

# Vẽ chữ
draw.text((x, y), text, fill="black", font=font)

img.show()
```