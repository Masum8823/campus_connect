<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: dashboard.php");
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_GET['id'];

// Check if request already exists
$check = mysqli_query($conn, "SELECT * FROM message_requests WHERE (sender_id='$sender_id' AND receiver_id='$receiver_id') OR (sender_id='$receiver_id' AND receiver_id='$sender_id')");

if(mysqli_num_rows($check) == 0){
    // Insert new pending request
    mysqli_query($conn, "INSERT INTO message_requests (sender_id, receiver_id, status) VALUES ('$sender_id', '$receiver_id', 'pending')");
}

header("Location: profile.php?id=$receiver_id");
exit();
?>