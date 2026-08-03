<?php
include '../config.php';
session_start();

if(isset($_GET['id']) && isset($_GET['link'])){
    $notif_id = $_GET['id'];
    $redirect_link = $_GET['link'];

    // নোটিফিকেশনটি read হিসেবে আপডেট করা
    mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE id = '$notif_id'");

    // টার্গেট পেজে পাঠিয়ে দেওয়া
    header("Location: " . $redirect_link);
    exit();
}
header("Location: dashboard.php");
?>