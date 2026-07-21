<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Fetch all alumni journeys from database
$query = "SELECT alumni_stories.*, users.full_name, users.profile_pic, users.dept 
          FROM alumni_stories 
          JOIN users ON alumni_stories.user_id = users.id 
          ORDER BY created_at DESC";
$stories = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alumni Hub - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .journey-card { border-radius: 20px; border: none; transition: 0.3s; background: white; overflow: hidden; }
        .journey-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .alumni-badge { font-size: 10px; background: #6f42c1; color: white; padding: 3px 10px; border-radius: 50px; font-weight: bold; }
        .quote-icon { font-size: 40px; color: #e9ecef; position: absolute; top: 20px; right: 30px; }
        .insight-box { font-size: 13px; line-height: 1.5; }
        .navbar { background: #0d6efd !important; }
    </style>
</head>
<body>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../user/dashboard.php">
                <i class="bi bi-mortarboard-fill me-2"></i>Alumni Hub
            </a>
            <div class="ms-auto d-flex">
                <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold me-2">Back to Feed</a>
                <!-- Show "Share Journey" button only for Alumni or Admin -->
                <?php if($user_role == 'alumni' || $user_role == 'admin'): ?>
                    <a href="share_journey.php" class="btn btn-warning btn-sm fw-bold">Share My Journey</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark mb-2">Inspirational Journeys</h2>
            <p class="text-muted">Explore career paths and advice from our successful graduates.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-8">
                <?php if(mysqli_num_rows($stories) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($stories)): ?>
                        <div class="card journey-card mb-4 shadow-sm position-relative">
                            <i class="bi bi-quote quote-icon"></i>
                            <div class="card-body p-4 p-lg-5">
                                <!-- Alumni Profile Header -->
                                <div class="d-flex align-items-center mb-4">
                                    <?php $img = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name'])."&background=random"; ?>
                                    <img src="<?php echo $img; ?>" class="rounded-circle me-3 border border-3 border-light shadow-sm" width="70" height="70" style="object-fit: cover;">
                                    <div>
                                        <h5 class="mb-0 fw-bold"><?php echo $row['full_name']; ?> <span class="alumni-badge ms-1 text-uppercase">ALUMNI</span></h5>
                                        <p class="text-primary mb-0 fw-semibold" style="font-size: 14px;"><?php echo $row['current_job_title']; ?> at <?php echo $row['company_name']; ?></p>
                                        <small class="text-muted"><?php echo $row['dept']; ?> Graduate</small>
                                    </div>
                                </div>

                                <!-- The Story -->
                                <div class="story-content mb-4">
                                    <p class="text-dark" style="font-size: 16px; line-height: 1.8;">
                                        <?php echo nl2br(substr($row['journey_story'], 0, 350)); ?>...
                                    </p>
                                </div>

                                <!-- Two-Column Highlights -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-danger bg-opacity-10 rounded-4 border-start border-danger border-4 h-100">
                                            <h6 class="fw-bold text-danger small mb-2"><i class="bi bi-exclamation-triangle-fill"></i> BIGGEST MISTAKE</h6>
                                            <p class="insight-box text-dark mb-0"><?php echo $row['biggest_mistake']; ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-success bg-opacity-10 rounded-4 border-start border-success border-4 h-100">
                                            <h6 class="fw-bold text-success small mb-2"><i class="bi bi-lightbulb-fill"></i> ADVICE TO JUNIORS</h6>
                                            <p class="insight-box text-dark mb-0"><?php echo $row['advice_to_juniors']; ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer Info -->
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <div class="small">
                                        <span class="text-muted">Tech Stack:</span> 
                                        <span class="badge bg-light text-primary border ms-1"><?php echo $row['skills_used']; ?></span>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">View Full Roadmap →</button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                        <i class="bi bi-journal-richtext display-1 text-muted"></i>
                        <h4 class="mt-3 text-muted">No stories found.</h4>
                        <p class="text-muted">Be the first alumni to inspire the community!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>