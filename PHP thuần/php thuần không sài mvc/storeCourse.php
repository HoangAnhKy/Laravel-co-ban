<?php

$name = $_POST['name'];


$connect = mysqli_connect('localhost', 'root', '', 'course_cakephp');
$connect->set_charset('utf8');

$execute = $connect->query("INSERT INTO courses (name) VALUES ('$name')");
if (!$execute) {
    echo "Lỗi khi thực hiện truy vấn: " . $connect->error;
    die;
} else {
    echo "Thêm dữ liệu thành công!";
}

$connect->close();