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
  version: "3"
  services:
    redis:
      image: redis/redis-stack:latest
      container_name: redis-container
      ports:
        - "6379:6379"
      volumes:
        - ./redis-data:/data
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


## Cập nhật kho phần mềm

```sh
sudo apt update
```

## Cài đặt Redis

```sh
sudo apt install redis-server
```

## Trạng thái của Redis

```sh
sudo systemctl status redis 
sudo systemctl restart redis
sudo systemctl start redis
sudo systemctl stop redis
```

## Kiểm tra nó chạy ok ko

```sh
redis-cli
#   127.0.0.1:6379> ping
#   PONG
```