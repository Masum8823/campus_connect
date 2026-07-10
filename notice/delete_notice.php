<?php
include '../config.php';
session_start();

if(isset($_GET['id']) && $_SESSION['role'] != 'student'){
    $notice_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Delete notice only if it belongs to the current teacher
    $query = "DELETE FROM notices WHERE id='$notice_id' AND user_id='$user_id'";
    
    if(mysqli_query($conn, $query)){
        header("Location: ../user/dashboard.php");
        exit();
    } else {
        echo "Error deleting notice.";
    }
}
?>