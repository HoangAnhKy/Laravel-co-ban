- [Lưu ý](#lưu-ý)
  - [routing](#routing)
  - [Pipeline](#pipeline)
    - [khởi tạo pipeline](#khởi-tạo-pipeline)
    - [push dữ liệu lên store và dùng thêm cờ pipeline, nó sẽ chỉnh sửa đúng format bên trên](#push-dữ-liệu-lên-store-và-dùng-thêm-cờ-pipeline-nó-sẽ-chỉnh-sửa-đúng-format-bên-trên)
- [khởi tạo một dữ liệu lên store](#khởi-tạo-một-dữ-liệu-lên-store)
- [khởi tạo nhiều dữ liệu lên store](#khởi-tạo-nhiều-dữ-liệu-lên-store)

***

# Lưu ý

## routing

Routing chính là "Người chỉ đường" quyết định dữ liệu của sẽ được cất vào ngăn kéo vật lý nào trong kho.

lưu ý: hoạt động khi number_of_shards > 1

ví dụ tạo store my_products

```sh 
PUT /my_products
{
    "settings": {
        "number_of_shards": 5,
        "number_of_replicas": 0
    }
}
```

ví dụ khi tạo dữ liệu chuột sẽ cất vào ngăn chuột, khi search mà `routing=laptop&title=Chuột không dây Logitech` cũng sẽ không thấy vì khác routing(khác năng tìm kiếm)

```php
public function store()
{
    $params = [
        'index'   => 'my_products',
        'id'      => 'prod_refresh_test',
        'routing' => 'chuột',
        'body'    => [
            'title' => 'Chuột không dây Logitech',
            'price' => 500000
        ]
    ];

    $response = $this->client->index($params);
    return response()->json($response->asArray());
}
```

## Pipeline

format lại dữ liệu gửi lên cho đúng định dạng được quy định sẵn

### khởi tạo pipeline

- `ctx`: dữ liệu hiện tại
- `ctx._source`: dữ liệu trong kho lấy ra để  update

```php
public function setupPipeline()
{
    $scriptSource = "if (ctx.containsKey('price') && ctx.price != null) { ctx.final_price = ctx.price * 1.1; }";

    $params = [
        'id' => 'smart_product_processor',
        'body' => [
            'description' => 'Tự động chuẩn hóa dữ liệu',
            'processors' => [
                // 1. Viết hoa tiêu đề (thêm ignore_missing để nếu không có title thì bỏ qua)
                [
                    'uppercase' => [
                        'field' => 'title',
                        'ignore_missing' => true
                    ]
                ],
                // 2. Cắt khoảng trắng (thêm ignore_missing tương tự)
                [
                    'trim' => [
                        'field' => 'content',
                        'ignore_missing' => true
                    ]
                ],
                // 3. Tự tính giá sau thuế (Cần check null thật kỹ ở đây)
                [
                    'script' => [
                        'source' => $scriptSource
                    ]
                ],
                // 4. Đóng dấu thời gian, thêm mới nếu chưa có created_at_es. có rồi sẽ cập nhật ghi đè
                [
                    'set' => [
                        'field' => 'created_at_es',
                        'value' => '{{{_ingest.timestamp}}}'
                    ]
                ]
        ]
    ]
];

$this->client->ingest()->putPipeline($params);
return "Máy lọc V2 đã lên sóng, bao chấp mọi loại dữ liệu thiếu!";
}
```

### push dữ liệu lên store và dùng thêm cờ pipeline, nó sẽ chỉnh sửa đúng format bên trên

```php
public function store()
{
    $params = [
        'index' => 'my_products',
        'id' => 'sp_001',
        'pipeline' => 'smart_product_processor', // <--- Chỉ định dùng bộ lọc ở trên
        'body' => [
            'title' => 'chuột gaming logitech', // Gửi chữ thường
            'price' => 1000000,                // Giá gốc
            'content' => '   Hàng chính hãng, bảo hành 2 năm   ' // Có khoảng trắng thừa
        ]
    ];

    $response = $this->client->index($params);
    return response()->json($response->asArray());
}
```

# khởi tạo một dữ liệu lên store

```php
public function store()
{
    $params = [
        'index' => 'my_products', // Tương đương tên Table
        'id' => 'product_2',    // ID của bản ghi
        'body' => [
            'title' => 'Laptop MACBOOK PRO 2025',
            'content' => 'Cấu hình mạnh, pin trâu cho dân văn phòng',
            'price' => 410000
        ]
    ];

    $response = $this->client->index($params);
    return response()->json($response->asArray());
}
```

# khởi tạo nhiều dữ liệu lên store 

```php
public function bulkStore(){
    $params = [
        'body' => []
    ];

    $dataFromDB = [
        "product_3" => ['title' => 'iPhone 17 Pro', 'content' => '', 'price' => 3000],
        "product_4" => ['title' => 'Samsung S26 Ultra', 'content' => '', 'price' => 2800],
    ];

    $indexName = 'my_products';

    foreach ($dataFromDB as $key => $value) {
        // Lưu ý: Trong Bulk, các field metadata phải có dấu gạch dưới (_)
        // ngoại trừ routing không cần (_)
        $params['body'][] = [
            'index' => [
                '_index' => $indexName,
                '_id'    => $key
            ]
        ];
        $params['body'][] = $value;
    }

    $response = $this->client->bulk($params);
    return response()->json($response->asArray());
}
```