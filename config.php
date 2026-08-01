<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "campus_connect";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// ১. ইউজার লগইন থাকলে তার লাস্ট অ্যাক্টিভিটি টাইম আপডেট করা
if (session_status() === PHP_SESSION_NONE) { session_start(); } // সেশন চেক

if(isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    mysqli_query($conn, "UPDATE users SET last_activity = NOW() WHERE id = '$uid'");
}

// ২. সময়কে "Time Ago" ফরম্যাটে দেখানোর একটি হেল্পার ফাংশন
function getTimeAgo($timestamp) {
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    
    $minutes = round($seconds / 60);       // value 60 is seconds
    $hours   = round($seconds / 3600);      // value 3600 is 60 minutes * 60 sec
    $days    = round($seconds / 86400);     // value 86400 is 24 hours * 60 min * 60 sec

    if ($seconds <= 60) return "Just now";
    else if ($minutes <= 60) return ($minutes == 1) ? "1 min ago" : "$minutes mins ago";
    else if ($hours <= 24) return ($hours == 1) ? "an hour ago" : "$hours hours ago";
    else return ($days == 1) ? "yesterday" : "$days days ago";
}
?>

