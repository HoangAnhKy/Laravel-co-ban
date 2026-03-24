```py
from paddleocr import PaddleOCR 
import numpy as np
import re
import json

ocr = PaddleOCR(
    text_detection_model_name="PP-OCRv5_mobile_det",
    text_recognition_model_name="latin_PP-OCRv5_mobile_rec",
    use_doc_orientation_classify=False,
    use_doc_unwarping=False,
    use_textline_orientation=False,
    # text_recognition_model_dir="./inference/inference", # mode đọc tiếng việt
    device="gpu",
)

img_path = './image_cccd/nam_1987.png'


def read_ocr(page, items):
    for text, poly in zip(page["rec_texts"], page["rec_polys"]):
        poly = np.array(poly)
        items.append({
            "text": text,
            "poly": poly,
            "xc": poly[:,0].mean(),
            "yc": poly[:,1].mean(),
        })


def group_by_row(items, rows):
    current = [items[0]]
    for item in items[1:]:
        if abs(item["yc"] - current[-1]["yc"]) < 12:
            current.append(item)
        else:
            rows.append(current)
            current = [item]
    rows.append(current)
    return rows

def group_by_poly(rows):
    merged_rows = []
    for row in rows:
        row.sort(key=lambda x: x["xc"])

        xs, ys = [], []
        for it in row:
            xs.extend(it["poly"][:,0])
            ys.extend(it["poly"][:,1])

        x1, y1 = int(min(xs)), int(min(ys))
        x2, y2 = int(max(xs)), int(max(ys))

        merged_rows.append({
            "text": " ".join(it["text"] for it in row),
            "bbox": [x1, y1, x2, y2]
        })
    return merged_rows

result = ocr.predict(img_path)  
rows = []

def map_data_from_rows(merged_rows):
    db_data = {
        "id_number": "",
        "full_name": "",
        "birthday": "",
        "hometown": "",
        "address": ""
    }
    
    # Ép toàn bộ text thành 1 chuỗi để dùng Regex săn Số ID và Ngày sinh
    full_text = " | ".join([row["text"] for row in merged_rows])
    
    # 1. Tìm Số CCCD (12 số) - Ảnh của bro làm mờ nên có thể sẽ ra rỗng, nhưng chạy ảnh thật sẽ ăn
    id_match = re.search(r'\b\d{12}\b', full_text)
    if id_match: 
        db_data["id_number"] = id_match.group()
        
    # 2. Tìm Ngày Sinh (Xử lý luôn cả lỗi nuốt mất dấu / của AI: VD 2212/1987)
    # Regex này chia làm 3 nhóm: 2 số - 2 số - 4 số, ở giữa có thể có hoặc ko có ký tự
    birthday_match = re.search(r'(\d{2})[^\d]?(\d{2})[^\d]?(\d{4})', full_text)
    if birthday_match: 
        db_data["birthday"] = f"{birthday_match.group(1)}/{birthday_match.group(2)}/{birthday_match.group(3)}"

    # 3. Quét từng dòng để lấy Tên và Địa chỉ
    for i, row in enumerate(merged_rows):
        text = row["text"]
        text_upper = text.upper()
        
        # --- BẮT HỌ VÀ TÊN ---
        # Dùng từ khóa tiếng Anh "FULL NAME" cho chắc, nhỡ AI đọc sai chữ "Họ và tên"
        if "FULL NAME" in text_upper or "HỌ VÀ TÊN" in text_upper:
            # Nếu tên bị gom chung dòng (VD: "Full name: NGUYỄN THÁI NHẬT")
            if ":" in text and len(text.split(":", 1)[1].strip()) > 3:
                db_data["full_name"] = text.split(":", 1)[1].strip()
            # Nếu tên rớt xuống dòng kế tiếp (Giống trong JSON của bro)
            elif i + 1 < len(merged_rows):
                db_data["full_name"] = merged_rows[i+1]["text"].strip()
                
        # --- BẮT QUÊ QUÁN ---
        # Bắt chữ "ORIGIN" để né lỗi AI đọc thành "Quê quặn"
        elif "ORIGIN" in text_upper or "QUÊ QUÁN" in text_upper:
            val = ""
            if ":" in text:
                val = text.split(":", 1)[1].strip() + ", " if text.split(":", 1)[1].strip() else ""
            if i + 1 < len(merged_rows):
                next_text = merged_rows[i+1]["text"]
                # Đảm bảo dòng dưới không phải là mục Nơi thường trú
                if "RESIDENCE" not in next_text.upper() and "THƯỜNG TRÚ" not in next_text.upper():
                    val += next_text.strip()
            db_data["hometown"] = val.strip(", ")
            
        # --- BẮT NƠI THƯỜNG TRÚ ---
        elif "RESIDENCE" in text_upper or "THƯỜNG TRÚ" in text_upper:
            val = ""
            # Xử lý khúc bị dính liền: "residence:TDPThượng"
            if ":" in text:
                val = text.split(":", 1)[1].strip() + ", " if text.split(":", 1)[1].strip() else ""
            if i + 1 < len(merged_rows):
                # Cộng luôn dòng địa chỉ rớt bên dưới vào
                val += merged_rows[i+1]["text"].strip()
            db_data["address"] = val.strip(", ")

    return db_data

for res in result:
    items = []
    read_ocr(res, items)
    if not items: continue
    
    # ---------------------------------------------------------
    # BƯỚC LỌC NHIỄU: XÓA SẠCH RÁC Ở GÓC DƯỚI BÊN TRÁI
    # ---------------------------------------------------------
    # Tìm tọa độ X, Y lớn nhất để ước lượng Chiều Rộng và Chiều Cao của thẻ
    max_x = max([it["xc"] for it in items])
    max_y = max([it["yc"] for it in items])
    
    # Lọc mảng items: Bỏ qua những ô nằm ở 35% bên trái VÀ 30% dưới cùng
    # (Đây chính xác là "vùng cấm" chứa chữ Có giá trị đến/Date of expiry)
    clean_items = []
    for it in items:
        if it["xc"] < (max_x * 0.35) and it["yc"] > (max_y * 0.70):
            continue # Ném nó đi, không cho vào mảng!
        clean_items.append(it)
        
    items = clean_items # Gán lại mảng đã được dọn rác
    items.sort(key=lambda x: (x["yc"], x["xc"]))
    group_by_row(items, rows)
    
    merged_rows = group_by_poly(rows)
    final_data = map_data_from_rows(merged_rows)
    print(json.dumps(final_data, ensure_ascii=False, indent=4))
```