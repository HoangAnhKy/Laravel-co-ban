B1. Cấu hình giao diện

```js
// ...
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>

Pusher.logToConsole = true;

var pusher = new Pusher('9d0c4a69c464e0fef453', {
cluster: 'ap1'
});

var channel = pusher.subscribe('my-channel');
channel.bind('my-event', function(data) {
alert(JSON.stringify(data));
});
</script>
```

B2. Cấu hình laravel

- Cấu hình env, lấy trong `https://dashboard.pusher.com/apps/{app}/keys`

    ```env
    BROADCAST_DRIVER=pusher
    PUSHER_APP_ID=
    PUSHER_APP_KEY=
    PUSHER_APP_SECRET=
    PUSHER_APP_CLUSTER=
    ```

- Cài pusher và broadcasting, event

    ```sh
    composer require pusher/pusher-php-server
    php artisan install:broadcasting
        php artisan make:event MyEvent
    ```

-  Cấu hình file `event`

    ```php
    <?php

    namespace App\Events;

    use Illuminate\Queue\SerializesModels;
    use Illuminate\Foundation\Events\Dispatchable;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

    class MyEvent implements ShouldBroadcast
    {
        use Dispatchable, InteractsWithSockets, SerializesModels;

        public $message;

        public function __construct($message)
        {
            $this->message = $message;
        }

        public function broadcastOn()
        {
            return ['my-channel'];
        }

        public function broadcastAs()
        {
            return 'my-event';
        }
    }

    ```

-   Cấu hình file `config/broadcasting.php`

    ```php
    <?php

    return [


        'default' => env('BROADCAST_CONNECTION', 'null'),

        'connections' => [
            // ...
            'pusher' => [
                'driver' => 'pusher',
                'key' => env('PUSHER_APP_KEY'),
                'secret' => env('PUSHER_APP_SECRET'),
                'app_id' => env('PUSHER_APP_ID'),
                'options' => [
                    // 'cluster' => env('PUSHER_APP_CLUSTER'),
                    // 'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'mt1').'.pusher.com',
                    // 'port' => env('PUSHER_PORT', 443),
                    // 'scheme' => env('PUSHER_SCHEME', 'https'),
                    // 'encrypted' => true,
                    // 'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
                    'cluster' => 'ap1',
                    'useTLS' => true
                ],
            ],

            // ...
        ],

    ];

    ```

-   khi dùng chỉ gọi 

    ```php
    event(new MyEvent('hello world'));
    ```
