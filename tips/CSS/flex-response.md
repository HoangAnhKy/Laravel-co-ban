# code html demo
```html
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Flex-grow & Wrap Demo</title>
<style>
    .container {
        display: flex;
        flex-wrap: wrap; /* Cho phép xuống dòng */
        gap: 10px;
        background: #eee;
        padding: 10px;
    }
    .item {
        flex-grow: 1; /* Giãn ra chiếm không gian trống */
        flex-basis: 150px; /* Chiều rộng cơ bản */
        height: 80px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 20px;
        color: white;
    }
    .a { background: red; }
    .b { background: blue; flex-grow: 2; } /* Gấp đôi các item khác */
    .c { background: green; }
    .d { background: orange; }
    .e { background: purple; }
</style>
</head>
<body>
    <div class="container">
        <div class="item a">A</div>
        <div class="item b">B</div>
        <div class="item c">C</div>
        <div class="item d">D</div>
        <div class="item e">E</div>
    </div>
</body>
</html>
```
# code boostrap
```html
<div class="d-flex flex-wrap gap-3 bg-light p-3">
    <div class="flex-grow-1 bg-danger" style="height:80px;min-width:150px;"></div>
    <div class="flex-grow-2 bg-primary" style="height:80px;min-width:150px;"></div>
    <div class="flex-grow-1 bg-success" style="height:80px;min-width:150px;"></div>
    <div class="flex-grow-1 bg-warning" style="height:80px;min-width:150px;"></div>
    <div class="flex-grow-1" style="height:80px;min-width:150px; background-color: purple;"></div>
</div>
```
# giải thích

- khi nó quá tap thì nó tự trôi xuống dòng và nó dãn cột ra hết màn hình
- thích hợp tích hợp table add/edit khi muốn có nhiều row trong đó  
- nguyên lý chỉ cần đó là `flex` và nó có thể  xuống hàng(`flex-wrap`) và `flex-grow`
- `flex-grow` trong CSS là thuộc tính của Flexbox cho biết mức độ một item `được phép giãn` ra chiếm phần không gian trống còn lại trong container. 1 là mặc định, 2 là gấp đôi
- `flex-basis`: Kích thước cơ bản (trước khi giãn/thu)
- `flex-shrink`: Xác định mức độ co lại của item khi container bị hẹp, nó có các giá trị:
    - 0: không co
    - 1: co 
    - 2: co gấp đôi