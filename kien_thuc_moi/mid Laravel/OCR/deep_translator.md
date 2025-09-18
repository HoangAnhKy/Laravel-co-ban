`deep-translator` là một thư viện Python giúp dịch văn bản thông qua nhiều dịch vụ dịch khác nhau (Google Translate, Microsoft, LibreTranslate, Deepl, Yandex, …) mà không cần phải trực tiếp làm việc với API phức tạp.

# Các dịch vụ dịch hỗ trợ:
- `GoogleTranslator` → Google Translate
- `DeeplTranslator` → DeepL
- `MicrosoftTranslator` → Microsoft Translator
- `YandexTranslator` → Yandex Translate
- `LibreTranslator` → LibreTranslate (open source)
- `QcriTranslator`, PapagoTranslator, LingueeTranslator, …


# Các hàm chính thường dùng

## khởi tạo đối tượng dịch

```py
from deep_translator import GoogleTranslator

translator = GoogleTranslator(source='en', target='vi')

# source: ngôn ngữ gốc (vd: 'en', 'auto')
# target: ngôn ngữ đích (vd: 'vi', 'ja')
```

## `translate(text)` Dịch 1 chuỗi văn bản
```py
result = translator.translate("Hello world")
print(result)  # Xin chào thế giới
```

## `translate_batch(list_of_texts)` Dịch nhiều chuỗi cùng lúc

```py
sentences = ["Hello", "How are you?", "Goodbye"]
results = translator.translate_batch(sentences)
print(results)  # ['Xin chào', 'Bạn khỏe không?', 'Tạm biệt']
```

## `translate_file(path)` Dịch nội dung từ file

```py
result = translator.translate_file("document.txt")
print(result)
```

## `get_supported_languages()` Lấy danh sách ngôn ngữ được hỗ trợ

```py
langs = GoogleTranslator.get_supported_languages()
print(langs)

# Lấy danh sách dạng dict {tên_ngôn_ngữ: mã}:
langs = GoogleTranslator.get_supported_languages(as_dict=True)
print(langs["vietnamese"])  # vi
```
