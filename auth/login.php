<?php
include '../config.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$error = ""; 

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if($user && password_verify($pass, $user['password'])){
        
        if($user['is_verified'] == 0){
            $new_otp = rand(100000, 999999);
            mysqli_query($conn, "UPDATE users SET otp = '$new_otp' WHERE email = '$email'");

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'masum688823@gmail.com'; 
                $mail->Password   = 'qpcm gmol tydu rqed';   
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('masum688823@gmail.com', 'CampusConnect');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Account Verification - CampusConnect';
                $mail->Body    = "Your verification OTP is: <b>$new_otp</b>";
                $mail->send();

                $_SESSION['temp_email'] = $email;
                echo "<script>alert('Account not verified. A new OTP has been sent.'); window.location='verify_otp.php';</script>";
                exit();
            } catch (Exception $e) {
                $error = "Verification needed, but email failed to send.";
            }
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['dept'] = $user['dept'];

        if($user['role'] == 'admin'){
            header("Location: ../admin/index.php");
        } else {
            header("Location: ../user/dashboard.php");
        }
        exit();

    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        body { background: var(--primary-gradient); min-height: 100vh; display: flex; align-items: center; font-family: 'Plus Jakarta Sans', sans-serif; }
        .login-card { background: rgba(255, 255, 255, 0.95); border-radius: 20px; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.2); }
        .brand-logo { font-size: 2.5rem; color: #764ba2; text-align: center; }
        .form-control { border-radius: 10px; padding: 12px 15px; background: #f8f9fa; border: 1px solid #eee; }
        .input-group-text { background: #f8f9fa; border: 1px solid #eee; border-radius: 10px 0 0 10px; color: #764ba2; }
        .btn-login { background: var(--primary-gradient); border: none; border-radius: 10px; padding: 12px; font-weight: 600; transition: 0.3s; }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(118, 75, 162, 0.4); }
        .fade-in { animation: fadeIn 0.8s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4 fade-in">
            <div class="card login-card p-4">
                <div class="brand-logo mb-2"><i class="bi bi-connectdevelop"></i></div>
                <h3 class="text-center fw-bold text-dark mb-4">Welcome Back</h3>

                <?php if($error != ""): ?>
                    <div class="alert alert-danger py-2 small text-center"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="name@university.com" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label class="form-label small fw-bold text-muted">Password</label>
                            <a href="forgot_password.php" class="small text-decoration-none" style="color: #764ba2;">Forgot?</a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button name="login" class="btn btn-primary btn-login w-100 text-white shadow-sm mb-3">
                        SIGN IN
                    </button>
                </form>

                <div class="text-center mt-3">
                    <p class="small text-muted">Don't have an account? <a href="register.php" class="text-decoration-none fw-bold" style="color: #764ba2;">Join Now</a></p>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="../index.php" class="text-white text-decoration-none small"><i class="bi bi-arrow-left me-1"></i> Back to Home</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>