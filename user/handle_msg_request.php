<?php
include '../config.php';
// সেশন config.php তেই স্টার্ট করা আছে

if(!isset($_SESSION['user_id']) || !isset($_GET['req_id'])){
    header("Location: dashboard.php"); 
    exit();
}

$req_id = $_GET['req_id'];
$action = $_GET['action'];
$current_user_id = $_SESSION['user_id'];

if($action == 'accept'){
    $sender_id = $_GET['sender_id'];
    
    // ১. মেসেজ রিকোয়েস্ট স্ট্যাটাস আপডেট করা
    mysqli_query($conn, "UPDATE message_requests SET status='accepted' WHERE id='$req_id'");

    // ২. কনভারসেশন টেবিল চেক করা এবং না থাকলে তৈরি করা
    $check_conv = mysqli_query($conn, "SELECT * FROM conversations WHERE (user1_id='$current_user_id' AND user2_id='$sender_id') OR (user1_id='$sender_id' AND user2_id='$current_user_id')");
    
    if(mysqli_num_rows($check_conv) == 0){
        mysqli_query($conn, "INSERT INTO conversations (user1_id, user2_id) VALUES ('$current_user_id', '$sender_id')");
    }

    // ৩. স্যারের পরামর্শ অনুযায়ী রিডাইরেক্ট পরিবর্তন: chat.php এর বদলে messages.php তে পাঠানো হলো
    header("Location: messages.php?status=accepted");

} elseif($action == 'decline'){
    // ডিক্লাইন করলে রিকোয়েস্টটি ডিলিট করে দেওয়া
    mysqli_query($conn, "DELETE FROM message_requests WHERE id='$req_id'");
    header("Location: message_requests.php?status=declined");
}
exit();
?>