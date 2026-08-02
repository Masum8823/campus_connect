<?php
include '../config.php';
// config.php তে সেশন স্টার্ট করা আছে তাই এখানে আর দরকার নেই

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

// ৩. কানেকশন স্ট্যাটাস চেক
$conn_status_query = mysqli_query($conn, "SELECT * FROM connections WHERE (sender_id='$current_user_id' AND receiver_id='$view_user_id') OR (sender_id='$view_user_id' AND receiver_id='$current_user_id')");
$conn_data = mysqli_fetch_assoc($conn_status_query);
$is_connected = false;
$is_pending_conn = false;

if($conn_data){
    if($conn_data['status'] == 'accepted') $is_connected = true;
    else $is_pending_conn = true;
}

// ৪. মেসেজ রিকোয়েস্ট লজিক
$msg_req_query = mysqli_query($conn, "SELECT * FROM message_requests WHERE (sender_id='$current_user_id' AND receiver_id='$view_user_id') OR (sender_id='$view_user_id' AND receiver_id='$current_user_id')");
$msg_req_data = mysqli_fetch_assoc($msg_req_query);
$chat_status = "none";
$msg_req_sender = null;
if($msg_req_data) {
    $chat_status = $msg_req_data['status'];
    $msg_req_sender = $msg_req_data['sender_id'];
}

// ৫. ব্লক চেক
$i_blocked_query = mysqli_query($conn, "SELECT * FROM message_blocks WHERE blocker_id='$current_user_id' AND blocked_id='$view_user_id'");
$i_blocked_him = mysqli_num_rows($i_blocked_query) > 0;
$he_blocked_query = mysqli_query($conn, "SELECT * FROM message_blocks WHERE blocker_id='$view_user_id' AND blocked_id='$current_user_id'");
$he_blocked_me = mysqli_num_rows($he_blocked_query) > 0;

// --- ৬. প্রাইভেসী লজিক (Privacy Logic) ---
// তথ্য দেখাবে যদি: নিজের প্রোফাইল হয় অথবা পাবলিক প্রোফাইল হয় অথবা অলরেডি কানেক্টেড থাকে
$show_details = ($is_my_profile || $user['is_private'] == 0 || $is_connected);

// ৭. টাইমলাইন আনা
$user_posts_query = "SELECT * FROM posts WHERE user_id='$view_user_id' ORDER BY created_at DESC";
$user_posts = mysqli_query($conn, $user_posts_query);

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
        .post-img { width: 100%; max-height: 400px; object-fit: cover; border-radius: 12px; margin-top: 10px; border: 1px solid #eee; }
        .section-title { font-weight: 800; color: #2d3436; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        .private-lock { font-size: 50px; color: #adb5bd; }
    </style>
</head>
<body>

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
                <div class="card profile-card p-4 p-md-5 mb-5">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-4 text-center border-end">
                            <img src="<?php echo $profile_img; ?>" class="user-avatar mb-4">
                            <h3 class="fw-bold mb-1 text-dark"><?php echo $user['full_name']; ?></h3>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-3 small fw-bold"><?php echo strtoupper($user['role']); ?></span>
                            <p class="text-muted small mb-1"><?php echo $user['dept']; ?> Department</p>
                            
                            <!-- Privacy Status Badge -->
                            <?php if($user['is_private']): ?>
                                <span class="badge bg-light text-muted border rounded-pill mb-4" style="font-size: 10px;"><i class="bi bi-lock-fill"></i> Private Profile</span>
                            <?php endif; ?>
                            
                            <p class="small text-secondary fw-medium italic mb-4">"<?php echo $user['bio'] ?? 'No bio added yet.'; ?>"</p>

                            <div class="d-grid gap-2 px-3">
                                <?php if($is_my_profile): ?>
                                    <a href="edit_profile.php" class="btn btn-primary rounded-pill fw-bold py-2 shadow-sm">Edit Profile</a>
                                <?php elseif($he_blocked_me): ?>
                                    <div class="alert alert-danger small py-2 rounded-pill text-center">Blocked</div>
                                <?php elseif($i_blocked_him): ?>
                                    <a href="toggle_block.php?id=<?php echo $view_user_id; ?>" class="btn btn-danger rounded-pill fw-bold py-2 shadow-sm">Unblock User</a>
                                <?php else: ?>
                                    <!-- Connection Button -->
                                    <?php if($is_connected): ?>
                                        <a href="toggle_connect.php?id=<?php echo $view_user_id; ?>" class="btn btn-success rounded-pill fw-bold py-2">Connected</a>
                                    <?php elseif($is_pending_conn): ?>
                                        <a href="toggle_connect.php?id=<?php echo $view_user_id; ?>" class="btn btn-warning rounded-pill fw-bold py-2">Pending Request</a>
                                    <?php else: ?>
                                        <a href="toggle_connect.php?id=<?php echo $view_user_id; ?>" class="btn btn-outline-primary rounded-pill fw-bold py-2">Connect</a>
                                    <?php endif; ?>

                                    <!-- Message Button -->
                                    <div class="mt-1">
                                        <?php if($_SESSION['role'] == 'admin' || $chat_status == 'accepted'): ?>
                                            <a href="chat.php?user_id=<?php echo $view_user_id; ?>" class="btn btn-dark w-100 rounded-pill fw-bold py-2 shadow-sm">Message</a>
                                        <?php elseif($chat_status == 'pending'): ?>
                                            <button class="btn btn-secondary w-100 rounded-pill fw-bold py-2" disabled>Message Requested</button>
                                        <?php else: ?>
                                            <a href="send_msg_request.php?id=<?php echo $view_user_id; ?>" class="btn btn-outline-dark w-100 rounded-pill fw-bold py-2">Request Message</a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-center mt-2">
                                        <a href="toggle_block.php?id=<?php echo $view_user_id; ?>" class="text-danger small text-decoration-none fw-bold"><i class="bi bi-slash-circle"></i> Block</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-8 ps-md-5 mt-5 mt-md-0">
                            <?php if($show_details): ?>
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
                                        <p class="info-value"><?php echo $user['linkedin_url'] ? "<a href='{$user['linkedin_url']}' target='_blank'>View Profile</a>" : 'N/A'; ?></p>
                                    </div>
                                </div>
                                <h5 class="section-title mt-4" style="font-size: 18px;">Activity Statistics</h5>
                                <div class="row g-3 text-center">
                                    <div class="col-4"><div class="stat-box shadow-sm"><h3 class="fw-bold mb-0 text-primary"><?php echo mysqli_num_rows($user_posts); ?></h3><small class="text-muted fw-bold small">Posts</small></div></div>
                                    <div class="col-4"><div class="stat-box shadow-sm"><h3 class="fw-bold mb-0 text-success"><?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM comments WHERE user_id='$view_user_id'"))['total']; ?></h3><small class="text-muted fw-bold small">Comments</small></div></div>
                                    <div class="col-4"><div class="stat-box shadow-sm"><h6 class="fw-bold mb-0 mt-2"><?php echo date('M Y', strtotime($user['created_at'])); ?></h6><small class="text-muted fw-bold small">Joined</small></div></div>
                                </div>
                            <?php else: ?>
                                <!-- Privacy Locked State -->
                                <div class="text-center py-5">
                                    <i class="bi bi-lock-fill private-lock mb-3 d-block"></i>
                                    <h4 class="fw-bold">This Profile is Private</h4>
                                    <p class="text-muted">Only connected users can see full information and timeline.</p>
                                    <?php if(!$is_pending_conn): ?>
                                        <p class="small text-primary fw-bold">Send a connection request to see more!</p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Timeline Section -->
                <?php if($show_details): ?>
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <h4 class="section-title"><i class="bi bi-grid-3x3-gap-fill text-primary"></i> <?php echo $is_my_profile ? "My Timeline" : $user['full_name']."'s Posts"; ?></h4>
                        <?php if(mysqli_num_rows($user_posts) > 0): ?>
                            <?php while($post = mysqli_fetch_assoc($user_posts)): 
                                $pid = $post['id'];
                                $likes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM likes WHERE post_id='$pid'"))['total'];
                                $is_liked = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM likes WHERE post_id='$pid' AND user_id='$current_user_id'")) > 0;
                                $comments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM comments WHERE post_id='$pid'"))['total'];
                            ?>
                                <div class="card timeline-post shadow-sm p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $profile_img; ?>" class="rounded-circle me-2 border shadow-sm" width="40" height="40" style="object-fit:cover;">
                                            <div><h6 class="mb-0 fw-bold small text-dark"><?php echo $user['full_name']; ?></h6><small class="text-muted" style="font-size: 11px;"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></small></div>
                                        </div>
                                        <?php if($is_my_profile || $user_role == 'admin'): ?>
                                            <div class="dropdown">
                                                <i class="bi bi-three-dots text-muted" role="button" data-bs-toggle="dropdown"></i>
                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                                    <?php if($is_my_profile): ?><li><a class="dropdown-item small" href="../post/edit_post.php?id=<?php echo $pid; ?>"><i class="bi bi-pencil me-2"></i> Edit</a></li><?php endif; ?>
                                                    <li><a class="dropdown-item small text-danger" href="../post/delete_post.php?id=<?php echo $pid; ?>" onclick="return confirm('Delete?')"><i class="bi bi-trash me-2"></i> Delete</a></li>
                                                </ul>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <p class="card-text mb-3" style="font-size: 15px; color: #444;"><?php echo nl2br($post['content']); ?></p>
                                    <?php if(!empty($post['post_image'])): ?><img src="../<?php echo $post['post_image']; ?>" class="post-img mb-3 shadow-sm border"><?php endif; ?>
                                    <div class="d-flex gap-4 text-muted small fw-bold border-top pt-3">
                                        <a href="toggle_like.php?post_id=<?php echo $pid; ?>" class="text-decoration-none <?php echo $is_liked ? 'text-primary' : 'text-muted'; ?>">
                                            <i class="bi <?php echo $is_liked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up'; ?> me-1"></i> <?php echo $likes; ?> Likes
                                        </a>
                                        <a href="../post/view_post.php?id=<?php echo $pid; ?>" class="text-decoration-none text-muted"><i class="bi bi-chat-left"></i> <?php echo $comments; ?> Comments</a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?><div class="text-center py-5 bg-white rounded-4 border"><i class="bi bi-file-earmark-post display-1 text-muted opacity-25"></i><p class="text-muted mt-3">No posts yet.</p></div><?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>