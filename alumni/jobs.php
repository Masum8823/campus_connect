<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

$search = $_GET['search'] ?? '';
$sql = "SELECT alumni_jobs.*, users.full_name FROM alumni_jobs 
        JOIN users ON alumni_jobs.alumni_id = users.id WHERE 1=1";

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Board | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --bg-light: #f8f9fa; }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .job-card { border-radius: 20px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: 0.3s; background: white; height: 100%; display: flex; flex-direction: column; }
        .job-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .search-box { border-radius: 50px; background: white; border: 1px solid #eee; padding: 12px 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .action-btn { font-size: 18px; color: #adb5bd; transition: 0.2s; }
        .action-btn:hover { color: var(--primary-color); }
        .delete-btn:hover { color: #dc3545; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-arrow-left me-2"></i> Job Board</a>
            <?php if($user_role == 'alumni' || $user_role == 'admin'): ?>
                <a href="post_job.php" class="btn btn-warning btn-sm fw-bold rounded-pill px-4 text-dark shadow-sm">+ Post Job</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Search Bar -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-7">
                <form method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control search-box" placeholder="Search by title, company or department..." value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary rounded-pill px-4 ms-2 fw-bold shadow-sm" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <?php if(mysqli_num_rows($jobs) > 0): ?>
                <?php while($job = mysqli_fetch_assoc($jobs)): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card job-card p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 small"><?php echo $job['job_type']; ?></span>
                                
                                <!-- Edit/Delete Options (Only for owner or admin) -->
                                <?php if($job['alumni_id'] == $current_user_id || $user_role == 'admin'): ?>
                                    <div class="d-flex gap-2">
                                        <?php if($job['alumni_id'] == $current_user_id): ?>
                                            <a href="edit_job.php?id=<?php echo $job['id']; ?>" class="action-btn" title="Edit Post"><i class="bi bi-pencil-square"></i></a>
                                        <?php endif; ?>
                                        <a href="delete_job.php?id=<?php echo $job['id']; ?>" class="action-btn delete-btn" title="Delete Post" onclick="return confirm('Are you sure you want to delete this job post?')"><i class="bi bi-trash"></i></a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <h4 class="fw-bold text-dark mb-1"><?php echo $job['job_title']; ?></h4>
                            <p class="text-muted small fw-bold mb-2">
                                <i class="bi bi-building"></i> <?php echo $job['company']; ?> • 
                                <i class="bi bi-people-fill text-danger"></i> Vacancy: <?php echo $job['vacancy']; ?>
                            </p>
                            <p class="text-primary small fw-bold mb-3"><i class="bi bi-mortarboard"></i> Targeted: <?php echo $job['target_dept']; ?></p>
                            
                            <p class="small text-secondary mb-4 flex-grow-1"><?php echo nl2br(substr($job['description'], 0, 160)); ?>...</p>
                            
                            <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top">
                                <small class="text-muted">Shared by: <strong><?php echo $job['full_name']; ?></strong></small>
                                <a href="<?php echo $job['apply_link']; ?>" target="_blank" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-bold">Apply Now</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-briefcase display-1 text-muted opacity-25"></i>
                    <p class="text-muted mt-3">No job opportunities found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>