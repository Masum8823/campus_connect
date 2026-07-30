<?php
include '../config.php';
session_start();
if(isset($_GET['id']) && isset($_SESSION['user_id'])){
    $id = $_GET['id'];
    $u_id = $_SESSION['user_id'];
    mysqli_query($conn, "DELETE FROM private_messages WHERE id='$id' AND sender_id='$u_id'");
}
?>