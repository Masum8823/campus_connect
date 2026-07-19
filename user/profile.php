<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($query);

$profile_img = ($user['profile_pic'] != 'default.png') ? "../" . $user['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($user['full_name'])."&background=random&size=128";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; }
        .profile-header { background: linear-gradient(135deg, #0d6efd 0%, #003d99 100%); height: 150px; border-radius: 0 0 20px 20px; }
        .profile-card { margin-top: -75px; border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .user-avatar { width: 150px; height: 150px; object-fit: cover; border: 5px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .info-label { font-size: 12px; font-weight: bold; color: #888; text-transform: uppercase; }
        .info-value { font-size: 16px; color: #333; margin-bottom: 15px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">CampusConnect</a>
            <a href="dashboard.php" class="btn btn-light btn-sm fw-bold">Back to Feed</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="profile-header"></div>
        
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card profile-card p-4">
                    <div class="row">
                        <!-- Left Column: Avatar & Basic Info -->
                        <div class="col-md-4 text-center border-end">
                            <img src="<?php echo $profile_img; ?>" class="rounded-circle user-avatar mb-3">
                            <h3 class="fw-bold mb-1"><?php echo $user['full_name']; ?></h3>
                            <span class="badge bg-primary px-3 rounded-pill mb-2"><?php echo strtoupper($user['role']); ?></span>
                            <p class="text-muted small"><?php echo $user['dept']; ?> Department</p>
                            <hr>
                            <div class="text-start px-3">
                                <p class="small text-secondary italic">"<?php echo $user['bio'] ?? 'No bio added yet.'; ?>"</p>
                            </div>
                            <a href="edit_profile.php" class="btn btn-outline-primary btn-sm w-100 mt-3 rounded-pill">
                                <i class="bi bi-pencil-square"></i> Edit Profile
                            </a>
                        </div>

                        <!-- Right Column: Full Details -->
                        <div class="col-md-8 ps-md-5 mt-4 mt-md-0">
                            <h5 class="fw-bold mb-4 text-primary border-bottom pb-2">Professional Details</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="info-label">University ID</label>
                                    <p class="info-value"><?php echo $user['university_id']; ?></p>
                                    
                                    <label class="info-label">Official Email</label>
                                    <p class="info-value"><?php echo $user['email']; ?></p>

                                    <label class="info-label">Contact Number</label>
                                    <p class="info-value"><?php echo $user['phone'] ?? 'Not provided'; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="info-label">Batch/Semester</label>
                                    <p class="info-value"><?php echo $user['batch'] ?? 'Not provided'; ?></p>

                                    <label class="info-label">Skills / Interests</label>
                                    <p class="info-value"><?php echo $user['skills'] ?? 'Not provided'; ?></p>

                                    <label class="info-label">LinkedIn Profile</label>
                                    <p class="info-value">
                                        <?php if($user['linkedin_url']): ?>
                                            <a href="<?php echo $user['linkedin_url']; ?>" target="_blank">View Profile</a>
                                        <?php else: echo 'Not provided'; endif; ?>
                                    </p>
                                </div>
                            </div>

                            <h5 class="fw-bold mt-4 mb-3 text-primary border-bottom pb-2">Account Statistics</h5>
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="p-2 bg-light rounded">
                                        <h4 class="mb-0">
                                            <?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM posts WHERE user_id='$user_id'"))['total']; ?>
                                        </h4>
                                        <small class="text-muted">Posts</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 bg-light rounded">
                                        <h4 class="mb-0">
                                            <?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM comments WHERE user_id='$user_id'"))['total']; ?>
                                        </h4>
                                        <small class="text-muted">Comments</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 bg-light rounded">
                                        <h4 class="mb-0">
                                            <?php echo date('M Y', strtotime($user['created_at'])); ?>
                                        </h4>
                                        <small class="text-muted">Joined</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>