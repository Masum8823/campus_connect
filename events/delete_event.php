<?php
include '../config.php';
session_start();

if(isset($_GET['id']) && isset($_SESSION['user_id'])){
    $id = $_GET['id'];
    $u_id = $_SESSION['user_id'];

    // মালিকানা চেক
    $check = mysqli_query($conn, "SELECT organizer_id FROM events WHERE id='$id'");
    $res = mysqli_fetch_assoc($check);

    if($res['organizer_id'] == $u_id || $_SESSION['role'] == 'admin'){
        mysqli_query($conn, "DELETE FROM events WHERE id='$id'");
    }
}
header("Location: index.php");
exit();