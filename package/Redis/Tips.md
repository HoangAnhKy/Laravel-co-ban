# Tip ghi cache khi sập

Có một tình huống nguy hiểm gọi là `"Cache Stampede"` (hoặc Cache Miss đồng loạt): Khi một bản ghi Cache quan trọng bị hết hạn (expired), hàng nghìn Request cùng lúc đổ xô vào Database để lấy dữ liệu mới. Có cao kiến gì để hạn chế việc này không?

```php
use Illuminate\Support\Facades\Redis;
use App\Models\Post;

public function getHotPostsWithRedis()
{
    $cacheKey = 'hot_posts';
    $lockKey = 'hot_posts_lock';

    // Thử tạo Lock bằng lệnh SET (NX: chỉ set nếu chưa có, EX: hết hạn sau 10s)
    // Đây là cách Redis tạo Atomic Lock
    $isLocked = Redis::set($lockKey, 'true', 'EX', 10, 'NX');

    if ($isLocked) {
        try {
            $posts = Post::where('is_hot', 1)->get();
            // Lưu vào Redis (phải tự encode sang JSON)
            Redis::setex($cacheKey, 600, $posts->toJson());
            return $posts;
        } finally {
            // Xong việc phải tự xóa cái khóa này đi
            Redis::del($lockKey);
        }
    } else {
        // Đợi 1 giây rồi lấy lại cache
        sleep(1);
        $posts = Redis::get($cacheKey);
        return json_decode($posts);
    }
}
```

# trường hợp nó đợi lâu quá truy vấn cần time lớn


```php
use Illuminate\Support\Facades\Redis;
use App\Models\Post;

public function getHotPostsPureRedis()
{
    $cacheKey = 'hot_posts';
    $lockKey = 'hot_posts_lock';
    $maxTries = 20; // Thử lại tối đa 20 lần
    $tryCount = 0;

    while ($tryCount < $maxTries) {
        // 1. Kiểm tra xem có dữ liệu trong Cache chưa
        $posts = Redis::get($cacheKey);
        if ($posts) {
            return json_decode($posts);
        }
        // 2. Cố gắng lấy Khóa (NX = Only Set if Not Exists)
        // EX 10 = Khóa tự hủy sau 10s để tránh kẹt
        $isLocked = Redis::set($lockKey, 'true', 'EX', 10, 'NX');

        if ($isLocked) {
            try {
                // Chỉ ông lấy được khóa mới vào đây
                $data = Post::where('is_hot', 1)->get();
                Redis::setex($cacheKey, 600, $data->toJson());
                return $data;
            } finally {
                Redis::del($lockKey); // Xử lý xong nhả khóa ngay
            }
        }
        // 3. Nếu không lấy được khóa và cũng chưa có cache (ông kia đang làm)
        // Thì đợi một chút (200ms) rồi vòng lại kiểm tra tiếp
        usleep(200000); // 200ms = 0.2s
        $tryCount++;
    }

    return response()->json(['error' => 'Server busy'], 503);
}
```

# cache tự động

```php
// trong env set cache là redis

Route::get("/demo-lock", function () {
    $lockKey = 'hot_posts_lock';

    try {
        // lock key trong vòng 30, nếu mà đối tượng vào sau trong 5s mà có thể  lấy được key thì trả về. Không thì sẽ bị timeout
        // nó tự động xóa lock khi hoàn thành 
        return Cache::lock($lockKey, 30)->block(5, function () {
            sleep(20);
            return 'success';
        });
    } catch (\Illuminate\Contracts\Cache\LockTimeoutException $e) {
        return 'timeout!';
    }
});
```