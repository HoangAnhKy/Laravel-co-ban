<?php
$title = "create course";
ob_start(); ?>
<h1> create </h1>

<form method="POST">
    <input name="name" class="form-control" placeholder="name course" />
    <button class="btn btn-success mt-2 form-control">submid</button>
</form>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__ ."/../layout/default.php"?>