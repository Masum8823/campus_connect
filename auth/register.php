<?php
include '../config.php';

if(isset($_POST['register'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $u_id = mysqli_real_escape_string($conn, $_POST['u_id']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $dept = $_POST['dept'];

    $query = "INSERT INTO users (full_name, university_id, email, password, role, dept) 
              VALUES ('$name', '$u_id', '$email', '$pass', '$role', '$dept')";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Registration Successful!'); window.location='login.php';</script>";
    } else {
        echo "<div class='alert alert-danger text-center'>Error: ID or Email already exists!</div>";
    }
}   
?>
<!-- Rest of the HTML stays the same, just ensure links are correct -->
<!DOCTYPE html>
<html lang="en">
<head>
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
            <h3 class="text-center mb-4 text-primary">CampusConnect Registration</h3>
            <form method="POST">
                <div class="mb-3">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter Full Name" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Student/Teacher ID</label>
                        <input type="text" name="u_id" class="form-control" placeholder="ID (e.g. 21020xx)" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Department</label>
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
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="University Email" required>
                </div>

                <div class="mb-4">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Create Password" required>
                </div>

                <button name="register" class="btn btn-primary w-100 shadow-sm">Create Account</button>
            </form>
            <p class="mt-3 text-center">Already have an account? <a href="login.php" class="text-decoration-none">Login Here</a></p>
        </div>
    </div>
</body>
</html>