<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: dashboard.php");
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_GET['id'];

$check_query = mysqli_query($conn, "SELECT * FROM connections WHERE (sender_id='$sender_id' AND receiver_id='$receiver_id') OR (sender_id='$receiver_id' AND receiver_id='$sender_id')");
$connection = mysqli_fetch_assoc($check_query);

if(!$connection){
    mysqli_query($conn, "INSERT INTO connections (sender_id, receiver_id, status) VALUES ('$sender_id', '$receiver_id', 'pending')");
} else {
    mysqli_query($conn, "DELETE FROM connections WHERE id='".$connection['id']."'");
}

header("Location: profile.php?id=" . $receiver_id);
exit();
?>