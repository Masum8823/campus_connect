<?php
include '../config.php';
session_start();

if(isset($_GET['id']) && isset($_SESSION['user_id'])){
    $receiver_id = $_GET['id'];
    $current_user_id = $_SESSION['user_id'];

    mysqli_query($conn, "DELETE FROM message_requests WHERE sender_id='$current_user_id' AND receiver_id='$receiver_id' AND status='pending'");
}
header("Location: profile.php?id=" . $receiver_id);
exit();