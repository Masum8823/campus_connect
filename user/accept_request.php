<?php
include '../config.php';
session_start();

if(isset($_GET['id']) && isset($_SESSION['user_id'])){
    $conn_id = $_GET['id'];
    $receiver_id = $_SESSION['user_id'];

    $update = "UPDATE connections SET status='accepted' WHERE id='$conn_id' AND receiver_id='$receiver_id'";
    
    if(mysqli_query($conn, $update)){
        header("Location: requests.php?msg=connected");
        exit();
    }
}
header("Location: requests.php");
?>