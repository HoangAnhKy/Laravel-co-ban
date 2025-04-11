# **Hướng dẫn sử dụng SortableJS**

## **1. Cài đặt**
### Cài bằng NPM/Yarn:
```bash
npm install sortablejs
```

### Hoặc sử dụng CDN:
```html
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
```

---

## **2. Khởi tạo cơ bản**
Tạo danh sách HTML:
```html
<div id="sortable-list">
    <div class="item">Item 1</div>
    <div class="item">Item 2</div>
    <div class="item">Item 3</div>
</div>
```

Kích hoạt SortableJS:
```javascript
new Sortable(document.getElementById('sortable-list'), {
    animation: 150 // Hiệu ứng kéo thả
});
```

---

## **3. Các tùy chọn (Options)**
### **`animation`**
- Hiệu ứng kéo thả (đơn vị: milliseconds).
- Ví dụ:
    ```javascript
    animation: 150
    ```

### **`handle`**
- Chỉ định phần tử (class) có thể kéo được (drag handle).
- Ví dụ:
    ```javascript
    handle: '.drag-handle'
    ```

### **`filter`**
- Ngăn không cho kéo thả một số phần tử (class).
- Ví dụ:
    ```javascript
    filter: '.no-drag'
    ```

### **`group`**
- Cho phép kéo thả giữa nhiều danh sách.
- Ví dụ:
    ```javascript
    group: 'shared'
    ```
- Cờ 

    ```js
    group: {
            name: 'shared',
            pull: 'clone', // cho phép clone về
            put: false  // không cho phép kéo source khác vô
        }
    ```

### **`disabled`**
- Vô hiệu hóa kéo thả.
- Ví dụ:
    ```javascript
    disabled: true
    ```
### **`sort`**
- Sắp xếp
- Ví dụ:
    ```javascript
    sort: true,
    ```

---

## **4. Các sự kiện (Events)**
có thể lắng nghe các sự kiện trong quá trình kéo thả.

### **`onEnd`**
- Gọi khi việc kéo thả hoàn tất.
- Ví dụ:
    ```javascript
    onEnd: function (evt) {
        console.log('Thứ tự mới:', evt.to.children);
        /*
        // Lấy danh sách sau khi kéo thả
        const items = document.querySelectorAll('#sortable-list .item');
        const order = Array.from(items).map(item => item.dataset.id);
        console.log('New order:', order);
        */
    }
    ```

### **`onStart`**
- Gọi khi bắt đầu kéo.
- Ví dụ:
    ```javascript
    onStart: function (evt) {
        console.log('Bắt đầu kéo:', evt.item);
    }
    ```

### **`onAdd`**
- Gọi khi phần tử được thêm vào danh sách.
- Ví dụ:
    ```javascript
    onAdd: function (evt) {
        console.log('Phần tử được thêm:', evt.item);
    }
    ```

---
# DEMO

## **5. Kéo thả giữa nhiều danh sách**
Tạo nhiều danh sách:
```html
<div id="list-1">
    <div class="item">Item 1</div>
    <div class="item">Item 2</div>
</div>

<div id="list-2">
    <div class="item">Item 3</div>
    <div class="item">Item 4</div>
</div>
```

Kích hoạt SortableJS với `group`:
```javascript
new Sortable(document.getElementById('list-1'), {
    group: 'shared',
    animation: 150
});

new Sortable(document.getElementById('list-2'), {
    group: 'shared',
    animation: 150
});
```

---

## **6. Thay đổi dữ liệu sau khi kéo thả**
có thể lấy danh sách thứ tự mới sau khi kéo thả bằng cách sử dụng sự kiện `onEnd`:
```javascript
new Sortable(document.getElementById('sortable-list'), {
    animation: 150,
    onEnd: function (evt) {
        const items = Array.from(evt.to.children).map(child => child.innerText);
        console.log('Thứ tự mới:', items);
    }
});
```

---

## **7. Hỗ trợ kéo thả với dữ liệu (Data Binding)**
Dùng `data-id` để lưu trữ ID và lấy thứ tự mới:
```html
<div id="sortable-list">
    <div class="item" data-id="1">Item 1</div>
    <div class="item" data-id="2">Item 2</div>
    <div class="item" data-id="3">Item 3</div>
</div>
```

Lấy thứ tự mới sau khi kéo thả:
```javascript
onEnd: function (evt) {
    const newOrder = Array.from(evt.to.children).map(child => child.dataset.id);
    console.log('Thứ tự ID mới:', newOrder);
}
```

---

## **8. Vô hiệu hóa kéo thả tạm thời**
Dùng tùy chọn `disabled` để bật/tắt kéo thả:
```javascript
const sortable = new Sortable(document.getElementById('sortable-list'), {
    animation: 150
});

// Vô hiệu hóa
sortable.option('disabled', true);

// Kích hoạt lại
sortable.option('disabled', false);
```

---

## **9. Xóa Sortable**
Nếu muốn xóa hoặc hủy kích hoạt Sortable:

```javascript
sortable.destroy();
```

---

## **10. Tài liệu tham khảo**
- [Trang chủ SortableJS](https://github.com/SortableJS/Sortable)
- [Demo chính thức](https://sortablejs.github.io/Sortable/)

