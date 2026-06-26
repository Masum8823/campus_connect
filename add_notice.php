<?php
include 'config.php';
session_start();

// সিকিউরিটি: স্টুডেন্টরা যাতে এই পেজে ঢুকতে না পারে
if(!isset($_SESSION['user_id']) || $_SESSION['role'] == 'student'){
    echo "Access Denied! Only Teachers/Admins can post notices.";
    exit();
}

if(isset($_POST['add_notice'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $audience = $_POST['audience'];

    $query = "INSERT INTO notices (title, description, target_audience) VALUES ('$title', '$desc', '$audience')";
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Notice Posted!'); window.location='dashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Notice - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 card p-4 shadow border-0">
                <h4 class="text-center text-primary">Post Official Notice</h4>
                <form method="POST">
                    <input type="text" name="title" class="form-control mb-3" placeholder="Notice Title" required>
                    <textarea name="desc" class="form-control mb-3" rows="5" placeholder="Detailed Description" required></textarea>
                    <select name="audience" class="form-control mb-3">
                        <option value="all">For Everyone</option>
                        <option value="students">For Students Only</option>
                        <option value="teachers">For Teachers Only</option>
                    </select>
                    <button name="add_notice" class="btn btn-primary w-100">Publish Notice</button>
                </form>
                <a href="dashboard.php" class="text-center d-block mt-3">Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>