<?php

require_once "../config/define.php";

// Lấy URL từ query string
$url = isset($_GET['url']) ? $_GET['url'] : null;
$url = rtrim($url, '/');
$url = explode('/', $url);

// Phân tích URL và gọi controller tương ứng
$controllerName = isset($url[0]) && $url[0] ? ucfirst($url[0]) . 'Controller' : 'UsersController';
$methodName = isset($url[1]) && $url[1] ? $url[1] : 'index';

$dir =  __DIR__ . "/../app/controller/" . $controllerName . ".php";

if (file_exists($dir)) {
    require $dir;
    $controllerName = '\\controller\\'.$controllerName;
    $controller = new $controllerName();

    // Kiểm tra nếu phương thức tồn tại
    if (method_exists($controller, $methodName)) {
        $controller->{$methodName}();
    } else {
        echo "Method not found.";
    }
} else {
    echo "Controller not found.";
}
