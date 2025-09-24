## Bật các module cần thiết 

- các mode: `(proxy + http + balancer + lbmethod + headers).`
- Đối với `laragon`: Mở file `httpd.conf hoặc tạo file riêng` trong `conf/extra` của `Laragon`. restart apache

    ```apache
    LoadModule proxy_module modules/mod_proxy.so                                
    # chuyển request từ client sang server khác.

    LoadModule proxy_http_module modules/mod_proxy_http.so                      
    # hỗ trợ proxy HTTP/HTTPS.

    LoadModule proxy_balancer_module modules/mod_proxy_balancer.so              
    # bật load balancer trong Apache.
    
    LoadModule lbmethod_byrequests_module modules/mod_lbmethod_byrequests.so    
    # cung cấp thuật toán phân phối request mặc định (byrequests) → round-robin.
    ```

- Đối với `apache` đã được cài trên linux, bất các mode hỗ trợ cho nó lên, Sau đó reload/restart Apache:

    ```sh
    # Bật module proxy và load balancer
    sudo a2enmod proxy
    sudo a2enmod proxy_http
    sudo a2enmod proxy_balancer
    sudo a2enmod lbmethod_byrequests
    sudo a2enmod headers
    ```

## Thêm cấu hình Load Balancer
Apache sẽ lắng nghe port 8080 để không trùng với port 80 mặc định.
```apache
<Proxy "balancer://laravel_cluster">
    BalancerMember http://127.0.0.1:9001
    BalancerMember http://127.0.0.1:9002
</Proxy>

<VirtualHost *:8080>
    ServerName localhost

    ProxyPreserveHost On
    ProxyPass / balancer://laravel_cluster/
    ProxyPassReverse / balancer://laravel_cluster/
</VirtualHost>
```

# Demo

## Tạo 2 vhost cho 2 backend:

```swift
/etc/apache2/sites-available/backend1.conf → DocumentRoot /var/www/laravel-app1/public
/etc/apache2/sites-available/backend2.conf → DocumentRoot /var/www/laravel-app2/public
```

Enable 2 site: `sudo a2ensite backend1.conf backend2.conf`

## Thêm port vào `ports.conf` nếu chưa có:
đường dẫn `/etc/apache2/ports.conf`
```yml
Listen 9001
Listen 9002
```

## Tạo Load Balancer vhost:

```sh
/etc/apache2/sites-available/laravel-lb.conf
```

```apache
<Proxy "balancer://laravel_cluster">
    BalancerMember http://127.0.0.1:9001
    BalancerMember http://127.0.0.1:9002
</Proxy>

<VirtualHost *:80>
    ServerName laravel.local
    ProxyPreserveHost On
    ProxyPass / balancer://laravel_cluster/
    ProxyPassReverse / balancer://laravel_cluster/
</VirtualHost>
```

truy cập `http://laravel.local`, Apache tự phân phối request giữa 2 backend.