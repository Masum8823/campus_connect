<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: dashboard.php");
    exit();
}

$blocker_id = $_SESSION['user_id'];
$blocked_id = $_GET['id'];

$check = mysqli_query($conn, "SELECT * FROM message_blocks WHERE blocker_id='$blocker_id' AND blocked_id='$blocked_id'");

if(mysqli_num_rows($check) > 0){
    mysqli_query($conn, "DELETE FROM message_blocks WHERE blocker_id='$blocker_id' AND blocked_id='$blocked_id'");
    $msg = "unblocked";
} else {
    mysqli_query($conn, "INSERT INTO message_blocks (blocker_id, blocked_id) VALUES ('$blocker_id', '$blocked_id')");
    
    mysqli_query($conn, "DELETE FROM message_requests WHERE (sender_id='$blocker_id' AND receiver_id='$blocked_id') OR (sender_id='$blocked_id' AND receiver_id='$blocker_id')");
    
    $msg = "blocked";
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>