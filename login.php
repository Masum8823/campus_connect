<?php
include 'config.php';
session_start();

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if($user && password_verify($pass, $user['password'])){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['dept'] = $user['dept'];
        
        header("Location: dashboard.php");
    } else {
        echo "<script>alert('Invalid Email or Password!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card p-4 shadow border-0" style="border-radius: 15px;">
                    <h3 class="text-center text-primary">Login</h3>
                    <form method="POST">
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button name="login" class="btn btn-primary w-100">Login</button>
                    </form>
                    <p class="mt-3 text-center">New here? <a href="register.php">Create Account</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>