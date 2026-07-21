<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Fetch detailed alumni story joined with user info
$query = "SELECT alumni_stories.*, users.full_name, users.profile_pic, users.dept, users.university_id 
          FROM alumni_stories 
          JOIN users ON alumni_stories.user_id = users.id 
          WHERE alumni_stories.id = '$id'";
$result = mysqli_query($conn, $query);
$story = mysqli_fetch_assoc($result);

if(!$story){
    echo "Journey not found!";
    exit();
}

$profile_img = ($story['profile_pic'] != 'default.png') ? "../" . $story['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($story['full_name'])."&background=random&size=128";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $story['full_name']; ?>'s Journey - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .journey-container { max-width: 900px; margin: 0 auto; }
        .main-card { border-radius: 25px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.05); overflow: hidden; background: #fff; }
        .header-bg { background: linear-gradient(135deg, #0d6efd 0%, #6f42c1 100%); height: 180px; }
        .profile-avatar { width: 130px; height: 130px; object-fit: cover; border: 6px solid #fff; margin-top: -65px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .section-title { font-weight: 800; border-left: 5px solid #0d6efd; padding-left: 15px; margin-bottom: 20px; color: #333; }
        .roadmap-box { background: #f0f7ff; border-radius: 20px; padding: 25px; border: 1px dashed #0d6efd; }
        .salary-tag { background: #fff3cd; color: #856404; padding: 5px 15px; border-radius: 50px; font-weight: bold; font-size: 14px; }
        .navbar { background: #0d6efd !important; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-arrow-left me-2"></i> Back to Alumni Hub</a>
        </div>
    </nav>

    <div class="container journey-container pb-5">
        <div class="card main-card">
            <div class="header-bg"></div>
            <div class="card-body p-4 p-lg-5">
                <!-- Profile Header -->
                <div class="text-center mb-5">
                    <img src="<?php echo $profile_img; ?>" class="rounded-circle profile-avatar mb-3">
                    <h2 class="fw-bold mb-1"><?php echo $story['full_name']; ?></h2>
                    <p class="text-primary fw-bold mb-1"><?php echo $story['current_job_title']; ?> @ <?php echo $story['company_name']; ?></p>
                    <div class="d-flex justify-content-center gap-2 mt-2">
                        <span class="badge bg-light text-dark border"><?php echo $story['dept']; ?> Graduate</span>
                        <?php if($story['first_salary']): ?>
                            <span class="salary-tag"><i class="bi bi-cash-stack"></i> First Salary: <?php echo $story['first_salary']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Success Story Section -->
                <div class="mb-5">
                    <h4 class="section-title">The Success Story</h4>
                    <p class="text-secondary" style="font-size: 17px; line-height: 1.9; white-space: pre-line;">
                        <?php echo $story['journey_story']; ?>
                    </p>
                </div>

                <!-- Roadmap Section -->
                <div class="mb-5">
                    <h4 class="section-title">Career Roadmap & Guidance</h4>
                    <div class="roadmap-box">
                        <p class="mb-0 text-dark" style="font-size: 16px; line-height: 1.8; white-space: pre-line;">
                            <?php echo $story['career_roadmap']; ?>
                        </p>
                    </div>
                </div>

                <!-- Mistakes & Advice Grid -->
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="p-4 bg-danger bg-opacity-10 rounded-4 border-start border-danger border-5 h-100">
                            <h5 class="fw-bold text-danger"><i class="bi bi-x-circle-fill"></i> Mistakes to Avoid</h5>
                            <p class="text-dark small mt-3"><?php echo nl2br($story['biggest_mistake']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-4 bg-success bg-opacity-10 rounded-4 border-start border-success border-5 h-100">
                            <h5 class="fw-bold text-success"><i class="bi bi-lightbulb-fill"></i> Pro Advice</h5>
                            <p class="text-dark small mt-3"><?php echo nl2br($story['advice_to_juniors']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Tech Stack -->
                <div class="p-4 bg-light rounded-4 text-center border">
                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 1px;">Recommended Tech Stack / Skills</h6>
                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <?php 
                            $skills = explode(',', $story['skills_used']);
                            foreach($skills as $skill):
                        ?>
                            <span class="badge bg-white text-primary border border-primary px-3 py-2"><?php echo trim($skill); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Bottom Actions -->
                <div class="text-center mt-5">
                    <hr>
                    <p class="text-muted small">Was this story inspiring?</p>
                    <button class="btn btn-primary rounded-pill px-5 fw-bold shadow">
                        <i class="bi bi-heart-fill me-2"></i> Inspired
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>