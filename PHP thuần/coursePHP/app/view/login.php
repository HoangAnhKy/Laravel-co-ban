<?php
$title = 'Login';

ob_start();
?>
<!--<form action="--><?php //= BASE_URL . "/users/login"?><!--" method="post">-->
<form id="login">
    <div class="mb-3">
        <label for="email-login" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email-login" name="email">
    </div>
    <div class="mb-3">
        <label for="password-login" class="form-label">Password</label>
        <input type="password" class="form-control" id="password-login" name="password">
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
<?php
$content = ob_get_clean();
ob_start();
?>
<script>
    $(document).ready(function () {
        $("#login").submit(function (e) {
            e.preventDefault();

            $.ajax({
                type: "POST",
                url: "<?= BASE_URL . "/users/login"?>", // Đường dẫn đến file PHP xử lý
                data: {
                    email: $("#email-login").val(),
                    password: $("#password-login").val(),
                },
                dataType: "json", // Kỳ vọng nhận JSON từ server
                encode: true,
                success: function (response) {
                   console.log(response)
                },
                error: function (xhr, status, error) {
                }
            })
        })
    })
</script>
<?php
$scripts = ob_get_clean();

require __DIR__ . "/layout/default.php"
?>

