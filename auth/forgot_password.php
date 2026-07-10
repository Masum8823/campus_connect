<?php
include '../config.php';
session_start();

if(isset($_POST['verify'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $u_id = mysqli_real_escape_string($conn, $_POST['u_id']);

    // Check if email and university ID matches in database
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND university_id='$u_id'");
    
    if(mysqli_num_rows($query) > 0){
        $user = mysqli_fetch_assoc($query);
        $_SESSION['reset_user_id'] = $user['id']; // Temporarily store user ID for reset
        header("Location: reset_password.php");
    } else {
        echo "<script>alert('Identity Verification Failed! Email or ID is incorrect.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4 card p-4 shadow border-0">
                <h4 class="text-center text-primary mb-3">Identity Verification</h4>
                <p class="text-muted small">Please enter your details to reset password.</p>
                <form method="POST">
                    <input type="email" name="email" class="form-control mb-3" placeholder="Registered Email" required>
                    <input type="text" name="u_id" class="form-control mb-3" placeholder="University ID (e.g. 21020xx)" required>
                    <button name="verify" class="btn btn-primary w-100 fw-bold">Verify Identity</button>
                </form>
                <a href="login.php" class="text-center d-block mt-3 text-decoration-none">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>