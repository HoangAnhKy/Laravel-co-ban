<?php ob_start() ?>
<h1>hello, php school </h1>
<?php $content = ob_get_clean(); ?>
<?php require __DIR__."/layout/default.php"; ?>