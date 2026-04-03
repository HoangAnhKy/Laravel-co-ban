# Khởi tạo store 

```php
$client->indices()->create($params); 
```

# Kiểm tra store

```php
$client->indices()->exists($params); 
```

# Kiểm tra Mapping

```php
$client->indices()->getSettings($params);
```

# Thêm Mapping

```php
$client->indices()->putMapping($params); 
```

# Xóa Kho

```php
$client->indices()->delete($params); 
```

# refresh kho

```php
$client->indices()->refresh($params); 
```
# lấy alias

```php
$client->indices()->getAlias($params); 
```
# kiểm tra alias

```php
$client->indices()->existsAlias($params); 
```
# update alias

```php
$client->indices()->updateAliases($params); 
```

# Demo SeviceCRUDStore

```php
<?php

namespace App\Services;
use Elastic\Elasticsearch\Client;

class StorageElasticSearch
{
    private $client;

    public $indexName = 'my_products';
    public $aliasName = 'my_products_alias';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function createStore()
    {
        $params = [
            'index' => $this->indexName, // Tên kho
            'body' => [
                'settings' => [
                    'number_of_shards' => 3,   // Chia kho thành 3 ngăn vật lý (Hỗ trợ Routing)
                    'number_of_replicas' => 1  // Bản sao dự phòng
                ],
                'mappings' => [
                    'dynamic' => 'strict', // Không cho phép đẩy trường lạ
                    'properties' => [
                        'title' => [
                            'type' => 'text',    // Cho phép search từng từ (Gaming, Chuột)
                            'fields' => [
                                'keyword' => [   // Tạo thêm bản sao để Group/Sort chính xác
                                    'type' => 'keyword',
                                    'ignore_above' => 256
                                ]
                            ]
                        ],
                        'content' => ['type' => 'text'],
                        'price' => [
                            'type' => 'integer' // Kiểu số để dùng lệnh range (gte, lte)
                        ],
                        'category' => [
                            'type' => 'keyword' // Kiểu nguyên văn để Group (Aggs) cực nhanh
                        ],
                        'created_at' => [
                            'type' => 'date',
                            'format' => 'yyyy-MM-dd HH:mm:ss'
                        ]
                    ]
                ],
                'aliases' => [
                    $aliasName => (object)[] // Tạo luôn cái tên đại diện
                ]
            ]
        ];

        try {
            $response = $this->client->indices()->create($params);
            return response()->json($response->asArray());
        } catch (\Exception $e) {
            return "Lỗi rồi: " . $e->getMessage();
        }
    }

    public function checkStore()
    {
        // 1. Kiểm tra tồn tại
        if (!$this->client->indices()->exists(['index' => $this->indexName])->asBool()) {
            return "Kho không tồn tại!";
        }
        // 2. Lấy thông tin mapping
        $mapping = $this->client->indices()->getMapping(['index' => $this->indexName]);
        return response()->json($mapping->asArray());
    }
    public function addFieldStore()
    {
        $params = [
            'index' => 'my_products',
            'body'  => [
                'properties' => [
                    'brand' => ['type' => 'keyword'] // Thêm trường brand mà không cần xóa kho
                ]
            ]
        ];

        $this->client->indices()->putMapping($params);
        return "ok";
    }

    public function refreshStore()
    {
        return $this->client->indices()->refresh(['index' => $this->indexName]);
    }

    public function deleteStore(){
        $params = ['index' => 'my_products'];
        if ($this->client->indices()->exists($params)->asBool()) {
            $this->client->indices()->delete($params);
            return "Kho đã bị san phẳng!";
        }
        return "Làm gì có kho mà xóa?";
    }

    public function getAliasDetails()
    {
        $params = ['name' => $this->aliasName]; // Tên Alias cần check

        try {
            $response = $this->client->indices()->getAlias($params);
            return response()->json($response->asArray());
        } catch (\Exception $e) {
            return "Alias này không tồn tại hoặc có lỗi rồi!";
        }
    }
    public function checkAliasDetails()
    {
        $exists = $this->client->indices()->existsAlias(['name' => $this->aliasName])->asBool();
        return $exists ? "Alias vẫn đang online!" : "Alias bay màu rồi!";
    }

    public function switchAlias()
    {
        $params = [
            'body' => [
                'actions' => [
                    // Bước 1: Bảo Alias đừng trỏ vào v1 nữa
                    ['remove' => ['index' => 'my_products_v1', 'alias' => 'products_alias']],
                    // Bước 2: Bảo Alias trỏ ngay sang v2
                    ['add'    => [
                        'index' => 'my_products_v2', 
                        'alias' => 'products_alias',
                        'is_write_index' => true // <--- Dùng sẽ nhận dữ liệu mới khi dùng Alias vào nhiều store, nó sẽ thêm hết cả n store
                    ]]
                ]
            ]
        ];

        $this->client->indices()->updateAliases($params);
        return "Đã 've sầu thoát xác' sang kho V2 thành công!";
    }
}
```

# típs tạo Suggestions | tự động gợi ý khi search

## khởi tạo kho có dùng analysis và analyzer

`Analyzer` (Bộ phân tích) gồm
- `Character Filter` (Lọc ký tự)
- `Tokenizer` (Máy cắt từ)
- `Token Filter` (Bộ lọc hậu kỳ)

về  **analysis->filter**: 
- type:
  - edge_ngram: Đây là linh hồn của bộ lọc. Nó sẽ lấy một từ và "thái lát" nó từ phía bên trái (rìa - edge) sang phải.
  - ngram: Nó sẽ lấy một từ và "thái lát" nó từ mọi vị trí (giống slice trong js băm cho đến hết)
- min_gram => 1: Lát cắt nhỏ nhất là 1 ký tự. Tức là khách vừa gõ 1 chữ cái đầu tiên, máy đã tìm thấy hàng.
- max_gram => 20: Lát cắt dài nhất là 20 ký tự. Nếu tên sản phẩm dài hơn 20, nó sẽ dừng băm ở ký tự thứ 20.

về  **analyzer->autocomplete_analyzer->filter**

- `tokenizer => standard (Chia từ)`: Nó sẽ chia câu của Kyha dựa trên khoảng trắng và dấu câu.
- filter theo
  - `lowercase`: Nó chuyển tất cả về chữ thường để khách gõ chữ hoa hay chữ thường đều trúng.
  - `asciifolding`: chuyển tất cả tiếng việt có dấu về không giấu
  - `trim`: xóa bỏ khoảng trắng thừa ở 2 đầu
  - `unique`: xóa những từ trùng
  - `autocomplete_filter`: bộ filter ban nãy tạo
  - các hàm có thể dùng ở trong hàm `analysis->filter` custom để  xử lý khi cắt từ trước
    - `stopwords`: loại bỏ những từ bên trong('stopwords' => ['của', 'là', 'và']).
    - `synonyms`: Lọc từ đồng nghĩa (synonyms' => [ 'dt' => 'điện thoại', 'iphone, táo, apple => apple_product'])

```php
public function createStore()
{
    $params = [
        'index' => $this->indexName, // Tên kho
        'body' => [
            'settings' => [
                'analysis' => [
                    'filter' => [
                        'autocomplete_filter' => [
                            'type' => 'edge_ngram',
                            'min_gram' => 1, // Gõ 1 chữ đã gợi ý
                            'max_gram' => 20  // Độ dài tối đa của từ băm
                        ]
                    ],
                    'analyzer' => [
                        'autocomplete_analyzer' => [
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => [
                                'lowercase',
                                'autocomplete_filter' // Dùng bộ lọc băm nhỏ ở trên
                            ]
                        ]
                    ]
                ],
                'number_of_shards' => 3,   // Chia kho thành 3 ngăn vật lý (Hỗ trợ Routing)
                'number_of_replicas' => 1  // Bản sao dự phòng
            ],
            'mappings' => [
                'properties' => [
                    'title' => [
                        'type' => 'text',    // Cho phép search từng từ (Gaming, Chuột)
                        'fields' => [
                            'keyword' => [   // Tạo thêm bản sao để Group/Sort chính xác
                                'type' => 'keyword',
                                'ignore_above' => 256
                            ]
                        ],
                        // lưu ý dùng search_analyzer
                        'analyzer' => 'autocomplete_analyzer', // Lúc lưu thì băm nhỏ
                        'search_analyzer' => 'standard'
                    ],
                    'content' => ['type' => 'text'],
                ]
            ],
            'aliases' => [
                $this->aliasName => (object)[] // Tạo luôn cái tên đại diện
            ]
        ]
    ];

    try {
        $response = $this->client->indices()->create($params);
        return response()->json($response->asArray());
    } catch (\Exception $e) {
        return "Lỗi rồi: " . $e->getMessage();
    }
}
```

## getSuggestions
```php
public function getSuggestions($query)
{
$params = [
    'index' => $this->aliasName,
    'body'  => [
        'size' => 5, // Chỉ lấy 5 gợi ý đầu tiên cho nhẹ
        '_source' => ['title'], // Chỉ lấy tiêu đề cho nhanh
        'query' => [
            'match' => [
                'title' => [
                    'query' => $query,
                    'operator' => 'and'
                ]
            ]
        ]
    ]
];

$results = $this->client->search($params);

// Trả về mảng đơn giản cho Frontend dễ dùng
return collect($results->asArray()['hits']['hits'])->map(function($hit) {
    return $hit['_source']['title'];
});
```

## thêm sản phẩm test thử


```php
public function bulkStore()
{
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
        $params['body'][] = [
            'index' => [
                '_index' => $indexName,
                'routing' => "Phone",
                '_id' => $key
            ]
        ];
        $params['body'][] = $value;
    }

    $response = $this->client->bulk($params);
    return response()->json($response->asArray());
}
```

## Kết quả

khi search ipho nó sẽ ra iphone 17 pro


```php
Route::get("/Sug", function (){
    $storage = app(\App\Services\StorageElasticSearch::class);
    return $storage->getSuggestions("iPho");
});
/*
[
    "iPhone 17 Pro"
]
*/
```

# trường hợp muốn thay đổi dữ liệu search hoặc copy toàn bộ dữ liệu khi thì dùng `Reindex`

```php
public function proUpgrade()
{
    // 1. Tạo v2 với analysis mới
    $this->createStoreV2(); 

    // 2. Reindex (Đổ hàng từ cũ sang mới)
    $this->client->reindex([
        'body' => [
            'source' => ['index' => 'my_products_v1'], // Kho cũ
            'dest' => ['index' => 'my_products_v2'] // Kho mới
        ]
    ]);

    // 3. Đổi Alias (Khách vẫn search bình thường qua Alias)
    $this->switchAlias('my_products_v1', 'my_products_v2', 'products_alias');

    return "Nâng cấp hoàn tất, không mất một giây downtime nào!";
}
```

# tạo kho với dữ liệu mapping là 'type' => 'nested'

dùng để tăng độ chính xác khi search, sử  dụng khi search kết hợp 

## tạo kho có trường đặc biệt sử  dụng nested

```php
public function createStore()
{
    $params = [
        'index' => 'products_nested_v1',
        'body' => [
            'mappings' => [
                'properties' => [
                    'title' => ['type' => 'text'],
                    'variants' => [ // Đây là mảng chứa các phiên bản
                        'type' => 'nested', // <--- QUAN TRỌNG NHẤT
                        'properties' => [
                            'color' => ['type' => 'keyword'],
                            'storage' => ['type' => 'keyword'],
                            'price' => ['type' => 'integer']
                        ]
                    ]
                ]
            ],
        ]
    ];
    $response = $this->client->indices()->create($params);
    return response()->json($response->asArray());
}
```

## khởi  tạo search có thêm nested

```php
/*
$products = [
    [
        'id' => 'samsung-s24',
        'title' => 'Samsung Galaxy S24',
        'variants' => [
            ['color' => 'Đen', 'storage' => '256GB', 'price' => 22000000],
            ['color' => 'Vàng', 'storage' => '512GB', 'price' => 26000000]
        ]
    ],
    [
        'id' => 'macbook-m3',
        'title' => 'Macbook Pro M3',
        'variants' => [
            ['color' => 'Xám', 'storage' => '512GB', 'price' => 45000000]
        ]
    ]
];
*/

public function search(Request $request)
{
    $params = [
        'index' => 'products_nested_alias',
        'body'  => [
            'query' => [
                'nested' => [
                    'path' => 'variants', // Chỉ định tìm trong mảng variants
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['term' => ['variants.color' => 'Xám']],
                                ['term' => ['variants.storage' => '512GB']]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    $results = $this->client->search($params);
    return response()->json($results->asArray()['hits']['hits']);
}
```