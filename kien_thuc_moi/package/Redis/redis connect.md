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

- Thay đổi chế độ giám sát (supervised) thành systemd: chỗ comment supervised no

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

## kết nối với laravel trên VM

**Xác Nhận Cấu Hình Redis Cho Kết Nối Từ Xa**

mở cấu hình

```sh
sudo nano /etc/redis/redis.conf
```

thay đổi các dòng sau:

```sh
pidfile /var/run/redis/redis-server.pid
bind 0.0.0.0
protected-mode no # đặt protected-mode no, hãy đảm bảo rằng Redis được bảo mật bằng mật khẩu.
supervised systemd
requirepass your_redis_password
```

khởi động lại

```sh
sudo systemctl restart redis
```

Chỉnh Sửa File .env Trong Dự Án Laravel

```env
CACHE_STORE=redis


REDIS_HOST=YOUR_VM_IP
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379
```

# Cài giao diện redis-wed cho linux

Cập nhật hệ thống

```sh
sudo apt update
sudo apt upgrade -y

sudo apt install php libapache2-mod-php php-mysql php-redis git -y
```

Tải và Cài Đặt phpRedisAdmin

- Clone phpRedisAdmin

  ```sh
  cd /var/www/html
  sudo git clone https://github.com/erikdubbelboer/phpRedisAdmin.git redisadmin
  ```

- Cấu Hình phpRedisAdmin:

  - Đổi người sở hữu

    ```sh
    sudo chown -R www-data:www-data /var/www/html/redisadmin
    ```

  - Chỉnh Sửa File Cấu Hình:

    ```sh
    sudo nano /var/www/html/redisadmin/config.php
    ```

    Thay đổi các thông số kết nối Redis như sau:

    ```php
    <?php
    // Hostname hoặc IP của Redis server
    $redis_host = '192.168.129.130';

    // Port của Redis server
    $redis_port = 6379;

    // Mật khẩu Redis (nếu có)
    $redis_password = '';

    // Cơ sở dữ liệu Redis (mặc định là 0)
    $redis_database = 0;
    ?>
    ```

- Cấu Hình Apache Cho phpRedisAdmin

  - tạo htaccess

    ```sh
    sudo nano /var/www/html/redisadmin/.htaccess
    ```

    ```.htaccess
    AuthType Basic
    AuthName "Restricted Access"
    AuthUserFile /etc/apache2/.htpasswd
    Require valid-user
    ```

  - Tạo File .htpasswd và Thêm Người Dùng:

    ```sh
    sudo apt install apache2-utils -y
    sudo htpasswd -c /etc/apache2/.htpasswd your_username
    ```

- Cấu Hình Quyền Truy Cập Apache:

  - Đảm bảo rằng Apache cho phép sử dụng file .htaccess.

    - Mở File Virtual Host mặc định:

      ```sh
      sudo nano /etc/apache2/sites-available/000-default.conf
      ```

    - Thêm hoặc chỉnh

      ```apache.conf
      <Directory /var/www/html/>
          Options Indexes FollowSymLinks
          AllowOverride All
          Require all granted
      </Directory>
      ```

- Reset apache và chạy composer

  ```sh
  sudo systemctl restart apache2
  cd /var/www/html/redisadmin
  sudo composer install
  ```

## Cài giao diện reids với node js

Cài Đặt Node.js và npm

```sh
sudo apt update
sudo apt upgrade -y

# cài node
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

Cài redis

```sh
sudo npm install -g redis-commander
```

chạy redis

```sh
redis-commander
```

- Cấu Hình Redis Commander Làm Dịch Vụ systemd. Để nó tự chạy khi khởi động hệ thống

  ```sh
  sudo nano /etc/systemd/system/redis-commander.service
  ```

  ```ini
  [Unit]
  Description=Redis Commander
  After=network.target

  [Service]
  ExecStart=/usr/bin/redis-commander --redis-host 192.168.129.130 --redis-port 6379 --redis-password your_redis_password
  Restart=always
  User=www-data
  Environment=PATH=/usr/bin:/usr/local/bin
  Environment=NODE_ENV=production

  [Install]
  WantedBy=multi-user.target
  ```

  Lưu Ý:

  - Thay 192.168.129.130, 6379, và your_redis_password bằng thông tin thực tế của Redis server.
  - Đảm bảo rằng đường dẫn đến redis-commander là chính xác. Nếu cần, có thể tìm đường dẫn bằng cách chạy which redis-commander.

chạy tự động redis

```sh
sudo systemctl daemon-reload
sudo systemctl start redis-commander
sudo systemctl enable redis-commander
sudo systemctl status redis-commander
```
