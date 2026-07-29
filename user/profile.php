<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// ১. কার প্রোফাইল দেখছি সেটি আইডি দিয়ে ধরা
$view_user_id = isset($_GET['id']) ? $_GET['id'] : $current_user_id;
$is_my_profile = ($view_user_id == $current_user_id);

// ২. ইউজারের তথ্য আনা
$query = mysqli_query($conn, "SELECT * FROM users WHERE id='$view_user_id'");
$user = mysqli_fetch_assoc($query);

if(!$user){
    echo "User not found!";
    exit();
}

// --- ৩. কানেকশন স্ট্যাটাস চেক করার লজিক (এটিই আগে মিসিং ছিল) ---
$conn_status_query = mysqli_query($conn, "SELECT * FROM connections WHERE (sender_id='$current_user_id' AND receiver_id='$view_user_id') OR (sender_id='$view_user_id' AND receiver_id='$current_user_id')");
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
// --------------------------------------------------------

// ৪. এই ইউজারের করা সব পোস্ট তুলে আনা (Timeline)
$user_posts_query = "SELECT * FROM posts WHERE user_id='$view_user_id' ORDER BY created_at DESC";
$user_posts = mysqli_query($conn, $user_posts_query);

// প্রোফাইল পিকচার লজিক
$profile_img = ($user['profile_pic'] != 'default.png') ? "../" . $user['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($user['full_name'])."&background=random&size=128";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $user['full_name']; ?> | Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --bg-light: #f0f2f5; --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .profile-header { background: linear-gradient(135deg, #0d6efd 0%, #003d99 100%); height: 180px; border-radius: 0 0 30px 30px; }
        .profile-card { margin-top: -90px; border-radius: 25px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); background: white; }
        .user-avatar { width: 150px; height: 150px; object-fit: cover; border: 6px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-radius: 30px; }
        .info-label { font-size: 11px; font-weight: 800; color: #adb5bd; text-transform: uppercase; letter-spacing: 1px; }
        .info-value { font-size: 15px; color: #333; margin-bottom: 15px; font-weight: 600; }
        .stat-box { background: #f8f9fa; border-radius: 15px; padding: 15px; text-align: center; border: 1px solid #eee; }
        .timeline-post { border-radius: 20px; border: none; box-shadow: var(--card-shadow); margin-bottom: 20px; background: white; overflow: hidden; }
        .post-img { width: 100%; max-height: 400px; object-fit: cover; border-radius: 12px; margin-top: 10px; }
        .section-title { font-weight: 800; color: #2d3436; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="dashboard.php">CampusConnect</a>
            <a href="dashboard.php" class="btn btn-light btn-sm fw-bold rounded-pill px-4">Back to Feed</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="profile-header"></div>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Profile Info Card -->
                <div class="card profile-card p-4 p-md-5 mb-5">
                    <div class="row">
                        <!-- Left Column: User Profile -->
                        <div class="col-md-4 text-center border-end">
                            <img src="<?php echo $profile_img; ?>" class="user-avatar mb-4">
                            <h3 class="fw-bold mb-1 text-dark"><?php echo $user['full_name']; ?></h3>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-3 small fw-bold"><?php echo strtoupper($user['role']); ?></span>
                            <p class="text-muted small mb-4"><?php echo $user['dept']; ?> Department</p>
                            
                            <div class="px-3 mb-4">
                                <p class="small text-secondary fw-medium italic">"<?php echo $user['bio'] ?? 'No bio added yet.'; ?>"</p>
                            </div>

                            <!-- বাটন লজিক (Connect / Pending / Connected) -->
                            <?php if($is_my_profile): ?>
                                <a href="edit_profile.php" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                                    <i class="bi bi-pencil-square me-2"></i> Edit Profile
                                </a>
                            <?php else: ?>
                                <?php if($is_connected): ?>
                                    <a href="toggle_connect.php?id=<?php echo $view_user_id; ?>" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm">
                                        <i class="bi bi-person-check-fill me-2"></i> Connected
                                    </a>
                                <?php elseif($is_pending): ?>
                                    <a href="toggle_connect.php?id=<?php echo $view_user_id; ?>" class="btn btn-warning w-100 rounded-pill fw-bold py-2 shadow-sm">
                                        <i class="bi bi-clock-history me-2"></i> Request Pending
                                    </a>
                                <?php else: ?>
                                    <a href="toggle_connect.php?id=<?php echo $view_user_id; ?>" class="btn btn-outline-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                                        <i class="bi bi-person-plus-fill me-2"></i> Connect
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Right Column: Details & Stats -->
                        <div class="col-md-8 ps-md-5 mt-5 mt-md-0">
                            <h5 class="section-title" style="font-size: 18px;">Information Details</h5>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="info-label">University ID</label>
                                    <p class="info-value"><?php echo $user['university_id']; ?></p>
                                    <label class="info-label">Official Email</label>
                                    <p class="info-value text-truncate"><?php echo $user['email']; ?></p>
                                    <label class="info-label">Contact</label>
                                    <p class="info-value"><?php echo $user['phone'] ?? 'Not provided'; ?></p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="info-label">Batch</label>
                                    <p class="info-value"><?php echo $user['batch'] ?? 'N/A'; ?></p>
                                    <label class="info-label">Skills</label>
                                    <p class="info-value text-primary"><?php echo $user['skills'] ?? 'N/A'; ?></p>
                                    <label class="info-label">LinkedIn</label>
                                    <p class="info-value">
                                        <?php if($user['linkedin_url']): ?>
                                            <a href="<?php echo $user['linkedin_url']; ?>" target="_blank" class="text-decoration-none">View Profile</a>
                                        <?php else: echo 'N/A'; endif; ?>
                                    </p>
                                </div>
                            </div>

                            <h5 class="section-title mt-4" style="font-size: 18px;">Activity Statistics</h5>
                            <div class="row g-3">
                                <div class="col-4">
                                    <div class="stat-box shadow-sm">
                                        <h3 class="fw-bold mb-0 text-primary"><?php echo mysqli_num_rows($user_posts); ?></h3>
                                        <small class="text-muted fw-bold small">Posts</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-box shadow-sm">
                                        <h3 class="fw-bold mb-0 text-success">
                                            <?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM comments WHERE user_id='$view_user_id'"))['total']; ?>
                                        </h3>
                                        <small class="text-muted fw-bold small">Comments</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-box shadow-sm">
                                        <h6 class="fw-bold mb-0 mt-2"><?php echo date('M Y', strtotime($user['created_at'])); ?></h6>
                                        <small class="text-muted fw-bold small">Joined</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- --- User Posts Timeline Section --- -->
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <h4 class="section-title"><i class="bi bi-grid-3x3-gap-fill text-primary"></i> <?php echo $is_my_profile ? "My Timeline" : $user['full_name']."'s Posts"; ?></h4>
                        
                        <?php if(mysqli_num_rows($user_posts) > 0): ?>
                            <?php while($post = mysqli_fetch_assoc($user_posts)): 
                                $pid = $post['id'];
                                $likes_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM likes WHERE post_id='$pid'"))['total'];
                                $is_liked = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM likes WHERE post_id='$pid' AND user_id='$current_user_id'")) > 0;
                                $comments_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM comments WHERE post_id='$pid'"))['total'];
                            ?>
                                <div class="card timeline-post shadow-sm p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $profile_img; ?>" class="rounded-circle me-2 border shadow-sm" width="40" height="40" style="object-fit:cover;">
                                            <div>
                                                <h6 class="mb-0 fw-bold small text-dark"><?php echo $user['full_name']; ?></h6>
                                                <small class="text-muted" style="font-size: 11px;"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></small>
                                            </div>
                                        </div>
                                        
                                        <?php if($is_my_profile || $user_role == 'admin'): ?>
                                            <div class="dropdown">
                                                <i class="bi bi-three-dots text-muted" role="button" data-bs-toggle="dropdown"></i>
                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                                    <?php if($is_my_profile): ?>
                                                        <li><a class="dropdown-item small" href="../post/edit_post.php?id=<?php echo $pid; ?>"><i class="bi bi-pencil me-2"></i> Edit</a></li>
                                                    <?php endif; ?>
                                                    <li><a class="dropdown-item small text-danger" href="../post/delete_post.php?id=<?php echo $pid; ?>" onclick="return confirm('Delete this post?')"><i class="bi bi-trash me-2"></i> Delete</a></li>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <p class="card-text mb-3" style="font-size: 15px; color: #444;"><?php echo nl2br($post['content']); ?></p>

                                    <?php if(!empty($post['post_image'])): ?>
                                        <img src="../<?php echo $post['post_image']; ?>" class="post-img mb-3 shadow-sm border">
                                    <?php endif; ?>

                                    <div class="d-flex gap-4 text-muted small fw-bold border-top pt-3">
                                        <a href="toggle_like.php?post_id=<?php echo $pid; ?>" class="text-decoration-none <?php echo $is_liked ? 'text-primary' : 'text-muted'; ?>">
                                            <i class="bi <?php echo $is_liked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up'; ?> me-1"></i> 
                                            <?php echo $likes_count; ?> Like<?php echo ($likes_count != 1) ? 's' : ''; ?>
                                        </a>

                                        <a href="../post/view_post.php?id=<?php echo $pid; ?>" class="text-decoration-none text-muted">
                                            <i class="bi bi-chat-left"></i> <?php echo $comments_count; ?> Comments
                                        </a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-5 bg-white rounded-4 border">
                                <i class="bi bi-file-earmark-post display-1 text-muted opacity-25"></i>
                                <p class="text-muted mt-3">No posts yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Timeline End -->

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>