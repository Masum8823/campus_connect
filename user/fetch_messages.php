<?php
include '../config.php';
session_start();

if(isset($_GET['conv_id']) && isset($_SESSION['user_id'])){
    $conv_id = $_GET['conv_id'];
    $my_id = $_SESSION['user_id'];

    $query = mysqli_query($conn, "SELECT * FROM private_messages WHERE conversation_id='$conv_id' ORDER BY created_at ASC");

    while($msg = mysqli_fetch_assoc($query)){
        $class = ($msg['sender_id'] == $my_id) ? 'sent' : 'received';
        echo '<div class="message ' . $class . '">' . htmlspecialchars($msg['message_text']) . '</div>';
    }
}
?>