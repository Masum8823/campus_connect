<?php
include '../config.php';

if(!isset($_SESSION['user_id']) || !isset($_GET['conv_id'])){
    header("Location: messages.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$conv_id = mysqli_real_escape_string($conn, $_GET['conv_id']);

// ১. আগে নিশ্চিত হওয়া এই চ্যাটটি ইউজারের কি না
$check = mysqli_query($conn, "SELECT * FROM conversations WHERE id='$conv_id' AND (user1_id='$current_user_id' OR user2_id='$current_user_id')");
$conv_data = mysqli_fetch_assoc($check);

if($conv_data){
    $u1 = $conv_data['user1_id'];
    $u2 = $conv_data['user2_id'];

    // ২. কনভারসেশন ডিলিট (এখন SQL CASCADE এর কারণে মেসেজগুলো অটো ডিলিট হবে)
    mysqli_query($conn, "DELETE FROM conversations WHERE id='$conv_id'");

    // ৩. মেসেজ রিকোয়েস্ট স্ট্যাটাস ডিলিট বা রিসেট করা
    // যাতে ভবিষ্যতে আবার মেসেজ রিকোয়েস্ট পাঠানো যায়
    mysqli_query($conn, "DELETE FROM message_requests WHERE (sender_id='$u1' AND receiver_id='$u2') OR (sender_id='$u2' AND receiver_id='$u1')");

    header("Location: messages.php?status=deleted");
    exit();
} else {
    header("Location: messages.php?status=error");
}
?>