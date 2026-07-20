<?php
include '../config.php';
session_start();

if(isset($_GET['id']) && isset($_SESSION['user_id'])){
    $c_id = $_GET['id'];
    $u_id = $_SESSION['user_id'];
    mysqli_query($conn, "DELETE FROM comments WHERE id='$c_id' AND user_id='$u_id'");
}
header("Location: dashboard.php");
exit();