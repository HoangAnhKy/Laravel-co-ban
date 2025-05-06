# Tài liệu tham khảo

- [jobcloud/php-kafka-lib](https://github.com/jobcloud/php-kafka-lib)
- [tải và cài đặt](../readme.md/#kiểm-tra-xem-có-rdkafka-không)

# cài đặt

```sh
composer require jobcloud/php-kafka-lib
```

# vì nó chạy cần rdkafka nên chạy với docker

- docker compose

  ```yml
  services:
  lrvkafka:
    build:
    context: .
    dockerfile: Dockerfile
    container_name: laravel-app
    ports:
      - 80:80
    restart: always
    volumes:
      - ./src:/var/www/html/
  ```

- httpd.conf

  ```conf
  <VirtualHost *:80>
      ServerAdmin lrvkafka.local.com
      DocumentRoot "/var/www/html/public"
      <Directory "/var/www/html/public">
          Options Indexes FollowSymLinks
          AllowOverride All
          Require all granted
      </Directory>
  </VirtualHost>
  ```

- dockerfile

  ```dockerfile
  FROM ubuntu:22.04

  # Đặt chế độ không tương tác
  ENV DEBIAN_FRONTEND=noninteractive

  # Cập nhật hệ thống, cài đặt Apache và PHP 8.4
  RUN apt-get update && apt-get upgrade -y && \
      apt-get install -y software-properties-common && \
      add-apt-repository ppa:ondrej/php -y && \
      apt-get update && \
      apt-get install -y \
      apache2 \
      php8.4 \
      php8.4-fpm \
      php8.4-common \
      php8.4-cli \
      php8.4-mbstring \
      php8.4-xml \
      php8.4-gd \
      php8.4-zip \
      php8.4-intl \
      php8.4-mysql \
      php8.4-curl \
      php8.4-bcmath \
      php8.4-dev \
      build-essential \
      curl \
      wget \
      tar \
      autoconf \
      pkg-config \
      librdkafka-dev \
      php-pear && \
      rm -rf /var/lib/apt/lists/*

  # Tạo các thư mục cần thiết cho Apache
  RUN mkdir -p /var/run/apache2 /var/lock/apache2 /var/log/apache2

  # Bật mod_rewrite và PHP-FPM
  RUN a2enmod rewrite proxy_fcgi setenvif && \
      a2enconf php8.4-fpm

  # Cấu hình Laravel VirtualHost (nếu cần)
  COPY ./httpd.conf /etc/apache2/sites-available/000-default.conf

  # Cài đặt extension php_rdkafka
  RUN pecl channel-update pecl.php.net && \
      printf "\n" | pecl install rdkafka && \
      echo "extension=rdkafka.so" > /etc/php/8.4/mods-available/rdkafka.ini && \
      phpenmod rdkafka

  # Cài đặt Composer
  RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

  # Mở cổng 80
  EXPOSE 80

  # Khởi động PHP-FPM và Apache
  CMD service php8.4-fpm start && apachectl -D FOREGROUND
  ```

# tạo config/kafka.php

```php
<?php

return [
    'brokers' => env('KAFKA_BROKERS', 'kafka:9092'), // do chạy với docker nên thay vô là kafka:9092
    'topic' => env('KAFKA_TOPIC', 'laravel-events'),
    'group_id' => env('KAFKA_GROUP_ID', 'laravel-consumer-group'),
];
```

# Khởi tạo service

```php
<?php

namespace App\Services;

use Jobcloud\Kafka\Message\KafkaProducerMessage;
use Jobcloud\Kafka\Producer\KafkaProducerBuilder;
use Exception;

class KafkaProducerService
{
    protected $producer;

    public function __construct()
    {
        $brokers = config('kafka.brokers');

        if (!$brokers) {
            throw new Exception("Kafka Brokers is not set! Check .env file.");
        }

        $this->producer = KafkaProducerBuilder::create()->withAdditionalBroker($brokers)
            ->build();
    }

    public function sendMessage(string $message)
    {
        $topic = config('kafka.topic');

        if (!$topic) {
            throw new Exception("Kafka Topic is not set! Check .env file.");
        }
        $kafkaMessage = KafkaProducerMessage::create($topic, -1)->withBody($message);
        $this->producer->produce($kafkaMessage);
        $this->producer->flush(10000); // không gọi flush(), message có thể chưa bao giờ thực sự gửi đi, đặc biệt trong script ngắn hoặc controller.nó đợi đầy mới gửi
    }
}
```

# chạy thử với đoạn code ở web

```php
use App\Services\KafkaProducerService;
use Illuminate\Http\Request;

Route::get('/send-message', function (Request $request, KafkaProducerService $producer) {
    $message = $request->get('message', 'test');
    $producer->sendMessage($message);
    return response()->json(['message' => 'Message sent to Kafka']);
});
```

# khởi tạo command

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Jobcloud\Kafka\Consumer\KafkaConsumerBuilder;
use Jobcloud\Kafka\Message\KafkaConsumerMessageInterface;
use Exception;

class KafkaConsumer extends Command
{
    protected $signature = 'kafka:consume';
    protected $description = 'Consume messages from Kafka topic';

    public function handle()
    {
        $brokers = config('kafka.brokers');
        $topic = config('kafka.topic');

        if (!$brokers) {
            $this->error("Kafka Brokers is not set!");
            return;
        }

        if (!$topic) {
            $this->error("Kafka Topic is not set!");
            return;
        }

        try {
            $consumer = KafkaConsumerBuilder::create()
                ->withAdditionalConfig([
                    'auto.offset.reset' => 'earliest', // or latest
                    'enable.auto.commit' => 'false', // Kiểm soát offset thủ công
                ])
                ->withAdditionalBroker($brokers)
                ->withConsumerGroup(config('kafka.group_id'))
                ->withAdditionalSubscription($topic)
                ->build();

            $consumer->subscribe(); // kết nối

            while (true) {
                $message = $consumer->consume(); // chỉnh timeout mặc định 10s
                if ($message instanceof KafkaConsumerMessageInterface) {
                    $this->info('Received message: ' . $message->getBody());
                }
                $consumer->commit($message);
            }
        } catch (Exception $e) {
            $this->error('Kafka Consumer Error: ' . $e->getMessage());
        }
    }
}
```

# chạy conmand consume

```sh
php artisan kafka:consume
```
