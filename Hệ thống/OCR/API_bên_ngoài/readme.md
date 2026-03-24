# lệnh cài

```sh
pip install google-genai
```

Dùng thẳng API Của Gemini, [vào Google AI Studio để lấy Key](https://aistudio.google.com/app/api-keys) 

# demo

```python
from google import genai
from google.genai import types

# 1. Khởi tạo Client (Cú pháp mới)
client = genai.Client(api_key="ĐIỀN_API_KEY_CỦA_AI_VÀO_ĐÂY")

# 2. Nạp ảnh (Đổi lại đúng đường dẫn ảnh)
img_path = './image_cccd/nam_1987.png' 
myfile = client.files.upload(file=img_path)

# 3. Viết "Bùa chú"
prompt = """
Hãy đọc các thông tin trên thẻ Căn cước công dân trong ảnh và trích xuất ra JSON theo đúng cấu trúc sau. 
Nếu thông tin nào bị che mờ, hãy để chuỗi rỗng "".
{
    "id_number": "Số CCCD",
    "full_name": "Họ và tên",
    "dob": "Ngày sinh (định dạng dd/mm/yyyy)",
    "hometown": "Quê quán",
    "address": "Nơi thường trú",
    "expiry_date": "Ngày hết hạn ở góc dưới cùng bên trái (định dạng dd/mm/yyyy)"
}
"""

print("🚀 Đang gửi ảnh cho Gemini xử lý...")

# 4. Gọi API (Cú pháp mới tinh)
response = client.models.generate_content(
    model='gemini-2.5-flash', # Bản 2.5 xịn nhất hiện tại
    contents=[myfile, prompt],
    config=types.GenerateContentConfig(
        response_mime_type="application/json", # Ép trả về JSON
    )
)

print("✅ JSON trả về:")
print(response.text)
```