<?php
include '../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

session_start();

if(isset($_POST['verify'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $u_id = mysqli_real_escape_string($conn, $_POST['u_id']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND university_id='$u_id'");
    
    if(mysqli_num_rows($query) > 0){
        $user = mysqli_fetch_assoc($query);
        
        $otp = rand(100000, 999999);
        mysqli_query($conn, "UPDATE users SET otp = '$otp' WHERE email = '$email'");

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'masum688823@gmail.com'; 
            $mail->Password   = 'qpcm gmol tydu rqed'; // তোমার অ্যাপ পাসওয়ার্ড   
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('masum688823@gmail.com', 'CampusConnect Support');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP - CampusConnect';
            $mail->Body    = "<h3>Password Reset Request</h3>
                              <p>You requested to reset your password. Use the following OTP to proceed:</p>
                              <h1 style='background: #f4f4f4; padding: 10px; text-align: center; letter-spacing: 5px;'>$otp</h1>
                              <p>If you didn't request this, please ignore this email.</p>";

            $mail->send();

            // ৪. সেশনে ইমেইল এবং পারপাস সেট করা
            $_SESSION['temp_email'] = $email;
            $_SESSION['otp_purpose'] = 'reset'; // আমরা verify_otp.php তে এটি চেক করবো
            
            header("Location: verify_otp.php");
            exit();

        } catch (Exception $e) {
            $error = "Failed to send OTP. Please try again.";
        }
    } else {
        $error = "Identity Verification Failed! Email or ID is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        body { background: var(--primary-gradient); min-height: 100vh; display: flex; align-items: center; font-family: 'Inter', sans-serif; }
        .forgot-card { background: rgba(255, 255, 255, 0.95); border-radius: 20px; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.2); }
        .brand-logo { font-size: 2.5rem; color: #764ba2; text-align: center; }
        .form-control { border-radius: 10px; padding: 12px 15px; background: #f8f9fa; border: 1px solid #eee; }
        .input-group-text { background: #f8f9fa; border: 1px solid #eee; border-radius: 10px 0 0 10px; color: #764ba2; }
        .btn-verify { background: var(--primary-gradient); border: none; border-radius: 10px; padding: 12px; font-weight: 600; transition: 0.3s; }
        .btn-verify:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(118, 75, 162, 0.4); }
        .fade-in { animation: fadeIn 0.8s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4 fade-in">
            <div class="card forgot-card p-4">
                <div class="brand-logo mb-2"><i class="bi bi-shield-lock"></i></div>
                <h3 class="text-center fw-bold text-dark mb-2">Find Your Account</h3>
                <p class="text-center text-muted small mb-4">Enter details to receive a reset code</p>

                <?php if(isset($error)): ?>
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

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">University ID</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                            <input type="text" name="u_id" class="form-control" placeholder="e.g. 21020xx" required>
                        </div>
                    </div>

                    <button name="verify" class="btn btn-primary btn-verify w-100 text-white shadow-sm mb-3">
                        SEND RESET CODE
                    </button>
                </form>

                <div class="text-center mt-2">
                    <a href="login.php" class="text-decoration-none fw-bold small" style="color: #764ba2;">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>