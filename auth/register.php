<?php
include '../config.php';

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if(isset($_POST['register'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $u_id = mysqli_real_escape_string($conn, $_POST['u_id']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $dept = $_POST['dept'];

    // Generate 6-digit OTP
    $otp = rand(100000, 999999);

    // Save to DB with is_verified = 0
    $query = "INSERT INTO users (full_name, university_id, email, password, role, dept, otp, is_verified) 
              VALUES ('$name', '$u_id', '$email', '$pass', '$role', '$dept', '$otp', 0)";
    
    if(mysqli_query($conn, $query)){
        $mail = new PHPMailer(true);
        try {
            // Server settings using your provided credentials
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'masum688823@gmail.com'; 
            $mail->Password   = 'qpcm gmol tydu rqed';   
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('masum688823@gmail.com', 'CampusConnect');
            $mail->addAddress($email, $name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Account Verification - CampusConnect';
            $mail->Body    = "
                <div style='font-family: Segoe UI, Tahoma, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 500px;'>
                    <h2 style='color: #764ba2;'>Welcome to CampusConnect!</h2>
                    <p>Hello <strong>$name</strong>,</p>
                    <p>Thank you for joining our community. To complete your registration, please use the OTP code below:</p>
                    <div style='background: #f8f9fa; padding: 15px; text-align: center; border-radius: 8px;'>
                        <h1 style='letter-spacing: 8px; color: #333; margin: 0;'>$otp</h1>
                    </div>
                    <p style='margin-top: 20px; font-size: 13px; color: #777;'>If you did not request this, please ignore this email.</p>
                </div>
            ";

            $mail->send();

            session_start();
            $_SESSION['temp_email'] = $email;
            header("Location: verify_otp.php");
            exit();

        } catch (Exception $e) {
            mysqli_query($conn, "DELETE FROM users WHERE email='$email'");
            $error = "Failed to send email. Error: " . $mail->ErrorInfo;
        }
    } else {
        $error = "Error: ID or Email already exists!";
    }
}   
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CampusConnect</title>
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
            padding: 50px 0;
        }
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            border: none;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .brand-logo {
            font-size: 2.2rem;
            color: #764ba2;
            text-align: center;
            margin-bottom: 10px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 11px 15px;
            background: #f8f9fa;
            border: 1px solid #eee;
            font-size: 14px;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(118, 75, 162, 0.1);
            border-color: #764ba2;
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
        .btn-register {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: 0.3s;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(118, 75, 162, 0.4);
        }
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
        <div class="col-md-8 col-lg-6 fade-in">
            <div class="card register-card p-4">
                <div class="brand-logo">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h3 class="text-center fw-bold text-dark mb-4">Join CampusConnect</h3>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger py-2 small text-center" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <!-- Full Name -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" class="form-control form-control-with-icon" placeholder="Enter your full name" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Student/Teacher ID -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">ID Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-card-heading"></i></span>
                                <input type="text" name="u_id" class="form-control form-control-with-icon" placeholder="e.g. 21020xx" required>
                            </div>
                        </div>
                        <!-- Role -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">Role</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-people"></i></span>
                                <select name="role" class="form-select form-control-with-icon">
                                    <option value="student">Student</option>
                                    <option value="teacher">Teacher</option>
                                    <option value="alumni">Alumni</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Department -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Department</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-building"></i></span>
                            <select name="dept" class="form-select form-control-with-icon" required>
                                <option value="" selected disabled>Select Department</option>
                                <option value="CSE">CSE</option>
                                <option value="EEE">EEE</option>
                                <option value="BBA">BBA</option>
                                <option value="English">English</option>
                                <option value="Civil">Civil</option>
                            </select>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">University Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control form-control-with-icon" placeholder="yourname@university.com" required>
                        </div>
                        <div class="form-text small text-muted">Verification OTP will be sent here.</div>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control form-control-with-icon" placeholder="••••••••" required minlength="6">
                        </div>
                    </div>

                    <button name="register" class="btn btn-primary btn-register w-100 text-white shadow-sm mb-3">
                        SEND VERIFICATION CODE
                    </button>
                </form>

                <div class="text-center mt-2">
                    <p class="small text-muted">Already have an account? 
                        <a href="login.php" class="text-decoration-none fw-bold" style="color: #764ba2;">Login Here</a>
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