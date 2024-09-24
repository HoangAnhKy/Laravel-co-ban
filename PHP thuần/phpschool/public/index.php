<?php

require __DIR__ . '/../config/define.php';
// get url and convert string to array 
$url = $_GET['url'] ?? "";
$url = rtrim($url, "/");
$url = explode("/", $url);

$name_controller = (isset($url[0]) && $url[0]) ? $url[0] . "Controller" : DEFAULT_CONTROLLER;
$name_action =  (isset($url[1]) && $url[1]) ? $url[1] : DEFAULT_METHOD;
// check slug after controller and action
$slug =  count($url) > 2 ? array_slice($url, 2) : [];

$dir = __DIR__ . "/../app/controller/" . $name_controller . ".php";
if (file_exists($dir)){
    require $dir;

    $controller = new $name_controller();

    if (method_exists($controller, $name_action)){
        call_user_func_array([$controller, $name_action], $slug);
    } else {
        echo "Method not found.";
    }
} else {
    echo "Controller not found.";
}