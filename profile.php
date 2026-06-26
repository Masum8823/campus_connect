<?php
include 'config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(isset($_POST['upload'])){
    $filename = $_FILES["image"]["name"];
    $tempname = $_FILES["image"]["tmp_name"];
    $folder = "uploads/" . time() . "_" . $filename; // ফাইলের নাম ইউনিক করার জন্য টাইম যোগ করা

    if(move_uploaded_file($tempname, $folder)){
        $query = "UPDATE users SET profile_pic='$folder' WHERE id='$user_id'";
        mysqli_query($conn, $query);
        echo "<script>alert('Profile Picture Updated!'); window.location='dashboard.php';</script>";
    } else {
        echo "Failed to upload image.";
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
                <h4 class="text-center">Update Profile Picture</h4>
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="image" class="form-control mb-3" required>
                    <button name="upload" class="btn btn-primary w-100">Upload Image</button>
                </form>
                <br>
                <a href="dashboard.php" class="text-center d-block">Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>