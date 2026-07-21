<?php
include '../config.php';
session_start();

// Security: If identity is not verified, redirect back
if(!isset($_SESSION['reset_user_id'])){
    header("Location: forgot_password.php");
    exit();
}

if(isset($_POST['reset'])){
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if($pass !== $confirm_pass){
        $error = "Passwords do not match!";
    } elseif(strlen($pass) < 6){
        $error = "Password must be at least 6 characters long.";
    } else {
        $new_pass_hashed = password_hash($pass, PASSWORD_DEFAULT);
        $user_id = $_SESSION['reset_user_id'];

        // Update password and clear any existing OTP
        $update = mysqli_query($conn, "UPDATE users SET password='$new_pass_hashed', otp=NULL WHERE id='$user_id'");

        if($update){
            unset($_SESSION['reset_user_id']); // Clear temporary session
            echo "<script>alert('Password Reset Successful! Please login with your new password.'); window.location='login.php';</script>";
            exit();
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - CampusConnect</title>
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
        .reset-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            border: none;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            padding: 40px 30px;
        }
        .icon-box {
            width: 70px;
            height: 70px;
            background: #f0fdf4;
            color: #198754;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto 20px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            background: #f8f9fa;
            border: 1px solid #eee;
        }
        .input-group-text {
            background: #f8f9fa;
            border: 1px solid #eee;
            border-radius: 10px 0 0 10px;
            color: #764ba2;
        }
        .form-control-with-icon {
            border-radius: 0 10px 10px 0;
        }
        .btn-reset {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-reset:hover {
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
            <div class="card reset-card">
                <div class="icon-box">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h3 class="text-center fw-bold text-dark">New Password</h3>
                <p class="text-center text-muted small mb-4">Set a strong password to protect your account</p>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger py-2 small text-center" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <!-- New Password -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Create New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password" class="form-control form-control-with-icon" placeholder="••••••••" required minlength="6">
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                            <input type="password" name="confirm_password" class="form-control form-control-with-icon" placeholder="••••••••" required minlength="6">
                        </div>
                    </div>

                    <button name="reset" class="btn btn-primary btn-reset w-100 text-white shadow-sm mb-2">
                        UPDATE PASSWORD
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="login.php" class="text-decoration-none small fw-bold" style="color: #764ba2;">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>