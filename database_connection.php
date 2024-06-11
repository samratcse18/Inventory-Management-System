<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "inventory";

// Create connection
$connect = new mysqli($servername, $username, $password, $database);

// $connect = mysql_connect('localhost', 'rashed', 'rashed');
// mysql_select_db('inventory', $connect);

session_start();
// $type=0;
// $user_id=0;
// $user_name=0;
// $type = $_SESSION['type'];
// $user_id = $_SESSION['user_id'];
// $user_name = $_SESSION['user_name'];
?>
