<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$view_user_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];

$is_my_profile = ($view_user_id == $_SESSION['user_id']);

$query = mysqli_query($conn, "SELECT * FROM users WHERE id='$view_user_id'");
$user = mysqli_fetch_assoc($query);

if(!$user){
    echo "User not found!";
    exit();
}

$profile_img = ($user['profile_pic'] != 'default.png') ? "../" . $user['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($user['full_name'])."&background=random&size=128";

$conn_status_query = mysqli_query($conn, "SELECT * FROM connections WHERE (sender_id='".$_SESSION['user_id']."' AND receiver_id='$view_user_id') OR (sender_id='$view_user_id' AND receiver_id='".$_SESSION['user_id']."')");
$conn_data = mysqli_fetch_assoc($conn_status_query);

$is_connected = false;
$is_pending = false;

if($conn_data){
    if($conn_data['status'] == 'accepted'){
        $is_connected = true;
    } else {
        $is_pending = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $user['full_name']; ?> - Profile</title>
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
                        <div class="col-md-4 text-center border-end">
                            <img src="<?php echo $profile_img; ?>" class="rounded-circle user-avatar mb-3">
                            <h3 class="fw-bold mb-1"><?php echo $user['full_name']; ?></h3>
                            <span class="badge bg-primary px-3 rounded-pill mb-2"><?php echo strtoupper($user['role']); ?></span>
                            <p class="text-muted small"><?php echo $user['dept']; ?> Department</p>
                            <hr>
                            <div class="text-start px-3">
                                <p class="small text-secondary italic">"<?php echo $user['bio'] ?? 'No bio added yet.'; ?>"</p>
                            </div>

                            <?php if($is_my_profile): ?>
                            <a href="edit_profile.php" class="btn btn-outline-primary btn-sm w-100 mt-3 rounded-pill">
                                <i class="bi bi-pencil-square"></i> Edit Profile
                            </a>
                        <?php else: ?>
                            <?php if($is_connected): ?>
                                <a href="toggle_connect.php?id=<?php echo $view_user_id; ?>" class="btn btn-success btn-sm w-100 mt-3 rounded-pill">
                                    <i class="bi bi-person-check-fill"></i> Connected
                                </a>
                            <?php elseif($is_pending): ?>
                                <a href="toggle_connect.php?id=<?php echo $view_user_id; ?>" class="btn btn-warning btn-sm w-100 mt-3 rounded-pill">
                                    <i class="bi bi-clock-history"></i> Request Pending
                                </a>
                            <?php else: ?>
                                <a href="toggle_connect.php?id=<?php echo $view_user_id; ?>" class="btn btn-primary btn-sm w-100 mt-3 rounded-pill">
                                    <i class="bi bi-person-plus-fill"></i> Connect
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                        </div>

                        <div class="col-md-8 ps-md-5 mt-4 mt-md-0">
                            <h5 class="fw-bold mb-4 text-primary border-bottom pb-2">Information Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="info-label">University ID</label>
                                    <p class="info-value"><?php echo $user['university_id']; ?></p>
                                    <label class="info-label">Email</label>
                                    <p class="info-value"><?php echo $user['email']; ?></p>
                                    <label class="info-label">Contact</label>
                                    <p class="info-value"><?php echo $user['phone'] ?? 'Not provided'; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="info-label">Batch</label>
                                    <p class="info-value"><?php echo $user['batch'] ?? 'N/A'; ?></p>
                                    <label class="info-label">Skills</label>
                                    <p class="info-value"><?php echo $user['skills'] ?? 'N/A'; ?></p>
                                    <label class="info-label">LinkedIn</label>
                                    <p class="info-value">
                                        <?php if($user['linkedin_url']): ?>
                                            <a href="<?php echo $user['linkedin_url']; ?>" target="_blank">View Profile</a>
                                        <?php else: echo 'N/A'; endif; ?>
                                    </p>
                                </div>
                            </div>

                            <h5 class="fw-bold mt-4 mb-3 text-primary border-bottom pb-2">User Stats</h5>
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="p-2 bg-light rounded">
                                        <h4 class="mb-0">
                                            <?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM posts WHERE user_id='$view_user_id'"))['total']; ?>
                                        </h4>
                                        <small class="text-muted">Posts</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 bg-light rounded">
                                        <h4 class="mb-0">
                                            <?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM comments WHERE user_id='$view_user_id'"))['total']; ?>
                                        </h4>
                                        <small class="text-muted">Comments</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 bg-light rounded">
                                        <h4 class="mb-0"><?php echo date('M Y', strtotime($user['created_at'])); ?></h4>
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