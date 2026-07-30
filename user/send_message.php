<?php
include '../config.php';
session_start();

if(isset($_POST['conv_id']) && isset($_SESSION['user_id'])){
    $conv_id = $_POST['conv_id'];
    $sender_id = $_SESSION['user_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $conv_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user1_id, user2_id FROM conversations WHERE id='$conv_id'"));
    $receiver_id = ($conv_info['user1_id'] == $sender_id) ? $conv_info['user2_id'] : $conv_info['user1_id'];

    $block_check = mysqli_query($conn, "SELECT * FROM message_blocks WHERE (blocker_id='$sender_id' AND blocked_id='$receiver_id') OR (blocker_id='$receiver_id' AND blocked_id='$sender_id')");

    if(mysqli_num_rows($block_check) > 0){
        exit();
    }

    if(!empty($message)){
        mysqli_query($conn, "INSERT INTO private_messages (conversation_id, sender_id, message_text) VALUES ('$conv_id', '$sender_id', '$message')");
    }
}
?>