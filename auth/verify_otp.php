<?php
include '../config.php';
session_start();

if(!isset($_SESSION['temp_email'])){
    header("Location: register.php");
    exit();
}

if(isset($_POST['verify'])){
    $user_otp = $_POST['otp'];
    $email = $_SESSION['temp_email'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND otp='$user_otp'");
    
    if(mysqli_num_rows($query) > 0){
        mysqli_query($conn, "UPDATE users SET is_verified = 1, otp = NULL WHERE email='$email'");
        unset($_SESSION['temp_email']);
        echo "<script>alert('Account Verified! You can now login.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Invalid OTP! Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4 card p-4 shadow border-0" style="border-radius: 15px;">
                <h4 class="text-center text-primary mb-3">Email Verification</h4>
                <p class="text-center text-muted small">An OTP has been sent to <?php echo $_SESSION['temp_email']; ?></p>
                <form method="POST">
                    <input type="text" name="otp" class="form-control mb-3 text-center fs-4" placeholder="Enter 6-digit OTP" required maxlength="6">
                    <button name="verify" class="btn btn-success w-100 fw-bold">Verify Account</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>