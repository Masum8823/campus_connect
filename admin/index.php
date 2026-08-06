<?php
include '../config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit();
}

$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$total_posts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM posts"))['total'];
$total_notices = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM notices"))['total'];
$total_items = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM lost_found"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; padding-top: 80px; font-family: 'Plus Jakarta Sans', sans-serif; }
        .stat-card { border-radius: 20px; border: none; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: #212529; padding: 20px; color: white; }
        .main-content { margin-left: 260px; padding: 20px; }
        .nav-link { color: #ccc; padding: 12px; border-radius: 10px; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { background: #0d6efd; color: white; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h4 class="fw-bold text-center mb-4 text-primary">Admin Control</h4>
        <nav class="nav flex-column">
            <a href="index.php" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="manage_users.php" class="nav-link"><i class="bi bi-people me-2"></i> Manage Users</a>
            <a href="manage_lost_found.php" class="nav-link text-white"><i class="bi bi-search me-2"></i> Lost & Found</a>
            <a href="manage_academic.php" class="nav-link text-white"><i class="bi bi-mortarboard me-2"></i> Academic Resources</a>
            <a href="manage_content.php" class="nav-link"><i class="bi bi-file-post me-2"></i> Content Moderation</a>
            <a href="../user/dashboard.php" class="nav-link"><i class="bi bi-arrow-left-circle me-2"></i> User View</a>
            <hr>
            <a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-power me-2"></i> Logout</a>
            <a href="suggestions.php" class="nav-link text-white">
                <i class="bi bi-chat-right-heart me-2"></i> User Suggestions
            </a>        
        </nav>
    </div>

    <div class="main-content">
        <h2 class="fw-bold mb-4">System Overview</h2>
        <div class="row">
            <div class="col-md-3">
                <div class="card stat-card bg-primary text-white p-3 mb-4">
                    <h3><?php echo $total_users; ?></h3>
                    <p class="mb-0">Total Members</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-success text-white p-3 mb-4">
                    <h3><?php echo $total_posts; ?></h3>
                    <p class="mb-0">Campus Posts</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-warning text-dark p-3 mb-4">
                    <h3><?php echo $total_notices; ?></h3>
                    <p class="mb-0">Official Notices</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-info text-white p-3 mb-4">
                    <h3><?php echo $total_items; ?></h3>
                    <p class="mb-0">L&F Reports</p>
                </div>
            </div>
        </div>

        <div class="card stat-card p-4 mt-2">
            <h5 class="fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i> Welcome, Admin!</h5>
            <p class="text-muted mb-0">From here you can manage all users, monitor content, and ensure the safety of the CampusConnect community.</p>
        </div>
    </div>

</body>
</html>