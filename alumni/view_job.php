<?php
include '../config.php';

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: jobs.php");
    exit();
}

$id = $_GET['id'];

// Fetch job details and alumni info
$query = "SELECT alumni_jobs.*, users.full_name, users.profile_pic, users.dept as alumni_dept 
          FROM alumni_jobs 
          JOIN users ON alumni_jobs.alumni_id = users.id 
          WHERE alumni_jobs.id = '$id'";
$result = mysqli_query($conn, $query);
$job = mysqli_fetch_assoc($result);

if(!$job){ echo "Job post not found!"; exit(); }

$profile_img = ($job['profile_pic'] != 'default.png') ? "../" . $job['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($job['full_name'])."&background=random";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Details | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 100px; }
        .detail-card { border-radius: 25px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.05); background: white; overflow: hidden; }
        .header-accent { background: linear-gradient(135deg, #0d6efd 0%, #4b0082 100%); padding: 40px; color: white; text-align: center; }
        .publisher-box { background: #f8f9fa; border-radius: 15px; padding: 15px; display: flex; align-items: center; }
        .apply-btn { padding: 15px 40px; border-radius: 50px; font-weight: 800; font-size: 18px; transition: 0.3s; }
        .apply-btn:hover { transform: scale(1.05); box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2); }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="jobs.php"><i class="bi bi-arrow-left me-2"></i> Back to Job Board</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card detail-card">
                    <div class="header-accent">
                        <span class="badge bg-white text-primary rounded-pill px-3 mb-3 fw-bold"><?php echo $job['job_type']; ?></span>
                        <h1 class="fw-bold mb-2"><?php echo $job['job_title']; ?></h1>
                        <p class="lead mb-0 opacity-75"><?php echo $job['company']; ?></p>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <!-- Key Highlights -->
                        <div class="row g-3 mb-5">
                            <div class="col-6 col-md-3 text-center">
                                <div class="p-3 bg-light rounded-4 border h-100">
                                    <i class="bi bi-people-fill text-danger fs-3"></i>
                                    <h6 class="mt-2 mb-0 fw-bold small">Vacancy</h6>
                                    <span class="text-muted small"><?php echo $job['vacancy']; ?> Positions</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 text-center">
                                <div class="p-3 bg-light rounded-4 border h-100">
                                    <i class="bi bi-mortarboard-fill text-primary fs-3"></i>
                                    <h6 class="mt-2 mb-0 fw-bold small">Target</h6>
                                    <span class="text-muted small"><?php echo $job['target_dept']; ?></span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 text-center">
                                <div class="p-3 bg-light rounded-4 border h-100">
                                    <i class="bi bi-geo-alt-fill text-success fs-3"></i>
                                    <h6 class="mt-2 mb-0 fw-bold small">Location</h6>
                                    <span class="text-muted small"><?php echo $job['location']; ?></span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 text-center">
                                <div class="p-3 bg-light rounded-4 border h-100">
                                    <i class="bi bi-calendar-check text-warning fs-3"></i>
                                    <h6 class="mt-2 mb-0 fw-bold small">Posted</h6>
                                    <span class="text-muted small"><?php echo date('M d, Y', strtotime($job['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Job Description -->
                        <h4 class="fw-bold mb-3 border-bottom pb-2">Full Job Description</h4>
                        <p class="text-secondary" style="font-size: 17px; line-height: 1.8; white-space: pre-line;">
                            <?php echo $job['description']; ?>
                        </p>

                        <!-- Publisher Info -->
                        <div class="publisher-box border mt-5">
                            <img src="<?php echo $profile_img; ?>" class="rounded-circle me-3 border" width="55" height="55" style="object-fit: cover;">
                            <div>
                                <small class="text-muted d-block" style="font-size: 10px; font-weight: 700; text-transform: uppercase;">Opportunity Shared By</small>
                                <h6 class="mb-0 fw-bold"><?php echo $job['full_name']; ?></h6>
                                <small class="text-muted"><?php echo $job['alumni_dept']; ?> Graduate</small>
                            </div>
                            <a href="../user/profile.php?id=<?php echo $job['alumni_id']; ?>" class="ms-auto btn btn-sm btn-outline-primary rounded-pill">Profile</a>
                        </div>

                        <!-- Apply Button -->
                        <div class="text-center mt-5">
                            <a href="<?php echo $job['apply_link']; ?>" target="_blank" class="btn btn-primary apply-btn shadow">
                                <i class="bi bi-box-arrow-up-right me-2"></i> APPLY FOR THIS ROLE
                            </a>
                            <p class="text-muted small mt-3">You will be redirected to the application link or email portal.</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>