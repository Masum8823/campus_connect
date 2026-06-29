<?php
include 'config.php';
session_start();

if(isset($_GET['id']) && isset($_SESSION['user_id'])){
    $post_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $query = "DELETE FROM posts WHERE id='$post_id' AND user_id='$user_id'";
    
    if(mysqli_query($conn, $query)){
        header("Location: dashboard.php");
    } else {
        echo "Error deleting post.";
    }
}
?>