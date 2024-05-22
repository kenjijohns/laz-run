<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    header("Location: index.php");
    exit();
}
    $host="localhost";
    $user="root";
    $pass="";
    $dbname="quiz"; 

    $conn = mysqli_connect($host, $user, $pass, $dbname);