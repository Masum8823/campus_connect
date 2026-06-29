<?php
include '../config.php'; // Correct path to root config
session_start();

// Security: Only teachers/admins can post notices
if(!isset($_SESSION['user_id']) || $_SESSION['role'] == 'student'){
    echo "Access Denied! Only Teachers/Admins can post notices.";
    exit();
}

if(isset($_POST['add_notice'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $audience = $_POST['audience'];
    $user_id = $_SESSION['user_id']; 

    $query = "INSERT INTO notices (user_id, title, description, target_audience) 
              VALUES ('$user_id', '$title', '$desc', '$audience')";
              
    if(mysqli_query($conn, $query)){
        // Success: Redirect to dashboard inside user folder
        echo "<script>alert('Notice Posted!'); window.location='../user/dashboard.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Notice - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 card p-4 shadow border-0">
                <h4 class="text-center text-primary mb-4">Post Official Notice</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Notice Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="desc" class="form-control" rows="5" placeholder="Detailed description..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Audience</label>
                        <select name="audience" class="form-control">
                            <option value="all">For Everyone</option>
                            <option value="students">For Students Only</option>
                            <option value="teachers">For Teachers Only</option>
                        </select>
                    </div>
                    <button name="add_notice" class="btn btn-primary w-100 fw-bold">Publish Notice</button>
                </form>
                <div class="text-center mt-3">
                    <!-- Correct path back to dashboard -->
                    <a href="../user/dashboard.php" class="text-decoration-none">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>