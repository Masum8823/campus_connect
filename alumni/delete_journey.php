<?php
include '../config.php';
session_start();

if(isset($_GET['id']) && isset($_SESSION['user_id'])){
    $id = $_GET['id'];
    $u_id = $_SESSION['user_id'];
    mysqli_query($conn, "DELETE FROM alumni_stories WHERE id='$id' AND user_id='$u_id'");
}
header("Location: stories.php");
exit();