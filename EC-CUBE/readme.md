# Cài đặt source

php version 8.1

## Version mặc định

```sh
composer create-project ec-cube/ec-cube ec-cube 
```

## Version cụ thể

```sh
composer create-project ec-cube/ec-cube ec-cube "4.3.*" --keep-vcs
```

# Cấu hình DB

Sửa lại cấu hình file `env`

```env
DATABASE_URL=mysql://tên_user:mật_khẩu@127.0.0.1/tên_database
```

# Chạy lệnh cấu hình 

```sh
bin/console eccube:install
```

# Chạy server

```sh
php -S 127.0.0.1:8000
```

nếu có lỗi 

```env
APP_ENV=dev
APP_DEBUG=1
```