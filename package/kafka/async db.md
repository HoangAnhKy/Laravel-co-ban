Sử dụng nguyên lý pub-sub và ở chỗ sub(consume) thêm giữ liệu vào db.

- Sử dụng code cũ [pub-sub](./pub-sub/pub-sub%20thuần%20php.md)
- Tham khảo thêm ở chỗ [thêm db mongodb](../../Modal/connect%20mongodb.md)
- chỗ pub

  ```php
  Route::get('/test', function () {
      $request = [
          "name" => fake()->name(),
          "email" => fake()->unique()->safeEmail( ),
          "password" => "123"
      ];

      $user = Users::create($request); // Tạo user mới

      $config = new ProducerConfig();
      $config->setBootstrapServers(env('KAFKA_BROKERS'));
      $config->setAcks(-1);

      $producer = new Producer($config); // Tạo Producer
      $producer->send(env('KAFKA_TOPIC'), $user);
  });
  ```

- chỗ sub

  ```php
  <?php

  namespace App\Console\Commands;

  use Illuminate\Console\Command;
  use Illuminate\Support\Facades\DB;
  use longlang\phpkafka\Consumer\Consumer;
  use longlang\phpkafka\Consumer\ConsumerConfig;

  class ConsumeKafkaMessage extends Command
  {
      protected $signature = 'kafka:consume';
      protected $description = 'Nhận tin nhắn từ Kafka';

      public function handle()
      {
          $config = new ConsumerConfig();
          $config->setBootstrapServers(env('KAFKA_BROKERS'));
          $config->setGroupId(env('KAFKA_GROUP'));
          $config->setTopic(env('KAFKA_TOPIC'));
          $config->setAutoCommit(false);

          $consumer = new Consumer($config); // Tạo Consumer

          while (true) {
              $message = $consumer->consume();
              if ($message) {
                  $user = json_decode($message->getValue(), true);
                  DB::connection('mongodb')->table('users')->insert($user); // Lưu vào MongoDB

                  $this->info("Received message: " . $message->getValue());
                  $consumer->ack($message);
              }
          }
      }
  }
  ```
