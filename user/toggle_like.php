<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['post_id'])){
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = $_GET['post_id'];

$check_query = mysqli_query($conn, "SELECT * FROM likes WHERE post_id='$post_id' AND user_id='$user_id'");

if(mysqli_num_rows($check_query) > 0){
    mysqli_query($conn, "DELETE FROM likes WHERE post_id='$post_id' AND user_id='$user_id'");
} else {
    mysqli_query($conn, "INSERT INTO likes (post_id, user_id) VALUES ('$post_id', '$user_id')");
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>