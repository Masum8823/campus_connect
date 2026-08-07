<?php
include '../config.php';
// config.php-তে সেশন অলরেডি স্টার্ট করা আছে, তাই এখানে আর দরকার নেই

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: jobs.php");
    exit();
}

$job_id = mysqli_real_escape_string($conn, $_GET['id']);
$current_user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// ১. চেক করা: এই পোস্টটি ইউজারের নিজের কি না অথবা ইউজার অ্যাডমিন কি না
$check_query = mysqli_query($conn, "SELECT alumni_id FROM alumni_jobs WHERE id='$job_id'");
$job_data = mysqli_fetch_assoc($check_query);

if($job_data && ($job_data['alumni_id'] == $current_user_id || $user_role == 'admin')){
    
    // ২. ডিলিট কুয়েরি রান করা
    $sql = "DELETE FROM alumni_jobs WHERE id='$job_id'";
    
    if(mysqli_query($conn, $sql)){
        // সফল হলে মেসেজসহ ফেরত পাঠানো
        header("Location: jobs.php?msg=deleted");
        exit();
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }
} else {
    // পারমিশন না থাকলে
    echo "<script>alert('Unauthorized Action!'); window.location='jobs.php';</script>";
}
?>