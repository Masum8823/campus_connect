<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php"); exit();
}

$id = $_GET['id'];
$u_id = $_SESSION['user_id'];

// চেক করা: ইউজার কি আসলেও ওই আইটেমের মালিক?
$check = mysqli_query($conn, "SELECT status FROM marketplace_items WHERE id='$id' AND seller_id='$u_id'");
$item = mysqli_fetch_assoc($check);

if($item){
    $new_status = ($item['status'] == 'available') ? 'sold' : 'available';
    mysqli_query($conn, "UPDATE marketplace_items SET status='$new_status' WHERE id='$id'");
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();