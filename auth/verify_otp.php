<?php
include '../config.php';
session_start();

if(!isset($_SESSION['temp_email'])){
    header("Location: register.php");
    exit();
}

if(isset($_POST['verify'])){
    $user_otp = mysqli_real_escape_string($conn, $_POST['otp']);
    $email = $_SESSION['temp_email'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND otp='$user_otp'");
    
    if(mysqli_num_rows($result) > 0){
        $user_data = mysqli_fetch_assoc($result);

        mysqli_query($conn, "UPDATE users SET is_verified = 1, otp = NULL WHERE email='$email'");

        if(isset($_SESSION['otp_purpose']) && $_SESSION['otp_purpose'] == 'reset'){
            $_SESSION['reset_user_id'] = $user_data['id']; 
            unset($_SESSION['otp_purpose']); 
            header("Location: reset_password.php");
            exit();
        } else {
            unset($_SESSION['temp_email']);
            echo "<script>alert('Account Verified Successfully!'); window.location='login.php';</script>";
            exit();
        }
    } else {
        $error = "Invalid OTP code! Please check your email again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        body {
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }
        .verify-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            border: none;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            padding: 40px 30px;
        }
        .icon-box {
            width: 70px;
            height: 70px;
            background: #eef2ff;
            color: #764ba2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto 20px;
        }
        .otp-input {
            letter-spacing: 15px;
            font-size: 28px;
            font-weight: bold;
            text-align: center;
            border-radius: 12px;
            background: #f8f9fa;
            border: 2px solid #eee;
            padding: 10px;
        }
        .otp-input:focus {
            box-shadow: 0 0 0 0.25rem rgba(118, 75, 162, 0.1);
            border-color: #764ba2;
        }
        .btn-verify {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(118, 75, 162, 0.4);
        }
        .fade-in { animation: fadeIn 0.8s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4 fade-in">
            <div class="card verify-card text-center">
                <div class="icon-box">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h3 class="fw-bold text-dark">Verification</h3>
                <p class="text-muted small">
                    <?php if(isset($_SESSION['otp_purpose']) && $_SESSION['otp_purpose'] == 'reset'): ?>
                        Enter the reset code sent to your email <br>
                    <?php else: ?>
                        We've sent a 6-digit activation code to <br>
                    <?php endif; ?>
                    <strong><?php echo $_SESSION['temp_email']; ?></strong>
                </p>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger py-2 small mb-3"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-4">
                        <input type="text" name="otp" class="form-control otp-input" placeholder="000000" required maxlength="6" autocomplete="off">
                        <div class="form-text mt-3">Check your email inbox or spam folder.</div>
                    </div>

                    <button name="verify" class="btn btn-primary btn-verify w-100 text-white shadow-sm mb-3 uppercase">
                        <?php echo (isset($_SESSION['otp_purpose']) && $_SESSION['otp_purpose'] == 'reset') ? 'Verify to Reset' : 'Verify & Activate'; ?>
                    </button>
                </form>

                <div class="mt-2">
                    <p class="small text-muted">Didn't get the code? 
                        <a href="login.php" class="text-decoration-none fw-bold" style="color: #764ba2;">Try Again</a>
                    </p>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="../index.php" class="text-white text-decoration-none small">
                    <i class="bi bi-arrow-left me-1"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>