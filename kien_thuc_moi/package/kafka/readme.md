Apache Kafka là một hệ thống truyền tin nhắn phân tán (distributed messaging system) có độ tin cậy cao, được thiết kế để xử lý dữ liệu theo thời gian thực với tốc độ cao. Nó hoạt động theo mô hình publish-subscribe (pub-sub) giúp truyền tải và xử lý dữ liệu giữa các hệ thống một cách nhanh chóng.

Kafka thường được sử dụng để:

- Streaming dữ liệu real-time (theo dõi hoạt động của người dùng, phân tích dữ liệu trực tuyến).
- Giao tiếp giữa các microservices theo kiến trúc event-driven.
- Làm hệ thống hàng đợi (message queue) thay thế Redis, RabbitMQ.
- Đồng bộ dữ liệu giữa các hệ thống khác nhau (MySQL, MongoDB, ElasticSearch…).

# Kafka Hoạt Động Như Thế Nào?

Kafka có 4 thành phần chính:

- Producer: Gửi dữ liệu (messages) vào Kafka.
- Broker: Máy chủ lưu trữ dữ liệu và quản lý streams.
- Topic: Chủ đề mà dữ liệu được gửi đến.
- Consumer: Đọc dữ liệu từ Kafka.
- Zookeeper: Quản lý metadata của Kafka, phân phối leader-follower.

# Lưu ý khi sử dụng

## [Docker file](./docker-compose.yml)

## Các kết nối

### kiểm tra xem có rdkafka không

- nếu ko có [Download rdkafka](https://pecl.php.net/package/rdkafka/6.0.5/windows)

- file chính vào ext và file phụ nếu có sẽ nằm cùng php.exe

- thêm vào `php.ini`

  ```php
  extension=rdkafka.so
  ```

- Kiểm tra đã cài chưa
  ```sh
   php -m | grep rdkafka
  ```

### So sánh các thư viện PHP-Kafka cho Laravel

| **Thư viện**                          | **Cần rdkafka không?** | **Hỗ trợ Laravel?**      | **Hiệu suất**               | **Dễ cài trên Windows?** | **Dùng khi nào?**                                  |
| ------------------------------------- | ---------------------- | ------------------------ | --------------------------- | ------------------------ | -------------------------------------------------- |
| **mateusjunges/laravel-kafka**        | ✅ Cần rdkafka         | ✅ Laravel Native        | ⚡ Nhanh                    | ❌ Khó trên Windows      | Khi bạn cần tích hợp chặt với Laravel              |
| **ensi-platform/laravel-php-rdkafka** | ✅ Cần rdkafka         | ✅ Laravel Native        | ⚡ Rất nhanh                | ❌ Khó trên Windows      | Khi bạn cần quản lý Kafka trong `config/kafka.php` |
| **jobcloud/php-kafka-lib**            | ✅ Cần rdkafka         | 🔸 Dùng được với Laravel | ⚡ Tốt                      | ❌ Khó trên Windows      | Khi muốn có nhiều cấu hình linh hoạt               |
| **longlang/phpkafka**                 | ❌ Không cần rdkafka   | 🔸 Dùng được với Laravel | 🚀 Nhanh trên Windows/Linux | ✅ Dễ cài                | Khi bạn cần đơn giản, không muốn cài `rdkafka`     |
