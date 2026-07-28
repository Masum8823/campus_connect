<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

$view = $_GET['view'] ?? 'stories';
$search = $_GET['search'] ?? '';

$stories_sql = "SELECT alumni_stories.*, users.full_name, users.profile_pic, users.dept FROM alumni_stories JOIN users ON alumni_stories.user_id = users.id WHERE 1=1";
if($view == 'stories' && $search){
    $safe_search = mysqli_real_escape_string($conn, $search);
    $stories_sql .= " AND (current_job_title LIKE '%$safe_search%' OR company_name LIKE '%$safe_search%' OR users.full_name LIKE '%$safe_search%')";
}
$stories = mysqli_query($conn, $stories_sql . " ORDER BY created_at DESC");

$jobs_sql = "SELECT alumni_jobs.*, users.full_name FROM alumni_jobs JOIN users ON alumni_jobs.alumni_id = users.id ORDER BY created_at DESC";
$jobs = mysqli_query($conn, $jobs_sql);

$directory = mysqli_query($conn, "SELECT full_name, dept, current_job_title, company_name, profile_pic FROM alumni_stories JOIN users ON alumni_stories.user_id = users.id GROUP BY user_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alumni Hub Portal | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --bg-light: #f8f9fa; }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .hub-nav { background: white; border-radius: 15px; padding: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .hub-nav .nav-link { border-radius: 10px; color: #666; font-weight: 600; padding: 10px 20px; transition: 0.3s; }
        .hub-nav .nav-link.active { background: var(--primary-color); color: white; }
        .journey-card, .job-card { border-radius: 20px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: 0.3s; background: white; }
        .journey-card:hover, .job-card:hover { transform: translateY(-5px); }
        .search-box { border-radius: 50px; background: white; border: 1px solid #eee; padding: 12px 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="../user/dashboard.php">CampusConnect Alumni</a>
            <div class="d-flex align-items-center">
                <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold rounded-pill px-3 me-2">Dashboard</a>
                <?php if($user_role == 'alumni' || $user_role == 'admin'): ?>
                    <div class="dropdown">
                        <button class="btn btn-warning btn-sm fw-bold rounded-pill dropdown-toggle" data-bs-toggle="dropdown">+ Post</button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="share_journey.php">Share Journey</a></li>
                            <li><a class="dropdown-item" href="post_job.php">Post Job/Internship</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Section Selector (Tabs) -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <ul class="nav nav-pills nav-fill hub-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $view == 'stories' ? 'active' : ''; ?>" href="?view=stories"><i class="bi bi-journal-text me-2"></i>Success Stories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $view == 'jobs' ? 'active' : ''; ?>" href="?view=jobs"><i class="bi bi-briefcase me-2"></i>Job Board</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $view == 'directory' ? 'active' : ''; ?>" href="?view=directory"><i class="bi bi-people me-2"></i>Directory</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- content Render based on View -->
        <?php if($view == 'stories'): ?>
            <!-- Success Stories View -->
            <div class="row justify-content-center">
                <div class="col-md-7 mb-4">
                    <form method="GET" action=""><input type="hidden" name="view" value="stories"><input type="text" name="search" class="form-control search-box" placeholder="Search by Job, Company or Name..." value="<?php echo $search; ?>"></form>
                </div>
                <div class="col-md-9 col-lg-8">
                    <?php while($row = mysqli_fetch_assoc($stories)): 
                        $sid = $row['id'];
                        $is_inspired = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM alumni_inspired WHERE story_id='$sid' AND user_id='$current_user_id'")) > 0;
                    ?>
                        <div class="card journey-card mb-4">
                            <div class="card-body p-4 p-lg-5">
                                <div class="d-flex align-items-center mb-4">
                                    <?php $img = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name'])."&background=random"; ?>
                                    <img src="<?php echo $img; ?>" class="rounded-circle me-3 border shadow-sm" width="65" height="65">
                                    <div><h5 class="mb-0 fw-bold"><?php echo $row['full_name']; ?></h5><p class="text-primary mb-0 small fw-bold"><?php echo $row['current_job_title']; ?> @ <?php echo $row['company_name']; ?></p></div>
                                </div>
                                <p class="text-secondary mb-4" style="line-height: 1.7;"><?php echo nl2br(substr($row['journey_story'], 0, 300)); ?>...</p>
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <a href="toggle_inspire.php?id=<?php echo $sid; ?>" class="btn btn-sm <?php echo $is_inspired ? 'btn-danger' : 'btn-outline-danger'; ?> rounded-pill px-3"><i class="bi bi-heart-fill me-1"></i> Inspired</a>
                                    <a href="view_journey.php?id=<?php echo $sid; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">Read Full Journey →</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

        <?php elseif($view == 'jobs'): ?>
            <!-- Job Board View -->
            <div class="row justify-content-center">
                <div class="col-md-9">
                    <h4 class="fw-bold mb-4">Career Opportunities</h4>
                    <div class="row">
                        <?php while($job = mysqli_fetch_assoc($jobs)): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card job-card p-4">
                                    <span class="badge bg-primary-subtle text-primary mb-2 align-self-start rounded-pill px-3"><?php echo $job['job_type']; ?></span>
                                    <h5 class="fw-bold text-dark mb-1"><?php echo $job['job_title']; ?></h5>
                                    <p class="text-muted small fw-bold mb-3"><i class="bi bi-building"></i> <?php echo $job['company']; ?> • <i class="bi bi-geo-alt"></i> <?php echo $job['location']; ?></p>
                                    <p class="small text-secondary mb-4"><?php echo nl2br(substr($job['description'], 0, 120)); ?>...</p>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <small class="text-muted">Posted by: <strong><?php echo $job['full_name']; ?></strong></small>
                                        <a href="<?php echo $job['apply_link']; ?>" target="_blank" class="btn btn-primary btn-sm rounded-pill px-4">Apply Now</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

        <?php elseif($view == 'directory'): ?>
            <!-- Alumni Directory View -->
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <h4 class="fw-bold mb-4">Where our Alumni are working</h4>
                    <div class="row row-cols-2 row-cols-md-4 g-3">
                        <?php while($dir = mysqli_fetch_assoc($directory)): ?>
                            <div class="col text-center">
                                <div class="bg-white p-3 rounded-4 shadow-sm h-100 border">
                                    <?php $img = ($dir['profile_pic'] != 'default.png') ? "../" . $dir['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($dir['full_name']); ?>
                                    <img src="<?php echo $img; ?>" class="rounded-circle mb-2" width="60" height="60" style="object-fit: cover;">
                                    <h6 class="fw-bold small mb-1"><?php echo $dir['full_name']; ?></h6>
                                    <p class="text-muted mb-0" style="font-size: 10px;"><?php echo $dir['company_name']; ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>