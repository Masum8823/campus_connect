<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$story_id = $_GET['id'];

$check = mysqli_query($conn, "SELECT * FROM alumni_inspired WHERE story_id='$story_id' AND user_id='$user_id'");

if(mysqli_num_rows($check) > 0){
    mysqli_query($conn, "DELETE FROM alumni_inspired WHERE story_id='$story_id' AND user_id='$user_id'");
} else {
    mysqli_query($conn, "INSERT INTO alumni_inspired (story_id, user_id) VALUES ('$story_id', '$user_id')");
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>