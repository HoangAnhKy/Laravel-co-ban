# Lý thuyết

Vì khi khởi tạo provider đã thêm khúc khởi tạo client tới `elasticSearch` nên chỉ việc `injection` ra dùng thôi

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

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
```

# Cấu trúc

- injection lại client để  thao tác với elastic search 

    ```php
    <?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use Elastic\Elasticsearch\Client;

    class ClientController extends Controller
    {
        private $client = null;

        public function __construct(Client $client)
        {
            $this->client = $client;
        }
    }
    ```

- khi dùng thì thường sẽ gửi $param tới elastic search để 

    ```php
    // ...
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
    // ...
    ```

# Các hàm hay dùng của client ElasticSearch

```php
/**
 * TỔNG HỢP CÁC HÀM ELASTICSEARCH PHP CLIENT (V8/V9) CHO LARAVEL
 */

// --- 1. NHÓM THAO TÁC DỮ LIỆU (CRUD) ---
$client->index($params);   // Thêm mới hoặc ghi đè (Replace) bản ghi
$client->get($params);     // Lấy 1 bản ghi dựa trên ID cụ thể
$client->update($params);  // Cập nhật một vài trường (không ghi đè toàn bộ)
$client->delete($params);  // Xóa bản ghi dựa trên ID
$client->exists($params);  // Trả về true/false nếu ID có tồn tại
$client->bulk($params);    // Hàm "thần thánh": Gửi hàng nghìn lệnh cùng lúc (siêu nhanh)

// --- 2. NHÓM TÌM KIẾM & ĐẾM (SEARCH) ---
$client->search($params);  // Hàm quan trọng nhất để tìm kiếm dữ liệu (Query DSL)
$client->count($params);   // Chỉ đếm số lượng kết quả (không lấy dữ liệu, rất nhẹ)
$client->msearch($params); // Gửi nhiều câu tìm kiếm khác nhau trong 1 lần gọi
$client->explain($params); // Debug: Xem tại sao bản ghi này lại có điểm (score) cao

// --- 3. NHÓM QUẢN LÝ KHO (INDICES API) ---
$client->indices()->create($params);    // Tạo một Index mới (tương đương Create Table)
$client->indices()->delete($params);    // Xóa sổ 1 Index (tương đương Drop Table)
$client->indices()->exists($params);    // Kiểm tra Index đã tồn tại chưa
$client->indices()->putMapping($params); // Định nghĩa kiểu dữ liệu (String, Int, Date...)
$client->indices()->getSettings($params); // Xem cấu hình Shards, Replicas của Index

// --- 4. NHÓM KIỂM TRA HỆ THỐNG (SYSTEM) ---
$client->info();             // Xem version (9.0.0), Cluster name, Tagline
$client->ping();             // Kiểm tra nhanh xem Server còn sống không
$client->cat()->indices();   // Liệt kê danh sách các Index và dung lượng, số docs
$client->cluster()->health(); // Xem trạng thái sức khỏe (Green/Yellow/Red) của cả cụm
```

