<?php
//testing database connection file
$conn = mysqli_connect('127.0.0.1', 'root', '', 'kon3ctados', '3306');

//check the connection
if (!$conn) {
    die('database error! ' . mysqli_connect_error());
}

//echo 'successful database connection';