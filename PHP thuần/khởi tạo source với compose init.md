# Khởi tạo source với compose init

## Chạy web sau khi code xong

```sh
php -S localhost:8000 -t public
```

## khởi tạo composer

```sh
composer init
```

sửa lại thông tin source trong file `composer.json`

```json
{
  "autoload": {
    "psr-4": {
      "App\\": "src/"
    }
  }
}
```

chạy lại lệnh để  cập nhật

```sh
composer dump-autoload
```

# Cấu trúc source

có thể  set up như framework laravel hoặc cakephp tùy vào sở thích nhưng cần chú ý một số thứ bên dưới

```
project/
├── public/
│   └── index.php
├── src/
│   ├── Controllers/
│   │   ├── StudentController.php
│   │   └── CourseController.php
│   │
│   ├── Repositories/
│   │   ├── StudentRepository.php
│   │   └── CourseRepository.php
│   │
│   ├── Core/
│   │   ├── Router.php
│   │   └── DB.php
│   │
│   └── Helpers/
│       └── response.php
│
├── routes/
│   └── web.php
│
├── .env
└── composer.json
```

## Route

```php
// routes/web.php
$router->get('/users', [UserController::class, 'index']);
$router->post('/users', [UserController::class, 'store']);
```

code khởi tạo route

```php
<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get($uri, $action)
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function post($uri, $action)
    {
        $this->routes['POST'][$uri] = $action;
    }

    public function put($uri, $action)
    {
        $this->routes['PUT'][$uri] = $action;
    }

    public function delete($uri, $action)
    {
        $this->routes['DELETE'][$uri] = $action;
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
        $action = $this->routes[$method][$uri] ?? null;
        if (!$action) {
            http_response_code(404);
            exit("404");
        }
        [$controller, $func] = $action;
        call_user_func([new $controller,$func]);
    }
}
```

## DB.php

```php
<?php

namespace App\Core;

use PDO;

class DB
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (!self::$pdo) {

            self::$pdo = new PDO(
                "mysql:host=localhost;dbname=test",
                "root",
                ""
            );
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }
        return self::$pdo;
    }
}
```

## Helper

```php
<?php

function jsonResponse($data, int $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
```

## public

```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Helpers/response.php';
use App\Core\Router;
$router = new Router();
require_once __DIR__ . '/../routes/web.php';
$router->dispatch();
```

## Ví dụ dùng các syntax trên để code

```php
<?php

namespace App\Repositories;

use App\Core\DB;
use PDO;

class CourseRepository
{
    public function all()
    {
        $stmt = DB::connect()->query("SELECT * FROM courses");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($title)
    {
        $stmt = DB::connect()->prepare("
            INSERT INTO courses(title)
            VALUES(:title)
        ");
        $stmt->execute(['title' => $title]);
    }
}
```


```php
<?php

namespace App\Controllers;

use App\Repositories\CourseRepository;

class CourseController
{
    private CourseRepository $repo;

    public function __construct()
    {
        $this->repo = new CourseRepository();
    }

    public function index()
    {
        jsonResponse($this->repo->all());
    }

    public function store()
    {
        $data = json_decode(file_get_contents("php://input"),true);
        $this->repo->create($data['title']);
        jsonResponse(['message' => 'created']);
    }
}
```

