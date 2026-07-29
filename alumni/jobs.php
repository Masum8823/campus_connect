<?php
include '../config.php';
session_start();
if(!isset($_SESSION['user_id'])){ header("Location: ../auth/login.php"); exit(); }

$search = $_GET['search'] ?? '';
$sql = "SELECT alumni_jobs.*, users.full_name FROM alumni_jobs JOIN users ON alumni_jobs.alumni_id = users.id WHERE 1=1";
if($search){
    $safe_search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (job_title LIKE '%$safe_search%' OR company LIKE '%$safe_search%' OR target_dept LIKE '%$safe_search%' OR full_name LIKE '%$safe_search%')";
}
$jobs = mysqli_query($conn, $sql . " ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Board | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .job-card { border-radius: 20px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: 0.3s; background: white; height: 100%; }
        .search-box { border-radius: 50px; background: white; border: 1px solid #eee; padding: 12px 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">← Job Board</a>
            <?php if($_SESSION['role'] == 'alumni' || $_SESSION['role'] == 'admin'): ?>
                <a href="post_job.php" class="btn btn-warning btn-sm fw-bold rounded-pill px-3">+ Post Job</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center mb-5">
            <div class="col-md-7">
                <form method="GET"><input type="text" name="search" class="form-control search-box" placeholder="Search by title, company or department..." value="<?php echo htmlspecialchars($search); ?>"></form>
            </div>
        </div>
        <div class="row">
            <?php while($job = mysqli_fetch_assoc($jobs)): ?>
                <div class="col-md-6 mb-4">
                    <div class="card job-card p-4 d-flex flex-column shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 small"><?php echo $job['job_type']; ?></span>
                            <span class="text-danger fw-bold small"><i class="bi bi-people-fill"></i> Vacancy: <?php echo $job['vacancy']; ?></span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1"><?php echo $job['job_title']; ?></h5>
                        <p class="text-muted small fw-bold mb-2"><i class="bi bi-building"></i> <?php echo $job['company']; ?> • <i class="bi bi-mortarboard-fill"></i> For: <?php echo $job['target_dept']; ?></p>
                        <p class="small text-secondary mb-4"><?php echo nl2br(substr($job['description'], 0, 150)); ?>...</p>
                        <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top">
                            <small class="text-muted">By: <strong><?php echo $job['full_name']; ?></strong></small>
                            <a href="<?php echo $job['apply_link']; ?>" target="_blank" class="btn btn-primary btn-sm rounded-pill px-4">Apply</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>