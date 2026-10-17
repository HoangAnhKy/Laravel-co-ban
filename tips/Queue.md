- Khi chạy queue bị down liên tục hãy thử 
    ```sh
    php artisan queue:work --memory=1024 --timeout=600 --tries=3
    ```