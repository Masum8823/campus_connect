<?php
include '../config.php'; // Back to root for config
session_start();

if(isset($_GET['id']) && isset($_SESSION['user_id'])){
    $post_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Delete post only if it belongs to the logged-in user
    $query = "DELETE FROM posts WHERE id='$post_id' AND user_id='$user_id'";
    
    if(mysqli_query($conn, $query)){
        // Success: Go to dashboard in user folder
        header("Location: ../user/dashboard.php");
        exit();
    } else {
        echo "Error deleting post.";
    }
}
?>