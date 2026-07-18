<?php
include '../config.php';

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

    $otp = rand(100000, 999999);

    $query = "INSERT INTO users (full_name, university_id, email, password, role, dept, otp, is_verified) 
              VALUES ('$name', '$u_id', '$email', '$pass', '$role', '$dept', '$otp', 0)";
    
    if(mysqli_query($conn, $query)){
        
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
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Account Verification - CampusConnect';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd;'>
                    <h2 style='color: #0d6efd;'>Welcome to CampusConnect!</h2>
                    <p>Hello <strong>$name</strong>,</p>
                    <p>Thank you for registering. To activate your account, please use the following One-Time Password (OTP):</p>
                    <h1 style='background: #f4f4f4; padding: 10px; text-align: center; letter-spacing: 5px; color: #333;'>$otp</h1>
                    <p>This code is valid for a limited time. Do not share this code with anyone.</p>
                    <br>
                    <p>Best Regards,<br>CampusConnect Team</p>
                </div>
            ";

            $mail->send();

            session_start();
            $_SESSION['temp_email'] = $email;
            header("Location: verify_otp.php");
            exit();

        } catch (Exception $e) {
            mysqli_query($conn, "DELETE FROM users WHERE email='$email'");
            echo "<script>alert('Failed to send verification email. Please check your internet or email settings.');</script>";
        }

    } else {
        echo "<div class='alert alert-danger text-center'>Error: ID or Email already exists!</div>";
    }
}   
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; }
        .register-card { max-width: 500px; margin: 50px auto; border: none; border-radius: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card register-card shadow p-4">
            <h3 class="text-center mb-4 text-primary fw-bold">CampusConnect Registration</h3>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter Full Name" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Student/Teacher ID</label>
                        <input type="text" name="u_id" class="form-control" placeholder="ID (e.g. 21020xx)" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-control">
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="alumni">Alumni</option> 
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Department</label>
                    <select name="dept" class="form-control" required>
                        <option value="">Select Department</option>
                        <option value="CSE">CSE</option>
                        <option value="EEE">EEE</option>
                        <option value="BBA">BBA</option>
                        <option value="English">English</option>
                        <option value="Civil">Civil</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="University Email" required>
                    <div class="form-text">We will send a 6-digit OTP to this email.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Create Password" required minlength="6">
                </div>

                <button name="register" class="btn btn-primary w-100 shadow-sm fw-bold">Send Verification Code</button>
            </form>
            <p class="mt-3 text-center small">Already have an account? <a href="login.php" class="text-decoration-none">Login Here</a></p>
        </div>
    </div>
</body>
</html>