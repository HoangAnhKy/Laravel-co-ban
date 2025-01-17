<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<?php
        $id = $_GET['id'];
        echo $id;
        
        $connect = mysqli_connect('localhost', 'root', '', 'course_cakephp');
        $connect->set_charset('utf8');
        
        $execute = $connect->query("SELECT * FROM courses WHERE id = '$id'")->fetch_all();

        if (!$execute) {
            echo "Lỗi khi thực hiện truy vấn: " . $connect->error;
            die;
        } else {
            $course = $execute[0];
        }
    ?>
    <form>
        <label for="courseName">Course Name</label>
        <input type="text" name="name" id="courseName" value="<?= $course[1] ?>">
        <button type="button" id="submid">Submit</button>
    </form>
</body>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
    $(document).ready(function() {
        $('#submid').click(function() {
            var name = $('#courseName').val();
            var id = <?= $id ?? $_GET["id"] ?? $course[0] ?>;
            $.ajax({
                url: 'update.php',
                type: 'POST',
                data: {
                    name: name,
                    id: id
                },
                dataType: 'json', // Đảm bảo nhận JSON từ PHP
                success: function (response) {
                    if (response.success) {
                        alert(response.message); // Thông báo thành công
                        window.location.href = 'index.php';
                    } else {
                        alert("Lỗi: " + response.message); // Thông báo lỗi
                    }
                },
                error: function () {
                    alert("Đã xảy ra lỗi khi kết nối đến máy chủ.");
                }
            });
        });
    });
</script>
</html>