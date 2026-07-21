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
$sql = "SELECT alumni_stories.*, users.full_name, users.profile_pic, users.dept 
        FROM alumni_stories 
        JOIN users ON alumni_stories.user_id = users.id WHERE 1=1";

if($search){
    $safe_search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (current_job_title LIKE '%$safe_search%' OR company_name LIKE '%$safe_search%' OR users.dept LIKE '%$safe_search%')";
}

$sql .= " ORDER BY created_at DESC";
$stories = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Alumni Hub - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; padding-top: 80px; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .journey-card { border-radius: 20px; border: none; transition: 0.3s; background: white; }
        .journey-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .search-box { border-radius: 30px; padding-left: 45px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .search-icon { position: absolute; left: 20px; top: 12px; color: #aaa; }
        .inspired-btn { border-radius: 50px; font-weight: bold; transition: 0.3s; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../user/dashboard.php">CampusConnect Alumni</a>
            <div class="d-flex">
                <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold me-2">Back to Feed</a>
                <?php if($user_role == 'alumni' || $user_role == 'admin'): ?>
                    <a href="share_journey.php" class="btn btn-warning btn-sm fw-bold">Share My Journey</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Search Section -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-7">
                <form method="GET" class="position-relative">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" name="search" class="form-control form-control-lg search-box" placeholder="Search by Job, Company or Dept..." value="<?php echo $search; ?>">
                </form>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-8">
                <?php while($row = mysqli_fetch_assoc($stories)): 
                    $sid = $row['id'];
                    $count_res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM alumni_inspired WHERE story_id='$sid'"));
                    $total_inspired = $count_res['total'];
                    $is_inspired = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM alumni_inspired WHERE story_id='$sid' AND user_id='$current_user_id'")) > 0;
                ?>
                    <div class="card journey-card mb-4 shadow-sm">
                        <div class="card-body p-4 p-lg-5">
                            <div class="d-flex align-items-center mb-4">
                                <?php $img = ($row['profile_pic'] != 'default.png') ? "../" . $row['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($row['full_name']); ?>
                                <img src="<?php echo $img; ?>" class="rounded-circle me-3 border border-3 border-light shadow-sm" width="65" height="65" style="object-fit: cover;">
                                <div>
                                    <h5 class="mb-0 fw-bold"><?php echo $row['full_name']; ?></h5>
                                    <p class="text-primary mb-0 fw-semibold" style="font-size: 14px;"><?php echo $row['current_job_title']; ?> @ <?php echo $row['company_name']; ?></p>
                                    <small class="text-muted"><?php echo $row['dept']; ?> Graduate</small>
                                </div>
                            </div>

                            <p class="text-dark mb-4" style="font-size: 15px; line-height: 1.7;"><?php echo nl2br(substr($row['journey_story'], 0, 300)); ?>...</p>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <!-- Inspired Toggle Button -->
                                <a href="toggle_inspire.php?id=<?php echo $sid; ?>" class="btn btn-sm <?php echo $is_inspired ? 'btn-danger' : 'btn-outline-danger'; ?> inspired-btn px-3">
                                    <i class="bi <?php echo $is_inspired ? 'bi-heart-fill' : 'bi-heart'; ?>"></i> Inspired (<?php echo $total_inspired; ?>)
                                </a>
                                
                                <a href="view_journey.php?id=<?php echo $sid; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">Read Full Roadmap →</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>