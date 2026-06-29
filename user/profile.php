<?php
include '../config.php'; // Correct path to root config
session_start();

// Redirect to login if not authenticated
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(isset($_POST['upload'])){
    $filename = $_FILES["image"]["name"];
    $tempname = $_FILES["image"]["tmp_name"];
    
    // Path to save in DB: "uploads/filename"
    $db_save_path = "uploads/" . time() . "_" . $filename; 
    
    // Actual path to move file: "../uploads/filename" (Going up one level)
    $upload_target = "../" . $db_save_path; 

    if(move_uploaded_file($tempname, $upload_target)){
        // Update database with the clean path
        $query = "UPDATE users SET profile_pic='$db_save_path' WHERE id='$user_id'";
        mysqli_query($conn, $query);
        echo "<script>alert('Profile Picture Updated!'); window.location='dashboard.php';</script>";
    } else {
        echo "Failed to upload image. Please check folder permissions.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4 card p-4 shadow">
                <h4 class="text-center text-primary">Update Profile Picture</h4>
                <hr>
                <form method="POST" enctype="multipart/form-data">
                    <label class="form-label small text-muted">Select an image file</label>
                    <input type="file" name="image" class="form-control mb-3" required>
                    <button name="upload" class="btn btn-primary w-100 fw-bold">Upload Image</button>
                </form>
                <br>
                <a href="dashboard.php" class="text-center d-block text-decoration-none">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>