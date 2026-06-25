<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand" href="#">CampusConnect</a>
            <div class="d-flex text-white">
                <span>Welcome, <?php echo $_SESSION['user_name']; ?> (<?php echo $_SESSION['role']; ?>)</span>
                <a href="logout.php" class="btn btn-danger btn-sm ms-3">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5 text-center">
        <h1>Hello, <?php echo $_SESSION['user_name']; ?>!</h1>
        <p>You are successfully logged in to CampusConnect Dashboard.</p>
        <div class="alert alert-info">Campus Feed and other features are coming soon...</div>
    </div>
</body>
</html>