<?php
include '../config.php';
session_start();

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if($user && password_verify($pass, $user['password'])){
        if($user['is_verified'] == 0){
            echo "<script>alert('Please verify your email first!'); window.location='verify_otp.php';</script>";
            $_SESSION['temp_email'] = $email;
            exit();
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['dept'] = $user['dept'];
        
        header("Location: ../user/dashboard.php");
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
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --glass-bg: rgba(255, 255, 255, 0.95);
        }

        body {
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Inter', sans-serif;
        }

        .login-card {
            background: var(--glass-bg);
            border-radius: 20px;
            border: none;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
            transition: 0.3s;
        }

        .card-header-custom {
            background: transparent;
            padding: 30px 20px 10px;
            text-align: center;
            border: none;
        }

        .brand-logo {
            font-size: 2.5rem;
            color: #764ba2;
            margin-bottom: 10px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            background: #f8f9fa;
            border: 1px solid #eee;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(118, 75, 162, 0.1);
            border-color: #764ba2;
        }

        .btn-login {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(118, 75, 162, 0.4);
            opacity: 0.95;
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

        .footer-links a {
            color: #764ba2;
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
        }

        .footer-links a:hover {
            color: #667eea;
            text-decoration: underline;
        }

        /* Animation */
        .fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4 fade-in">
                
                <div class="card login-card p-2">
                    <div class="card-header-custom">
                        <div class="brand-logo">
                            <i class="bi bi-connectdevelop"></i>
                        </div>
                        <h3 class="fw-bold text-dark">Welcome Back</h3>
                        <p class="text-muted small">Login to access your campus community</p>
                    </div>

                    <div class="card-body px-4">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger py-2 small text-center" role="alert">
                                <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <!-- Email Field -->
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control form-control-with-icon" placeholder="name@university.com" required>
                                </div>
                            </div>

                            <!-- Password Field -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label small fw-bold text-muted">Password</label>
                                    <a href="forgot_password.php" class="small text-decoration-none" style="color: #764ba2;">Forgot?</a>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control form-control-with-icon" placeholder="••••••••" required>
                                </div>
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label small text-muted" for="remember">Remember me</label>
                            </div>

                            <!-- Login Button -->
                            <button name="login" class="btn btn-primary btn-login w-100 text-white mb-3">
                                SIGN IN
                            </button>
                        </form>

                        <div class="text-center footer-links mt-4 mb-2">
                            <p class="small text-muted">Don't have an account? 
                                <a href="register.php">Create Account</a>
                            </p>
                        </div>
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