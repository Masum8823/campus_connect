<?php
include 'config.php';
session_start();

// Security: If identity is not verified, redirect back
if(!isset($_SESSION['reset_user_id'])){
    header("Location: forgot_password.php");
    exit();
}

if(isset($_POST['reset'])){
    $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_id = $_SESSION['reset_user_id'];

    // Update password in database
    $update = mysqli_query($conn, "UPDATE users SET password='$new_pass' WHERE id='$user_id'");

    if($update){
        unset($_SESSION['reset_user_id']); // Clear temporary session
        echo "<script>alert('Password Reset Successful! Please login with your new password.'); window.location='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4 card p-4 shadow border-0">
                <h4 class="text-center text-success mb-3">Set New Password</h4>
                <form method="POST">
                    <input type="password" name="password" class="form-control mb-3" placeholder="Enter New Password" required minlength="6">
                    <button name="reset" class="btn btn-success w-100 fw-bold">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>