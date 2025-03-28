# Danh mục

- [Tích hợp thư viện vào laravel qua thư viện](#tích-hợp-thư-viện-vào-laravel-qua-thư-viện)
- [Khởi tạo](#khởi-tạo-giao-diện-với-cdn)
- [Call ajax lấy dữ liệu](#lấy-dữ-liệu)
- [Truyền dữ liệu lên form](#nhận-dữ-liệu)
- [Các option](#option-hay-dùng)
- [select row](#select)
- [button](#button)

## Tích hợp thư viện vào laravel qua thư viện.

```sh
composer require yajra/laravel-datatables-oracle
```

- Tối ưu hóa DataTables Load dữ liệu nhanh hơn với Server-side processing
- Hỗ trợ AJAX Không cần load toàn bộ trang, chỉ tải JSON từ Laravel
- Tích hợp với Eloquent & Query Builder Dễ dàng lấy dữ liệu từ database
- Hỗ trợ xuất Excel/PDF Dùng với DataTables Buttons

## Khởi tạo giao diện với cdn

- Phải khai báo thẻ `table`

  ```html
  <!DOCTYPE html>
  <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <meta
        name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
      />
      <meta http-equiv="X-UA-Compatible" content="ie=edge" />
      <title>Datatable</title>
      <link
        rel="stylesheet"
        href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css"
      />
    </head>
    <body>
      <table id="table-user">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Created At</th>
          </tr>
        </thead>
      </table>

      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
      <script>
        new DataTable("#table-user", {
          responsive: true,
        });
      </script>
    </body>
  </html>
  ```

## Lấy Dữ liệu

```js
$("#myTable").DataTable({
  ajax: "/api/myData",
});
```

## Nhận Dữ Liệu

- Khi nhận dữ liệu có 2 loại: `Mảng`, `object`

  - Nhận dữ liệu với trường hợp là `mảng`
    ```js
    $("#example").DataTable({
      data: data,
    });
    ```
  - Nhận dữ liệu với trường hợp `object`

    ```js
    $("#example").DataTable({
      data: data,
      columns: [
        { data: "name" },
        { data: "position" },
        { data: "salary" },
        { data: "office" },
      ],
    });
    ```

  - Tùy chỉnh đầu ra

    ```js
    {
        data: 'price',
        render: function ( data, type, row ) {
            return '$'+ data;
        }
    }
    ```

## option hay dùng

- Nơi dùng

  ```js
  new DataTable("#table-id", {
    // option
  });
  ```

  | Tên option     | có thể dùng                        | mô tả                                             |
  | -------------- | ---------------------------------- | ------------------------------------------------- |
  | `ordering`     | `true` hoặc `false`                | Bật/tắt sắp xếp cột                               |
  | `order`        | list [[cột số, 'desc' hoặc 'asc']] | Thứ tự sắp xếp mặc định                           |
  | `paging`       | `true`/`false`                     | Bật tắt paginate                                  |
  | `info `        | `true`/`false`                     | Hiển thị thông tin tổng số bản ghi                |
  | `searching`    | `true`/`false`                     | Bật/tắt ô tìm kiếm                                |
  | `fixedHeader`  | `true`/`false`                     | Giữ tiêu đề cố định khi cuộn trang                |
  | `scrollY`      | số                                 | Tạo thanh scroll                                  |
  | `pageLength`   | số                                 | Xác định số dòng hiện thị                         |
  | `lengthMenu`   | List vd:[20, 40]                   | Cho phép chọn số dòng hiển thị                    |
  |                |                                    |                                                   |
  | `deferRender ` | `true`/`false`                     | Tối ưu hóa hiệu suất khi có dữ liệu lớn           |
  | `processing `  | `true`/`false`                     | Hiển thị trạng thái "Loading..." khi tải dữ liệu. |
  | `serverSide `  | `true`/`false`                     | Tải hết dữ liệu 1 lần                             |
  | `responsive `  | `true`/`false`                     | responsive                                        |

## Select

- [download hoặc tải cdn](https://cdn.datatables.net/select/)

- chọn option `select`: `true`/`false`

- dùng thêm jquery bắt sự kiện

  ```js
  table = new DataTable("#table-user", {
    select: {
      style: "os", // Cho phép chọn giống hệ điều hành (Ctrl + click)
      style: "multi", // Chọn nhiều dòng
    },
  });

  table.on("select", function (e, dt, type, indexes) {
    var data = table.rows(indexes).data().toArray();
    console.log("Hàng được chọn:", data);
  });

  // Khi người dùng bỏ chọn một hàng
  table.on("deselect", function (e, dt, type, indexes) {
    console.log("Hàng bị bỏ chọn");
  });
  ```

## Button

### CDN phụ thuộc

```html
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<!-- csv, copy-->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<!-- print-->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<!-- xlsx -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<!-- pdf-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
```

### option

- cơ bản
  ```js
  dom: 'Bfrtip',
  buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
  ```
- chỉnh màu sắc icon

  ```js
    dom: 'Bfrtip',
    buttons: [
        {
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel"></i> Xuất Excel',
            className: 'btn btn-success' // Thêm class Bootstrap
        },
        {
            extend: 'pdfHtml5',
            text: '<i class="fa fa-file-pdf"></i> Xuất PDF',
            className: 'btn btn-danger'
        }
    ]
  ```

- Tự code backend

  ```js
  dom: 'Bfrtip',
      buttons: [
          {
              text: 'Xuất Excel từ Laravel',
              action: function (e, dt, node, config) {
                  window.location.href = "/export-users"; // Gọi API Laravel để xuất Excel
              }
          }
      ]
  ```
