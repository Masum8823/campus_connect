<?php
include '../config.php';
session_start();

if(isset($_POST['conv_id']) && isset($_SESSION['user_id'])){
    $conv_id = $_POST['conv_id'];
    $sender_id = $_SESSION['user_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    if(!empty($message)){
        mysqli_query($conn, "INSERT INTO private_messages (conversation_id, sender_id, message_text) VALUES ('$conv_id', '$sender_id', '$message')");
    }
}
?>