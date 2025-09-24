# Code ORC python

## Download python (lúc demo bản mới nhất là 3.12)

```sh
sudo apt update
sudo apt install python3 python3-pip -y
```

## Cài Tesseract OCR (máy server)

```sh
sudo apt update
sudo apt install -y tesseract-ocr libtesseract-dev
# cài tiếng nhật với việt 
sudo apt install -y tesseract-ocr-vie tesseract-ocr-jpn
```

kiểm tra nó ở đâu

```sh
whereis tesseract
#tesseract: /usr/bin/tesseract /usr/include/tesseract /usr/share/man/man1/tesseract.1.gz
```

## Cài đặt và khởi tạo `venv`
mục đích: công cụ tạo môi trường ảo

```sh
python3.12 -m venv venv
```

Kết quả sau khi chạy

Trong thư mục hiện tại sẽ có thêm folder venv/, bên trong gồm:

```less
venv/
├── bin/        # chứa python, pip, activate script
├── lib/        # chứa thư viện Python cài bằng pip (theo version)
├── include/    # header files, ít khi đụng
├── pyvenv.cfg  # file cấu hình, chỉ đường đến Python gốc
```

cách dùng

```sh
source venv/bin/activate   # bật venv
pip install xxx            # cài lib cho project, phải có bước bật venv 
deactivate                 # thoát venv
```

## Cài đặt thư viện

```sh
pip install pillow pytesseract
```

# demo chạy thử 

```py
import pytesseract
from PIL import Image

# ảnh test
img_path = "./sample.png"

# mở ảnh
img = Image.open(img_path)

# OCR tiếng Anh (default)
text = pytesseract.image_to_string(
    img,
    lang="eng+vie+jpn" # nhiều ngôn ngữ
)
print("👉 Kết quả OCR:")
print(text)
```