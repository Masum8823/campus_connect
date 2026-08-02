<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php"); exit();
}

$event_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$status = $_GET['status'];

if($status == 'remove'){
    mysqli_query($conn, "DELETE FROM event_participations WHERE event_id='$event_id' AND user_id='$user_id'");
} else {
    // আগের কোনো এন্ট্রি থাকলে ডিলিট করে নতুন স্ট্যাটাস ইনসার্ট করো
    mysqli_query($conn, "DELETE FROM event_participations WHERE event_id='$event_id' AND user_id='$user_id'");
    mysqli_query($conn, "INSERT INTO event_participations (event_id, user_id, status) VALUES ('$event_id', '$user_id', '$status')");
}

header("Location: view_event.php?id=" . $event_id);
exit();