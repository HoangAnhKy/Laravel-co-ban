# kết nối
Laravel hỗ trợ kết nối tới `redis` qua **`predis`** với **`phpredis`**

- **`predis`**: Dễ dàng cài đặt và sử dụng, nhưng hiệu suất có thể không nhanh bằng **phpredis**.

    ```sh
    composer require predis/predis
    ```

- **`phpredis`**: Có hiệu suất tốt hơn, nhưng cần cài đặt thêm tiện ích mở rộng PHP. [Tải tiện ích mở rộng](https://pecl.php.net/package/redis/6.0.2/windows)

# Sử dụng docker 

## lệnh cài redis

```sh
docker run -d --name redis-stack -p 6379:6379 -p 8001:8001 redis/redis-stack:latest
```
**Mô tả lệnh**

- `--name redis-container`: Đặt tên cho container là redis-container.

- `-p 6379:6379`: Mở cổng 6379 để truy cập Redis từ bên ngoài container.

- `-d`: Chạy container ở chế độ background (detached mode).

- `redis`: Image chính thức của Redis từ Docker Hub.

Sau khi chạy lệnh này, Redis sẽ bắt đầu hoạt động và có thể truy cập Redis thông qua `localhost:6379.`

Để có thể tương tác với redis thì chạy lệnh

```sh
docker exec -it redis-container redis-cli
```

## Sài docker-composse

- Cài file yml

    ```yaml
    version: '3'
    services:
    redis:
        image: redis
        container_name: redis-container
        ports:
        - "6379:6379"
        volumes:
        - redis-data:/data

    volumes:
    redis-data:
    ```

- Sau khi có file yml chạy câu lệnh để bật redis

    ```sh
    docker-compose up -d
    ```

## kiểm tra kết nối

sau khi chạy được redis chúng ta kiểm tra nó bằng cách chạy nó ra PONG là ok

```sh
# > docker exec -it id_redis_container sh
# > redis-cli
#   127.0.0.1:6379> ping
#   PONG
```

# Cài đặt trên linux

Bước 1: Cài đặt các phụ thuộc cần thiết

```sh
sudo apt update
sudo apt install build-essential pkg-config libjemalloc-dev libssl-dev -y
```

Bước 2: Tải mã nguồn Redis

```sh
curl -O http://download.redis.io/redis-stable.tar.gz
```
Giải nén

```sh
tar xzvf redis-stable.tar.gz
cd redis-stable
```

Bước 3: Biên dịch Redis

```sh
make
```

```sh
sudo make install
```

Bước 4: Tạo cấu trúc thư mục và tệp cấu hình

```sh
sudo mkdir /etc/redis
sudo mkdir /var/redis
sudo mkdir /var/redis/6379
```

Sao chép tệp cấu hình mẫu vào thư mục cấu hình:

```sh
sudo cp redis.conf /etc/redis
```

chỉnh sửa tệp copy

```sh
sudo nano /etc/redis/redis.conf
```

Thay đổi các thiết lập sau:

+ Thay đổi chế độ giám sát (supervised) thành systemd: chỗ comment supervised no

    ```conf
    supervised systemd
    ```

Bước 5: Tạo tệp dịch vụ systemd cho Redis

```sh
sudo nano /etc/systemd/system/redis.service
```

với nội dung

```conf
[Unit]
Description=Redis In-Memory Data Store
After=network.target

[Service]
User=redis
Group=redis
ExecStart=/usr/local/bin/redis-server /etc/redis/redis.conf
ExecStop=/usr/local/bin/redis-cli shutdown
Restart=always

[Install]
WantedBy=multi-user.target
```

Bước 6: Tạo người dùng và quyền truy cập cho Redis

```sh
sudo adduser --system --group --no-create-home redis
sudo mkdir /var/lib/redis
sudo chown redis:redis /var/lib/redis
sudo chmod 770 /var/lib/redis
```

Bước 7: Khởi động và kích hoạt Redis

Nạp lại systemd để nhận diện tệp dịch vụ mới

```sh
sudo systemctl daemon-reload
```

Khởi động dịch vụ Redis:

```bash
sudo systemctl start redis
sudo systemctl enable redis
```

