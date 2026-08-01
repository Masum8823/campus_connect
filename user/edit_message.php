<?php
include '../config.php';
if(isset($_POST['msg_id']) && isset($_SESSION['user_id'])){
    $id = $_POST['msg_id'];
    $u_id = $_SESSION['user_id'];
    $text = mysqli_real_escape_string($conn, $_POST['message']);
    mysqli_query($conn, "UPDATE private_messages SET message_text='$text', is_edited=1 WHERE id='$id' AND sender_id='$u_id'");
}
?>