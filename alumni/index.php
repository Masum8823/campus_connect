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
    $stories_sql .= " AND (alumni_stories.current_job_title LIKE '%$safe_search%' 
                        OR alumni_stories.company_name LIKE '%$safe_search%' 
                        OR users.full_name LIKE '%$safe_search%')";
}
$stories = mysqli_query($conn, $stories_sql . " ORDER BY created_at DESC");

$jobs_sql = "SELECT alumni_jobs.*, users.full_name FROM alumni_jobs JOIN users ON alumni_jobs.alumni_id = users.id WHERE 1=1";
if($view == 'jobs' && $search){
    $safe_search = mysqli_real_escape_string($conn, $search);
    $jobs_sql .= " AND (alumni_jobs.job_title LIKE '%$safe_search%' 
                     OR alumni_jobs.company LIKE '%$safe_search%' 
                     OR alumni_jobs.target_dept LIKE '%$safe_search%' 
                     OR users.full_name LIKE '%$safe_search%')";
}
$jobs = mysqli_query($conn, $jobs_sql . " ORDER BY created_at DESC");

$directory = mysqli_query($conn, "SELECT users.id, full_name, dept, current_job_title, company_name, profile_pic FROM alumni_stories JOIN users ON alumni_stories.user_id = users.id GROUP BY users.id");

function getCategoryIcon($category) {
    switch ($category) {
        case 'Electronics': return 'bi-laptop';
        case 'Documents': return 'bi-file-earmark-medical';
        case 'Personal Items': return 'bi-person-badge';
        case 'Wallets/Bags': return 'bi-wallet2';
        default: return 'bi-box-seam';
    }
}
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
        .journey-card, .job-card { border-radius: 20px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: 0.3s; background: white; height: 100%; }
        .journey-card:hover, .job-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .search-box { border-radius: 50px; background: white; border: 1px solid #eee; padding: 12px 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="../user/dashboard.php">CampusConnect Alumni</a>
            <div class="ms-auto d-flex align-items-center">
                <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold rounded-pill px-3 me-2">Dashboard</a>
                <?php if($user_role == 'alumni' || $user_role == 'admin'): ?>
                    <div class="dropdown">
                        <button class="btn btn-warning btn-sm fw-bold rounded-pill dropdown-toggle shadow-sm" data-bs-toggle="dropdown">+ Post</button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item small" href="share_journey.php">Share Journey</a></li>
                            <li><a class="dropdown-item small" href="post_job.php">Post Job/Internship</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4 pb-5">
        <!-- Tab Navigation -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-9">
                <ul class="nav nav-pills nav-fill hub-nav">
                    <li class="nav-item"><a class="nav-link <?php echo $view == 'stories' ? 'active' : ''; ?>" href="?view=stories"><i class="bi bi-journal-text me-2"></i>Success Stories</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $view == 'jobs' ? 'active' : ''; ?>" href="?view=jobs"><i class="bi bi-briefcase me-2"></i>Job Board</a></li>
                    <li class="nav-item"><a class="nav-link <?php echo $view == 'directory' ? 'active' : ''; ?>" href="?view=directory"><i class="bi bi-people me-2"></i>Directory</a></li>
                </ul>
            </div>
        </div>

        <!-- SEARCH BAR -->
        <?php if($view != 'directory'): ?>
        <div class="row justify-content-center mb-5">
            <div class="col-md-7">
                <form method="GET" action="">
                    <input type="hidden" name="view" value="<?php echo $view; ?>">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control search-box" placeholder="Search by name, company, title or department..." value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary rounded-pill px-4 ms-2 shadow-sm fw-bold" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- SUCCESS STORIES SECTION -->
        <?php if($view == 'stories'): ?>
            <div class="row justify-content-center">
                <div class="col-md-9 col-lg-8">
                    <?php while($row = mysqli_fetch_assoc($stories)): 
                        $sid = $row['id'];
                        $is_inspired = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM alumni_inspired WHERE story_id='$sid' AND user_id='$current_user_id'")) > 0;
                        $total_inspired = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM alumni_inspired WHERE story_id='$sid'"))['total'];
                    ?>
                        <div class="card journey-card mb-4 shadow-sm">
                            <div class="card-body p-4 p-lg-5">
                                <div class="d-flex align-items-center mb-4">
                                    <?php $img = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name'])."&background=random"; ?>
                                    <img src="<?php echo $img; ?>" class="rounded-circle me-3 border shadow-sm" width="65" height="65" style="object-fit:cover;">
                                    <div><h5 class="mb-0 fw-bold"><?php echo $row['full_name']; ?></h5><p class="text-primary mb-0 small fw-bold"><?php echo $row['current_job_title']; ?> @ <?php echo $row['company_name']; ?></p></div>
                                </div>
                                <p class="text-secondary mb-4" style="line-height: 1.7;"><?php echo nl2br(substr($row['journey_story'], 0, 320)); ?>...</p>
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <a href="toggle_inspire.php?id=<?php echo $sid; ?>" class="btn btn-sm <?php echo $is_inspired ? 'btn-danger' : 'btn-outline-danger'; ?> rounded-pill px-3"><i class="bi <?php echo $is_inspired ? 'bi-heart-fill' : 'bi-heart'; ?> me-1"></i> Inspired (<?php echo $total_inspired; ?>)</a>
                                    <a href="view_journey.php?id=<?php echo $sid; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">Read Full Roadmap →</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

        <!-- JOB BOARD SECTION (Updated with Vacancy and Target Dept) -->
        <?php elseif($view == 'jobs'): ?>
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="row">
                        <?php if(mysqli_num_rows($jobs) > 0): ?>
                            <?php while($job = mysqli_fetch_assoc($jobs)): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card job-card p-4 d-flex flex-column shadow-sm">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 small"><?php echo $job['job_type']; ?></span>
                                            <span class="text-danger fw-bold small"><i class="bi bi-people-fill"></i> Vacancy: <?php echo $job['vacancy']; ?></span>
                                        </div>

                                        <h5 class="fw-bold text-dark mb-1"><?php echo $job['job_title']; ?></h5>
                                        <p class="text-muted small fw-bold mb-2">
                                            <i class="bi bi-building"></i> <?php echo $job['company']; ?> • 
                                            <i class="bi bi-mortarboard-fill"></i> For: <?php echo $job['target_dept']; ?>
                                        </p>
                                        
                                        <p class="small text-secondary mb-4"><?php echo nl2br(substr($job['description'], 0, 150)); ?>...</p>
                                        
                                        <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top">
                                            <div class="d-flex align-items-center">
                                                <small class="text-muted">By: <strong><?php echo explode(' ', $job['full_name'])[0]; ?></strong></small>
                                                <?php if($job['alumni_id'] == $current_user_id): ?>
                                                    <div class="btn-group ms-2">
                                                        <a href="edit_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm text-secondary p-0 px-1"><i class="bi bi-pencil-square"></i></a>
                                                        <a href="delete_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm text-danger p-0 px-1" onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <a href="<?php echo $job['apply_link']; ?>" target="_blank" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm">Apply</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-5"><p class="text-muted">No matching jobs found.</p></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <!-- DIRECTORY SECTION -->
        <?php elseif($view == 'directory'): ?>
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="row g-3">
                        <?php while($dir = mysqli_fetch_assoc($directory)): ?>
                            <div class="col-6 col-md-3">
                                <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100">
                                    <?php $img = ($dir['profile_pic'] != 'default.png') ? "../" . $dir['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($dir['full_name'])."&background=random"; ?>
                                    <a href="../user/profile.php?id=<?php echo $dir['id']; ?>"><img src="<?php echo $img; ?>" class="rounded-circle mx-auto mb-2 shadow-sm" width="70" height="70" style="object-fit: cover;"></a>
                                    <h6 class="fw-bold text-dark mb-1 small"><?php echo $dir['full_name']; ?></h6>
                                    <p class="text-primary mb-0" style="font-size: 10px; font-weight: 800;"><?php echo $dir['company_name']; ?></p>
                                    <small class="text-muted" style="font-size: 10px;"><?php echo $dir['dept']; ?> Dept.</small>
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