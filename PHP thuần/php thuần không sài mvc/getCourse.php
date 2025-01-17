<?php

$connect = mysqli_connect('localhost', 'root', '', 'course_cakephp');
$connect->set_charset('utf8');

$courses = $connect->query('SELECT * FROM courses')->fetch_all();

$connect->close();