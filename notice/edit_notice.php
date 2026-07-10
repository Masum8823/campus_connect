<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] == 'student' || !isset($_GET['id'])){
    header("Location: ../user/dashboard.php");
    exit();
}

$notice_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch Notice Info to populate fields
$query = mysqli_query($conn, "SELECT * FROM notices WHERE id='$notice_id' AND user_id='$user_id'");
$notice = mysqli_fetch_assoc($query);

if(!$notice){
    echo "Permission Denied!";
    exit();
}

// Update Logic
if(isset($_POST['update_notice'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    
    mysqli_query($conn, "UPDATE notices SET title='$title', description='$desc' WHERE id='$notice_id'");
    header("Location: ../user/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Notice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container card p-4 shadow-sm" style="max-width: 600px;">
        <h4>Edit Notice</h4>
        <form method="POST">
            <input type="text" name="title" class="form-control mb-3" value="<?php echo $notice['title']; ?>" required>
            <textarea name="desc" class="form-control mb-3" rows="5" required><?php echo $notice['description']; ?></textarea>
            <button name="update_notice" class="btn btn-primary">Update Notice</button>
            <a href="../user/dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>