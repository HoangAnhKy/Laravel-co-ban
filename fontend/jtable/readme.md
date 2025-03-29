## Khai báo

- Download `jtable` về và khai báo vào `dự án` các phụ thuộc sau

  ```js
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link href="{{ asset("jtable.2.4.0/themes/metro/blue/jtable.css") }}" rel="stylesheet" type="text/css" />

  // jquery
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  // jtable
  <script src="{{asset("jtable.2.4.0/jquery.jtable.js")}}"></script>
  ```

## Cách dùng

- Tạo thẻ `div#table-id` để nhận dữ liệu
- Dùng jquery để load dữ liệu

  ```php
  <head>
      <link href="{{ asset("jtable.2.4.0/themes/metro/blue/jtable.css") }}" rel="stylesheet" type="text/css" />
  </head>
  <body>
      <div id="table-user"></div>
  </body>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  <script src="{{asset("jtable.2.4.0/jquery.jtable.js")}}"></script>

  <script type="text/javascript">
      $(document).ready(function () {
          $('#table-user').jtable({
              title: 'Table User',
              actions: {
                  listAction: function (postData, jtParams) {
                      return $.ajax({
                          url: '{{ route("api.get-user") }}',
                          type: 'GET',
                          dataType: 'json'
                      });
                  },
              },
              fields: {
                  id: { key: true, title: "id" },
                  name: { title: 'Name User' },
                  email: { title: 'Email' }
              }
          });

          <!-- load dữ liệu -->
          $('#table-user').jtable('load');
      });
  </script>
  ```

## title: 'Tiêu đề bảng'

## actions

- Sử dụng phần `actions` để lấy dữ liệu hoặc thao tác với dữ liệu `CRUD`
- `Json` trả về phải theo kiểu

  ```json
  // Khi lấy list
  {
    "Result": "OK",
    "Records": [
      { "id": 1, "name": "Alice", "email": "alice@example.com" },
      { "id": 2, "name": "Bob", "email": "bob@example.com" }
    ],
    "TotalRecordCount": 2
  }
  // khi lỗi
  {
    "Result": "ERROR",
    "Message": "Thông báo lỗi"
  }
  // khi thêm mới
  {
    "Result": "OK",
    "Record": { "id": 3, "name": "Charlie", "email": "charlie@example.com" }
  }
  // khi update với delete
  {
    "Result": "OK"
  }
  ```

- Mặc định với các action `CRUD` chỉ cần truyền link vào, phương thức mặc định là `POST`

  ```js
  actions: {
          listAction: '/users/list',
          createAction: '/users/create',
          updateAction: '/users/update',
          deleteAction: '/users/delete'
      }
  ```

- Tùy chỉnh lại method
  ```js
  listAction: function (postData, jtParams) {
                      return $.ajax({
                        url: '{{ route("api.get-user") }}',
                        type: 'GET',
                        data: jtParams,
                        dataType: 'json'
                      });
                  },
  ```

## fields

định nghĩa `các trường sẽ hiển thị hoặc thao tác trong jTable`. Mỗi key trong object fields tương ứng với một cột hoặc một thuộc tính của bản ghi:

    ```js
    fields: {
         id: {
                key: true,          // Chỉ định đây là khóa chính của bản ghi
                list: false         // Không hiển thị cột id trong bảng danh sách
            },
        name: {
                title: 'Name User',
                list: true,         // Hiển thị trên danh sách
                create: true,       // Cho phép nhập khi tạo mới
                edit: true,         // Cho phép sửa khi chỉnh sửa
                width: '30%'        // Độ rộng cột là 30%
            },
        email: { title: 'Email' },
        gender:{
            title: 'Giới tính',
            type: 'combobox',   // Sử dụng combobox cho dropdown select
            options: {          // Options có thể là một mảng hoặc object key-value
                'M': 'Nam',
                'F': 'Nữ'
            }
        },
        is_active: {
            title: 'Kích hoạt?',
            type: 'checkbox',   // Kiểu checkbox hiển thị giá trị true/false
            // Giá trị hiển thị khi checkbox được chọn hay không được chọn
            values: { 'false': 'Không', 'true': 'Có' },
            defaultValue: 'true'
        },
        is_radio:{
          type: 'radiobutton',
          options: { '1': 'Primary school', '2': 'High school', '3': 'University' }
        }
    }
    ```

- **Các thuộc tính thường gặp**

  - **`key`**: Chỉ định đây là khóa chính của bản ghi.

  - **`title`**: Tiêu đề hiển thị trên cột

  - **`type`**: Kiểu dữ liệu (text, textarea, date, checkbox, number...)

  - **`list`**: Cho phép hiển thị trên danh sách (true/false).

  - **`create`**: Cho phép hiển thị ô nhập khi tạo mới

  - **`edit`**: Cho phép hiển thị ô sửa khi chỉnh sửa.

  - **`width`**: Độ rộng cột (VD: '20%', '100px').

  - **`options`**: Nếu trường này cần chọn dữ liệu từ danh sách (ví dụ: dropdown), có thể cấu hình options là 1 mảng hoặc URL trả về JSON.

  - **`inputClass`**: Chỉnh lại css cho thẻ

## paging, pageSize, sorting, defaultSorting (Phân trang, sắp xếp)

- **Khi bật phân trang, cần trả về TotalRecordCount và Records trong JSON để jTable biết tổng số bản ghi.**

- **`paging`**: true: Bật phân trang.

- **`pageSize`**: 10: Mặc định 10 bản ghi trên 1 trang.

- **`sorting`**: true: Cho phép người dùng sắp xếp theo cột.

- **`defaultSorting`**: 'name ASC': Sắp xếp mặc định theo cột name tăng dần.

## Các thuộc tính / tùy chọn khác

- **`toolbar`**: Thêm nút custom vào toolbar của bảng.
  ```js
  toolbar: {
    items: [
      {
        icon: "/images/add.png", // Đường dẫn icon (hoặc có thể bỏ)
        text: "Thêm mới",
        click: function () {
          alert("Bạn đã nhấn vào nút Thêm mới!");
          // Hoặc mở modal để nhập dữ liệu mới
        },
      },
      {
        icon: "/images/reload.png",
        text: "Tải lại",
        click: function () {
          $("#PersonTableContainer").jtable("load");
        },
      },
    ];
  }
  ```
- **`selecting`**: Cho phép chọn dòng (row) trong bảng.

- **`multiSelect`**: Cho phép chọn nhiều dòng.

## formCreated chỉnh lại dữ liệu hiện thị cho form add/edit

```js
formCreated: function (event, data) {
            // Thêm class Bootstrap vào form mặc định của jTable
            data.form.addClass('modal-content p-3'); // Thêm class cho form
            data.form.find('input').addClass('form-control mb-2'); // Thêm class vào input
            data.form.find('.jtable-input-label').addClass('form-label'); // Thêm class vào label
            data.form.find('.jtable-toolbar-item').addClass('btn btn-primary'); // Chỉnh button
        }
```
