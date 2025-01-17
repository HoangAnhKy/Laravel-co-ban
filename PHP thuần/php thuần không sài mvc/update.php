<?php

$name = $_POST['name'];
$id = $_POST['id'];


try{
    $connect = mysqli_connect('localhost', 'root', '', 'course_cakephp');
$connect->set_charset('utf8');

$execute = $connect->query("UPDATE courses SET name = '$name' WHERE id = '$id'");
if (!$execute) {
    throw new Exception("Lỗi chuẩn bị truy vấn: " . $connect->error);

} else {
    echo json_encode([
        "success" => true,
        "message" => "Chỉnh sửa dữ liệu thành công!"
    ]);
}

$connect->close();
}catch(Exception $e){
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}