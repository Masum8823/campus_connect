<?php
include '../config.php';
session_start();

if(isset($_GET['id']) && isset($_SESSION['user_id'])){
    $job_id = $_GET['id'];
    $u_id = $_SESSION['user_id'];
    mysqli_query($conn, "DELETE FROM alumni_jobs WHERE id='$job_id' AND alumni_id='$u_id'");
}
header("Location: index.php?view=jobs");
exit();