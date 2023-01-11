# khai báo thư mục chứa container

-   vào `app/Providers/AppServiceProvider.php` -> function boot để khai báo đường dẫn đến các file container

```php
    public function boot()
    {
        //
        Blade::anonymousComponentPath(
            app_path('Domain/Container')
        );
    }
```

-   vào view để khai báo container đó

vd

```php
        <x-testcontainer></x-testcontainer> // x-tên file trong thư mục khai báo container
```
