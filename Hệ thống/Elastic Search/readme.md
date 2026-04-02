# Cài đặt thư viện

```sh
composer require elasticsearch/elasticsearch
```

## Kết nối thêm [docker-file](./docker-compose.yml)

- Giao diện ở [port5601](http://localhost:5601/)

# Cấu hình Kết nối (Service Provider)

## Tạo provider

```sh
php artisan make:provider ElasticsearchServiceProvider
```

## Thêm thông tin khởi tạo Elastic search

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Client;
class ElasticsearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function ($app) {
            return ClientBuilder::create()
                ->setHosts([config('services.elasticsearch.host')])
                ->build();
        });
    }
}
```

## vào `bootstrap/providers.php` kiểm tra xem có provider mới thêm vào chưa, nếu chưa thì tự thêm tay vào

# Thêm cấu hình vào `config/services.php`

```php
'elasticsearch' => [
    'host' => env('ELASTICSEARCH_HOST', 'http://localhost:9200'),
],
```


# Demo cơ bản

## Khởi tạo controller

```sh
php artisan make:controller ClientController
```
## thêm hàm store để  Đẩy dữ liệu lên và search để  Tìm kiếm
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Elastic\Elasticsearch\Client;

class ClientController extends Controller
{
    public function store(Client $client)
    {
        $params = [
            'index' => 'my_products', // Tương đương tên Table
            'id' => 'product_1',    // ID của bản ghi
            'body' => [
                'title' => 'Laptop Dell XPS 15',
                'content' => 'Cấu hình mạnh mẽ cho lập trình viên PHP',
                'price' => 2500
            ]
        ];

        $response = $client->index($params);
        return response()->json($response->asArray());
    }

    public function search(Request $request, Client $client)
    {
        $params = [
            'index' => 'my_products',
            'body' => [
                'query' => [
                    'match' => [
                        'title' => $request->q // Tìm kiếm theo tiêu đề
                    ]
                ]
            ]
        ];

        $results = $client->search($params);

        // Xử lý kết quả trả về
        return response()->json($results->asArray()['hits']['hits']);
    }
}
```

# Chi tiết về  dữ liệu

## Nơi lưu

sau khi dữ liệu được gửi lên từ SQL nó sẽ nằm trong `Index Management`

- **Cách vào**: Menu bên trái -> Management -> Stack Management -> Index Management.
- Tại đây sẽ thấy: Tên các Index mà Laravel đã tạo (ví dụ: products hoặc posts). Sẽ thấy cột Docs count (số lượng bản ghi) và Storage size (dung lượng chiếm dụng).


## Xem chi tiết thông tin 
1. Bấm Menu ☰ (góc trên trái) -> chọn Discover (nằm trong mục Analytics).
2. Vì đây là lần đầu, Kibana sẽ yêu cầu tạo một cái Data View (để nó biết là đang muốn xem index nào).
3. Bấm nút Create data view (nằm ở kế bên nút Data View).
4. Ô Name và Index pattern: gõ đúng chữ my_products.
5. Bấm Save data view to Kibana.