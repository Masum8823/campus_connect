<?php
// 1. Path to root config
include '../config.php'; 
session_start();

// 2. Auth Check
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// 3. Fetch user data
$user_info_query = mysqli_query($conn, "SELECT * FROM users WHERE id='$current_user_id'");
$user_res = mysqli_fetch_assoc($user_info_query);

// 4. Profile Picture Logic
$my_pic = ($user_res['profile_pic'] != 'default.png') ? "../" . $user_res['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($_SESSION['user_name'])."&background=random";

// 5. Handle Post Submission
if(isset($_POST['submit_post'])){
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    if(!empty($content)){
        mysqli_query($conn, "INSERT INTO posts (user_id, content) VALUES ('$current_user_id', '$content')");
        header("Location: dashboard.php"); 
        exit();
    }
}

// 6. Handle Comment Submission
if(isset($_POST['submit_comment'])){
    $post_id = $_POST['post_id'];
    $comment_text = mysqli_real_escape_string($conn, $_POST['comment_text']);
    
    if(!empty($comment_text)){
        mysqli_query($conn, "INSERT INTO comments (post_id, user_id, comment_text) VALUES ('$post_id', '$current_user_id', '$comment_text')");
        header("Location: dashboard.php");
        exit();
    }
}

// 7. Fetch Feed Posts
$posts_query = "SELECT posts.*, users.full_name, users.dept, users.role, users.profile_pic 
                FROM posts 
                JOIN users ON posts.user_id = users.id 
                ORDER BY posts.created_at DESC";
$all_posts = mysqli_query($conn, $posts_query);

// 8. Fetch Notices
$notices_query = mysqli_query($conn, "SELECT * FROM notices ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CampusConnect - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; }
        .post-card { border-radius: 12px; border: none; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .profile-img { object-fit: cover; border: 2px solid #0d6efd; padding: 2px; }
        .comment-box { background-color: #f8f9fa; border-radius: 8px; padding: 8px; margin-bottom: 5px; }
        .sidebar-link { transition: all 0.2s; }
        .sidebar-link:hover { background-color: #e9ecef; transform: translateX(5px); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">CampusConnect</a>
            <div class="d-flex align-items-center text-white">
                <span class="me-3 small">Hi, <?php echo $_SESSION['user_name']; ?></span>
                <a href="../auth/logout.php" class="btn btn-light btn-sm text-primary fw-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            
            <!-- Left Sidebar -->
            <div class="col-md-3">
                <!-- User Profile Card -->
                <div class="card p-3 post-card text-center shadow-sm">
                    <img src="<?php echo $my_pic; ?>" class="rounded-circle mx-auto mb-3 profile-img" width="100" height="100">
                    <h5 class="mb-0 small fw-bold"><?php echo $_SESSION['user_name']; ?></h5>
                    <p class="text-muted small"><?php echo strtoupper($_SESSION['role']); ?> | <?php echo $_SESSION['dept']; ?></p>
                    <hr>
                    <a href="profile.php" class="btn btn-outline-primary btn-sm w-100 mb-2">Update Profile Picture</a>
                    
                    <a href="../lost_found/index.php" class="btn btn-info btn-sm w-100 text-white fw-bold shadow-sm">
                        <i class="bi bi-search"></i> Lost & Found Section
                    </a>
                </div>

                <!-- NEW: Academic Hub Section -->
                <div class="card p-3 post-card shadow-sm mt-3">
                    <h6 class="fw-bold text-primary mb-3" style="font-size: 14px;"><i class="bi bi-book-half"></i> Academic Hub</h6>
                    <div class="d-grid gap-2">
                        <a href="../academic/index.php" class="btn btn-light btn-sm text-start sidebar-link border">
                            <i class="bi bi-calendar-check text-primary"></i> Routines & Materials
                        </a>
                        <a href="../academic/assignments.php" class="btn btn-light btn-sm text-start sidebar-link border">
                            <i class="bi bi-file-earmark-text text-danger"></i> Assignments
                        </a>
                        <a href="../academic/gpa_calculator.php" class="btn btn-light btn-sm text-start sidebar-link border">
                            <i class="bi bi-calculator text-success"></i> GPA Calculator
                        </a>
                    </div>
                </div>
            </div>

            <!-- Middle Feed -->
            <div class="col-md-6">
                <div class="card p-3 post-card">
                    <form method="POST">
                        <textarea name="content" class="form-control mb-2 border-0 bg-light" rows="3" placeholder="What's on your mind, <?php echo $_SESSION['user_name']; ?>?" required></textarea>
                        <button name="submit_post" class="btn btn-primary w-100 fw-bold shadow-sm">Post to Feed</button>
                    </form>
                </div>

                <h5 class="mb-3 text-secondary border-bottom pb-2" style="font-size: 16px;">Campus Activity</h5>
                
                <?php while($post = mysqli_fetch_assoc($all_posts)): ?>
                    <div class="card p-3 post-card">
                        <div class="d-flex align-items-center mb-3">
                            <?php 
                            $p_pic = ($post['profile_pic'] != 'default.png') ? "../" . $post['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($post['full_name']); 
                            ?>
                            <img src="<?php echo $p_pic; ?>" class="rounded-circle me-2 profile-img" width="45" height="45">
                            <div>
                                <h6 class="mb-0 small fw-bold"><?php echo $post['full_name']; ?> 
                                    <small class="badge bg-light text-dark border ms-1" style="font-size: 9px;"><?php echo strtoupper($post['role']); ?></small>
                                </h6>
                                <small class="text-muted" style="font-size: 11px;"><?php echo date('M d, h:i A', strtotime($post['created_at'])); ?> | <?php echo $post['dept']; ?></small>
                            </div>
                        </div>
                        
                        <p class="card-text small"><?php echo nl2br($post['content']); ?></p>
                        
                        <hr class="my-2 text-muted">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="text-muted">
                                <small class="me-3" style="cursor:pointer;"><i class="bi bi-hand-thumbs-up"></i> Like</small>
                                <small style="cursor:pointer;"><i class="bi bi-chat-left"></i> Comments</small>
                            </div>
                            <?php if($post['user_id'] == $_SESSION['user_id']): ?>
                                <div>
                                    <a href="../post/edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 11px;">Edit</a>
                                    <a href="../post/delete_post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size: 11px;" onclick="return confirm('Delete post?')">Delete</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Comments -->
                        <div class="p-2 border-top bg-light rounded shadow-sm">
                            <?php
                            $current_post_id = $post['id'];
                            $comments_query = mysqli_query($conn, "SELECT comments.*, users.full_name FROM comments JOIN users ON comments.user_id = users.id WHERE post_id='$current_post_id' ORDER BY created_at ASC");
                            while($comment = mysqli_fetch_assoc($comments_query)):
                            ?>
                                <div class="comment-box">
                                    <strong class="text-primary" style="font-size: 11px;"><?php echo $comment['full_name']; ?>:</strong>
                                    <span style="font-size: 11px;"><?php echo $comment['comment_text']; ?></span>
                                </div>
                            <?php endwhile; ?>

                            <form method="POST" class="mt-2 d-flex">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <input type="text" name="comment_text" class="form-control form-control-sm me-2 border-0 shadow-sm" placeholder="Write a comment..." required>
                                <button name="submit_comment" class="btn btn-primary btn-sm"><i class="bi bi-send"></i></button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Right Sidebar -->
            <div class="col-md-3">
                <div class="card p-3 post-card shadow-sm sticky-top" style="top: 80px;">
                    <h6 class="fw-bold text-primary small"><i class="bi bi-megaphone"></i> Official Notices</h6>
                    <hr class="mt-1">
                    <?php if(mysqli_num_rows($notices_query) > 0): ?>
                        <?php while($notice = mysqli_fetch_assoc($notices_query)): ?>
                            <div class="mb-3 border-bottom pb-2">
                                <h6 class="mb-1 fw-bold" style="font-size: 13px;"><?php echo $notice['title']; ?></h6>
                                <small class="text-muted d-block mb-1" style="font-size: 10px;"><?php echo date('M d, Y', strtotime($notice['created_at'])); ?></small>
                                <div class="d-flex justify-content-between">
                                    <a href="../notice/view_notice.php?id=<?php echo $notice['id']; ?>" class="text-decoration-none small fw-bold" style="font-size: 11px;">Read More →</a>
                                    <?php if(($_SESSION['role'] == 'teacher' || $_SESSION['role'] == 'admin') && $notice['user_id'] == $_SESSION['user_id']): ?>
                                        <a href="../notice/delete_notice.php?id=<?php echo $notice['id']; ?>" class="text-danger" style="font-size: 11px;" onclick="return confirm('Delete notice?')">Delete</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted small text-center italic">No notices yet.</p>
                    <?php endif; ?>

                    <?php if($_SESSION['role'] == 'teacher' || $_SESSION['role'] == 'admin'): ?>
                        <a href="../notice/add_notice.php" class="btn btn-sm btn-primary w-100 mt-2 shadow-sm">Post New Notice</a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</body>
</html>