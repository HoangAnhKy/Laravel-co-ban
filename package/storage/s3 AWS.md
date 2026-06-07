# **cài đặt thư viện**

```sh
composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies
```

# Cấu hình s3

## Tạo người dùng 
1) Nhấn vào **User** góc phải màn hình > **security credentials**
2) Chọn Users > Create User.
3) Nhấn Next để tạo người dùng 
4) Sau khi có người dùng nhấn vô đó > **security credentials** 
5) chọn **create access key** > **orther** > nhấn next cho tới khi download csv
6) chọn cấp quyền > **Attach policies directly** > search s3 và chọn **AmazonS3FullAccess**


## đăng ký bucket

1) Tìm kiếm s3 sau đó click vô s3 > **create bucket** 
2) đặt tên rồi tìm nhấn **create**
3) phải public nó thì mới dùng link **Object URL** coi được. Chọn **bucket** > **permission** > **Object Ownership** enable nó lên
4) **Block public access (bucket settings)** > click tất cả mở > comfirm
5) quay lại **object** chọn fodel mới up > **action** > **Make publick using** ACL

# Cấu hình env

```env
// nằm trong file down ban đầu
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
// tên region chọn ban đầu hoặc có trên url
AWS_DEFAULT_REGION=us-east-1
// tên bucket đã đăng ký
AWS_BUCKET=laravel11
AWS_USE_PATH_STYLE_ENDPOINT=false
```