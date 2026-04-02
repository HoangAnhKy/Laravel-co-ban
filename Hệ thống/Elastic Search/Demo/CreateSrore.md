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