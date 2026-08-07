# Khái niệm

Trong Laravel là cơ chế giúp phát (broadcast) các sự kiện (event) real-time đến các client, thường là trình duyệt (JavaScript client) hoặc các ứng dụng khác. Nhờ đó, người dùng có thể cập nhật dữ liệu ngay lập tức mà không cần tải lại trang, tạo ra trải nghiệm real-time tương tự các ứng dụng chat, thông báo live, hoặc cập nhật trạng thái theo thời gian thực.

# Các thành phần chính của Broadcasting trong Laravel

- Event: Sự kiện mà bạn muốn gửi đến client.
- Channel: Một "kênh" để gửi dữ liệu đến client. Bạn có thể sử dụng kênh riêng tư hoặc công cộng.
- Listener (Client-side): Các script phía client để lắng nghe và xử lý sự kiện khi nó được gửi từ server.

# Cách hoạt động của Broadcasting

- `Event Broadcasting`: tạo một event Laravel như bình thường, sau đó đánh dấu event này có thể broadcast (thường thông qua `interface` `ShouldBroadcast` hoặc `ShouldBroadcastNow`). Khi phát (dispatch) event, Laravel sẽ broadcast sự kiện đó đến channel và dữ liệu được khai báo trong event.

  ```php
  use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

  class BroadcastingEvent implements ShouldBroadcast
  ```

- `Channels (Kênh)`: cho phép khai báo kênh `public` hoặc `private (có xác thực) trong file routes/channels.php`. Người dùng chỉ có thể lắng nghe (listen) channel private nếu họ vượt qua logic xác thực (authorization), đảm bảo tính bảo mật.

  ```php
     public function broadcastOn()
      {
          // return ['my-channel']; //public
          return new PrivateChannel('App.Models.User.' . $this->userID);
      }
  ```

- `Broadcast Driver`: hỗ trợ các driver như Pusher, Ably, Laravel WebSockets (package BeyondCode), hoặc Redis (dùng kèm socket.io hoặc tương tự). cần cấu hình trong file `.env` và `config/broadcasting.php` để kết nối với dịch vụ real-time tương ứng.

- `Frontend – Client`: dùng một thư viện như `Laravel Echo` để lắng nghe các event từ channel đã khai báo. Khi sự kiện được broadcast, client sẽ nhận ngay lập tức dữ liệu (ví dụ: chat message mới, thông báo mới, …) và cập nhật giao diện.

# Lệnh cài

```sh
php artisan install:broadcasting
# chọn no hết
```

file chỉnh sửa ở `config/broadcasting`

và nếu dùng `pusher` thì sài theo hướng dẫn của nó [demo](../package/Pusher/readme.md)


# Demo

Tính năng kiểm tra user update, chỉ cho phép 1 user update chung 1 thời điểm

## tạo route và mô phỏng tình huống update user

```php
<?php

use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// kiểm tra đăng nhập
Route::get('/', function () {
    return response()->json([
        'user' => auth()->user() ?? ''
    ]);
});
// trường hợp đăng nhập được giới hạn là 2 account này để  test trong db
Route::get('login', function (Request $request) {
    $user_id = $request->user_id ?? 1;
    switch ($user_id) {
        case 1: $email = 'admin@yopmail.com'; break;
        case 2: $email = 'teach@yopmail.com'; break;
    }
    $password = '123456';
    if (auth()->attempt(['email' => $email, 'password' => $password])) {
        return redirect()->intended('/');
    }
});

// CRUD bình thường
Route::get("user/{id}", [UsersController::class, 'edit'])->name('users.edit');
Route::put("user-update/{id}", [UsersController::class, 'update'])->name('users.update');

// Bắt đâù code tính năng
// khóa page
Route::get('lock-page', function (Request $request) {
    $page = $request->page; // lây qua axios
    $lockPage = \App\Models\LockPage::lockPageForUser($page);
    return response()->json([
        'message' => 'success',
        'last_time' => \App\Models\LockPage::$lastTime
    ]);
})->name('lock-page');

// mở khóa
Route::get('unlock-page', function (Request $request) {
    $page = $request->page;
    $lockPage = \App\Models\LockPage::unlockPageForUser($page);
    broadcast(new \App\Events\LockPageEvent($lockPage, $page));
    return response()->json([
      'message' => 'success',
      'can_edit' => true
    ]);
})->name('unlock-page');

// Kiểm tra nếu còn đang sửa thì cộng thêm
Route::get('update-time-page', function (Request $request) {
    $page = $request->page;
    $lockPage = \App\Models\LockPage::updateTime($page);
    return response()->json([
        'message' => 'success',
        'lock_page' => $lockPage ?? ''
    ]);
})->name('update-time-page');

// check xem có đang sửa ko. có thì cho đợi
Route::get('check-page', function (Request $request) {
    $page = $request->page;
    $response = ['message' => 'unlocked', 'can_edit' => true];

    $lock_page = \App\Models\LockPage::query()->where('page', $page)->where("lock_pages.status", '<>', 0)->first();
    if ($lock_page && $lock_page->user_id !== auth()->id()) {
        $response = ['message' => 'locked', 'can_edit' => false];
    }
    return response()->json($response);
})->name('check-page');
```

## ở  giao diện chính khởi tạo 1 số hàm mặc định

```php
//....
// lắng nghe sự kiện pub/sub
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script>
    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: '{{ env("REVERB_APP_KEY") }}',
        wsHost: '{{ env("REVERB_HOST") }}',
        wsPort: {{ env("REVERB_PORT", 8080) }},
        wssPort: {{ env("REVERB_PORT", 8080) }},
        forceTLS: '{{ env("REVERB_SCHEME", "http") }}' === 'https',
        enabledTransports: ['ws', 'wss']
    });

    const listenChannel = (channel, event, callback) => {
        if (typeof callback !== 'function') {
            console.error('callback must be a function');
            return;
        }
        window.Echo.private(channel).listen(`.${event}`, (e) => callback(e));
    };
</script>

<script>
    // format lại url làm key
    const formatURL = (url) => {
        return new URL(url).pathname.replace(/^\/+/, '').replace(/\//g, '');
    }
    // lock trang đang sửa lại và khi lock xog nó sẽ thực hiện callback theo nghiệp vụ riêng của từng trang.
    // nếu chưa xog thì cứ tới thời gian giới hạn nó sẽ tự chạy gia hạn
    const LockPageForUser = (callback) => {
        if (typeof callback !== 'function') {
            alert('callback must be a function')
            return
        }
        axios.get('{{ route('lock-page') }}', {
            params: {
                page: formatURL(window.location.href)
            }
        })
            .then((res) => {
                callback(res);
                const nextCheck = () => {
                    setTimeout(() => {
                        updateTime(() => {
                            console.log('lock extended');
                            nextCheck();
                        });
                    }, (res.data.last_time ?? 1) * 10000);
                };
                nextCheck();
            })
            .catch(() => alert('something went wrong'))
    }

    // mở nếu xog việc
    const unlockPage = (callback) => {
        if (typeof callback !== 'function') {
            alert('callback must be a function')
            return
        }
        axios.get('{{ route('unlock-page') }}', {
            params: {
                page: formatURL(window.location.href)
            }
        })
            .then((res) => callback(res))
            .catch(() => alert('something went wrong'))
    }

    // check xem có đang sửa ko
    const checkLockPage = (callback) => {
        if (typeof callback !== 'function') {
            alert('callback must be a function')
            return
        }
        axios.get('{{ route('check-page') }}', {
            params: {
                page: formatURL(window.location.href)
            }
        })
            .then((res) => callback(res))
            .catch(() => alert('something went wrong'))
    }

    // cập nhật time
    const updateTime = (callback) => {
        if (typeof callback !== 'function') {
            alert('callback must be a function')
            return
        }
        axios.get('{{ route('update-time-page') }}', {
            params: {
                page: formatURL(window.location.href)
            }
        })
            .then((res) => callback(res))
            .catch(() => alert('something went wrong'))
    }
</script>
//....
```

## trang edit ví dụ

```php
@extends('layout.default')
@section('content')
<div id="root">
    <h1>@{{ title }}</h1>

    <div>
        <div>
            <label for="name">Name: </label>
            <input v-model="user.name" type="text" name="name" id="name">
        </div>
        <div>
            <label for="email">Email: </label>
            <input v-model="user.email" type="text" name="email" id="email">
        </div>
        <button @click="update">update</button>
    </div>

</div>
@endsection
@push('js')
    <script>
        const app = Vue.createApp({
            data(){
                return {
                    title: 'Edit',
                    user: @json($user ?? []),
                    can_edit: null,
                    loading: false
                }
            },
            methods: {
              // cập nhật dữ liệu và unlock nếu cập nhật thành công
                update(){
                    axios.put('{{ route('users.update', ['id' => $user->id])  }}', this.user)
                    .then(response => {
                        unlockPage(() => {
                            alert(response.data.message);
                            window.location.href = '/'
                        });
                    })
                    .catch(error => {
                        console.log(error);
                    })
                }
            },
            mounted(){
                // check xem có đang sửa ko, có thì cập nhật trạng thái edit
                checkLockPage((res) => {
                    this.can_edit = res.data.can_edit
                })
            },
             watch: {
              // theo dõi trạng thái nếu có thì hỏi người ta đợi hay quay lại trang trước
                can_edit(val){
                     if(!val){
                         if (confirm('You can not edit this page, wait for unlock')) {
                             // OK
                             this.loading = true;
                             const channel ='page.' + formatURL(window.location.href);
                             console.log(channel)
                             listenChannel(channel, 'lock-page-event', (res)=>{
                                 alert('unlock')
                                 window.location.reload();
                             })
                             return;
                         } else {
                             // Cancel
                             window.location.href = '/'
                         }
                     }
                     // nếu là người đầu tiên vào thì lock :))
                    LockPageForUser((res) => {
                        console.log(res.data)
                    })
                 }
             },
             // giả xử cập nhật xog sẽ unlock
            loading(val){
                if(val){
                    alert('Please wait for unlock')
                }
            }
        });
        app.mount('#root');
    </script>
@endpush
```

## tạo Broadcast cho việc pub/sub về giao diện

```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class LockPageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $page;

    public function __construct($data, $page)
    {
        $this->data = $data;
        $this->page = $page;

        Log::info('LockPageEvent', ['data' => $data, 'page' => $page]);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('page.' . $this->page);
    }

    public function broadcastAs()
    {
        return 'lock-page-event';
    }
}
```
## tại channel thêm 1 channel lắng nghe

```php
Broadcast::channel('page.{page}', function ($user, $page) {
    \Illuminate\Support\Facades\Log::info('page', ['page' => $page]);
    return true;
});
```

## code và migrate của bảng lock

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LockPage extends Model
{
    public $guarded = [];
    protected $table = 'lock_pages';

    public static $lastTime = 1;

    public static function lockPageForUser($page){
        $page = self::query()->updateOrCreate(
            ['page' => $page],
            [
                'user_id' => auth()->id(),
                'status' => 1,
                'last_time' => now()->addMinutes(self::$lastTime)
            ]
        );
        return $page;
    }

    public static function updateTime($page){
        $page = self::query()->where('page', $page)->first();
        if (empty($page)) throw new \Exception('Page not found');
        if ($page->user_id !== auth()->id()) throw new \Exception('You are not authorized to unlock this page');
        $page->update(['last_time' => now()->addMinutes(self::$lastTime)]);
        return $page;
    }

    public static function unlockPageForUser($page){
        $page = self::query()->where('page', $page)->first();

        if (empty($page)) throw new \Exception('Page not found');
        if ($page->user_id !== auth()->id()) throw new \Exception('You are not authorized to unlock this page');
        $page->update(['status' => 0]);
        return $page;
    }
}
/*
Schema::create('lock_pages', function (Blueprint $table) {
    $table->id();
    $table->string('page');
    $table->unsignedBigInteger('user_id');
    $table->timestamp('last_time')->default(now()->addMinutes(10));
    $table->tinyInteger('status')->default(0)->comment('1: active, 0: inactive');
    $table->timestamps();
});
*/
```