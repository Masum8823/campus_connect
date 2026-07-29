<?php
include '../config.php';
session_start();
if(!isset($_SESSION['user_id'])){ header("Location: ../auth/login.php"); exit(); }

$search = $_GET['search'] ?? '';
$sql = "SELECT alumni_stories.*, users.full_name, users.profile_pic, users.dept FROM alumni_stories JOIN users ON alumni_stories.user_id = users.id WHERE 1=1";
if($search){
    $safe_search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (alumni_stories.current_job_title LIKE '%$safe_search%' OR alumni_stories.company_name LIKE '%$safe_search%' OR users.full_name LIKE '%$safe_search%')";
}
$stories = mysqli_query($conn, $sql . " ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Success Stories | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .journey-card { border-radius: 20px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 20px; background: white; }
        .search-box { border-radius: 50px; background: white; border: 1px solid #eee; padding: 12px 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">← Success Stories</a>
            <div class="ms-auto">
                <?php if($_SESSION['role'] == 'alumni' || $_SESSION['role'] == 'admin'): ?>
                    <a href="share_journey.php" class="btn btn-warning btn-sm fw-bold rounded-pill px-3">Share My Journey</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center mb-5">
            <div class="col-md-7">
                <form method="GET"><input type="text" name="search" class="form-control search-box" placeholder="Search by name, job, or company..." value="<?php echo htmlspecialchars($search); ?>"></form>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-8">
                <?php while($row = mysqli_fetch_assoc($stories)): ?>
                    <div class="card journey-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <?php $img = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name']); ?>
                                <img src="<?php echo $img; ?>" class="rounded-circle me-3 border shadow-sm" width="65" height="65" style="object-fit:cover;">
                                <div><h5 class="mb-0 fw-bold"><?php echo $row['full_name']; ?></h5><p class="text-primary mb-0 small fw-bold"><?php echo $row['current_job_title']; ?> @ <?php echo $row['company_name']; ?></p></div>
                            </div>
                            <p class="text-secondary mb-4"><?php echo nl2br(substr($row['journey_story'], 0, 300)); ?>...</p>
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <a href="toggle_inspire.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="bi bi-heart-fill me-1"></i> Inspire</a>
                                <a href="view_journey.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">Read Full Journey →</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>