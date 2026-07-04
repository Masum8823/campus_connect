<?php
include '../config.php';
session_start();

if(isset($_GET['id']) && isset($_SESSION['user_id'])){
    $id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    mysqli_query($conn, "DELETE FROM lost_found WHERE id='$id' AND user_id='$user_id'");
}
header("Location: index.php");
exit();