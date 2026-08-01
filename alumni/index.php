<?php
include '../config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

$user_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$current_user_id'"));
$my_pic = ($user_info['profile_pic'] != 'default.png') ? "../" . $user_info['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($_SESSION['user_name'])."&background=random";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alumni Hub | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --bg-light: #f8f9fa; }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 100px; }
        .menu-card { border-radius: 25px; border: none; transition: 0.3s; background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.05); text-align: center; padding: 40px 20px; height: 100%; }
        .menu-card:hover { transform: translateY(-10px); box-shadow: 0 15px 40px rgba(13, 110, 253, 0.15); border: 1px solid var(--primary-color); }
        .icon-box { width: 80px; height: 80px; background: #e7f1ff; color: var(--primary-color); border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 35px; margin: 0 auto 20px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="../user/dashboard.php">CampusConnect Alumni</a>
            <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold rounded-pill px-4">Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Welcome to Alumni Hub</h2>
            <p class="text-muted">Connect, Learn, and Grow with our alumni community</p>
        </div>

        <div class="row g-4 justify-content-center">
            <!-- Card 1: Stories -->
            <div class="col-md-4">
                <a href="stories.php" class="text-decoration-none">
                    <div class="card menu-card">
                        <div class="icon-box"><i class="bi bi-journal-text"></i></div>
                        <h4 class="fw-bold text-dark">Success Stories</h4>
                        <p class="text-muted small">Read inspirational career journeys and roadmaps from our graduates.</p>
                        <span class="btn btn-outline-primary btn-sm rounded-pill px-4">Explore Stories</span>
                    </div>
                </a>
            </div>

            <!-- Card 2: Job Board -->
            <div class="col-md-4">
                <a href="jobs.php" class="text-decoration-none">
                    <div class="card menu-card">
                        <div class="icon-box"><i class="bi bi-briefcase"></i></div>
                        <h4 class="fw-bold text-dark">Job Opportunities</h4>
                        <p class="text-muted small">Find the latest jobs and internship openings shared by our alumni.</p>
                        <span class="btn btn-outline-primary btn-sm rounded-pill px-4">View Jobs</span>
                    </div>
                </a>
            </div>

            <!-- Card 3: Directory -->
            <div class="col-md-4">
                <a href="directory.php" class="text-decoration-none">
                    <div class="card menu-card">
                        <div class="icon-box"><i class="bi bi-people"></i></div>
                        <h4 class="fw-bold text-dark">Alumni Directory</h4>
                        <p class="text-muted small">Find out where our alumni are working and connect with them.</p>
                        <span class="btn btn-outline-primary btn-sm rounded-pill px-4">View Directory</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

</body>
</html>