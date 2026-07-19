<?php
include '../config.php'; 
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// Fetch user data
$user_info_query = mysqli_query($conn, "SELECT * FROM users WHERE id='$current_user_id'");
$user_res = mysqli_fetch_assoc($user_info_query);

$my_pic = ($user_res['profile_pic'] != 'default.png') ? "../" . $user_res['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($_SESSION['user_name'])."&background=random";

// Handle Post Submission
if(isset($_POST['submit_post'])){
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    if(!empty($content)){
        mysqli_query($conn, "INSERT INTO posts (user_id, content) VALUES ('$current_user_id', '$content')");
        header("Location: dashboard.php"); 
        exit();
    }
}

// Handle Comment Submission
if(isset($_POST['submit_comment'])){
    $post_id = $_POST['post_id'];
    $comment_text = mysqli_real_escape_string($conn, $_POST['comment_text']);
    if(!empty($comment_text)){
        mysqli_query($conn, "INSERT INTO comments (post_id, user_id, comment_text) VALUES ('$post_id', '$current_user_id', '$comment_text')");
        header("Location: dashboard.php");
        exit();
    }
}

// Fetch Feed Posts
$posts_query = "SELECT posts.*, users.full_name, users.dept, users.role, users.profile_pic 
                FROM posts 
                JOIN users ON posts.user_id = users.id 
                ORDER BY posts.created_at DESC";
$all_posts = mysqli_query($conn, $posts_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CampusConnect - Feed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary-color: #0d6efd; --bg-color: #f0f2f5; }
        body { background-color: var(--bg-color); 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding-top: 90px; }
        
        /* Sidebar Styling */
        .sidebar { position: fixed; top: 70px; left: 0; bottom: 0; width: 280px; padding: 20px; overflow-y: auto; background: white; border-right: 1px solid #ddd; z-index: 100; }
        .nav-link { color: #444; font-weight: 500; padding: 12px 15px; border-radius: 10px; transition: 0.3s; margin-bottom: 5px; }
        .nav-link:hover { background-color: #f0f2f5; color: var(--primary-color); transform: translateX(5px); }
        .nav-link.active { background-color: #e7f1ff; color: var(--primary-color); }
        .nav-link i { font-size: 1.2rem; margin-right: 15px; }

        /* Feed Area Styling */
        .main-content { margin-left: 280px; padding-top: 10px; }
        .feed-container { max-width: 680px; margin: 0 auto; }
        .post-card { border-radius: 15px; border: none; box-shadow: 0 1px 2px rgba(0,0,0,0.1); background: white; margin-bottom: 20px; }
        .profile-img-sidebar { width: 80px; height: 80px; object-fit: cover; border: 3px solid var(--primary-color); padding: 2px; }
        
        .post-input-box { border-radius: 25px; background-color: #f0f2f5; border: none; padding: 12px 20px; cursor: pointer; }
        .post-input-box:hover { background-color: #e4e6e9; }

        @media (max-width: 992px) {
            .sidebar { width: 80px; text-align: center; }
            .sidebar span, .sidebar h5, .sidebar p, .sidebar hr { display: none; }
            .main-content { margin-left: 80px; }
            .nav-link i { margin-right: 0; }
        }
    </style>
</head>
<body>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="dashboard.php">
                <i class="bi bi-connectdevelop"></i> CampusConnect
            </a>
            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <img src="<?php echo $my_pic; ?>" class="rounded-circle border border-white" width="35" height="35" role="button" data-bs-toggle="dropdown">
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar Navigation -->
    <div class="sidebar d-none d-md-block">
        <div class="text-center mb-4">
            <img src="<?php echo $my_pic; ?>" class="rounded-circle profile-img-sidebar mb-3">
            <h5 class="fw-bold mb-0"><?php echo $_SESSION['user_name']; ?></h5>
            <p class="text-muted small"><?php echo strtoupper($_SESSION['role']); ?> | <?php echo $_SESSION['dept']; ?></p>
        </div>
        <hr>
        <nav class="nav flex-column">
            <a href="dashboard.php" class="nav-link active">
                <i class="bi bi-house-door-fill"></i> <span>Campus Feed</span>
            </a>
            <a href="../notice/view_notice_list.php" class="nav-link">
                <i class="bi bi-megaphone text-warning"></i> <span>Official Notices</span>
            </a>
            <a href="../lost_found/index.php" class="nav-link">
                <i class="bi bi-search text-info"></i> <span>Lost & Found</span>
            </a>
            <a href="../academic/index.php" class="nav-link">
                <i class="bi bi-book text-success"></i> <span>Academic Hub</span>
            </a>
            <a href="profile.php" class="nav-link">
                <i class="bi bi-gear text-secondary"></i> <span>Settings</span>
            </a>
        </nav>
    </div>

    <!-- Main Feed Content -->
    <div class="main-content">
        <div class="container feed-container">
            
            <!-- Create Post Card -->
            <div class="card p-3 post-card mb-4 shadow-sm">
                <div class="d-flex align-items-center mb-3">
                    <img src="<?php echo $my_pic; ?>" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                    <button class="post-input-box text-muted text-start w-100" data-bs-toggle="modal" data-bs-target="#postModal">
                        What's on your mind, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>?
                    </button>
                </div>
                <div class="d-flex justify-content-around border-top pt-2">
                    <button class="btn btn-link text-decoration-none text-muted fw-bold btn-sm"><i class="bi bi-image text-success"></i> Photo</button>
                    <button class="btn btn-link text-decoration-none text-muted fw-bold btn-sm"><i class="bi bi-tag text-primary"></i> Tag</button>
                    <button class="btn btn-link text-decoration-none text-muted fw-bold btn-sm"><i class="bi bi-emoji-smile text-warning"></i> Feeling</button>
                </div>
            </div>

            <!-- News Feed Posts -->
            <div id="news-feed">
                <?php while($post = mysqli_fetch_assoc($all_posts)): ?>
                    <div class="card post-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <?php $p_pic = ($post['profile_pic'] != 'default.png') ? "../" . $post['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($post['full_name']); ?>
                                <img src="<?php echo $p_pic; ?>" class="rounded-circle me-2 border" width="45" height="45" style="object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 fw-bold"><?php echo $post['full_name']; ?> 
                                        <span class="badge bg-light text-dark border ms-1 fw-normal" style="font-size: 10px;"><?php echo strtoupper($post['role']); ?></span>
                                    </h6>
                                    <small class="text-muted" style="font-size: 12px;"><?php echo date('M d, h:i A', strtotime($post['created_at'])); ?> • <i class="bi bi-globe-americas"></i></small>
                                </div>
                            </div>
                            
                            <p class="card-text" style="font-size: 15px; line-height: 1.5;"><?php echo nl2br($post['content']); ?></p>
                            
                            <div class="d-flex justify-content-between text-muted border-top border-bottom py-2 my-3" style="font-size: 14px;">
                                <span role="button" class="fw-bold"><i class="bi bi-hand-thumbs-up"></i> Like</span>
                                <span role="button" class="fw-bold"><i class="bi bi-chat-left"></i> Comment</span>
                                <span role="button" class="fw-bold"><i class="bi bi-share"></i> Share</span>
                            </div>

                            <!-- Display Comments -->
                            <div class="comments-container">
                                <?php
                                $pid = $post['id'];
                                $comments = mysqli_query($conn, "SELECT comments.*, users.full_name FROM comments JOIN users ON comments.user_id = users.id WHERE post_id='$pid' ORDER BY created_at ASC LIMIT 3");
                                while($comment = mysqli_fetch_assoc($comments)):
                                ?>
                                    <div class="d-flex mb-2">
                                        <div class="bg-light rounded p-2 px-3 shadow-sm" style="font-size: 13px; max-width: 90%;">
                                            <strong class="text-primary d-block" style="font-size: 12px;"><?php echo $comment['full_name']; ?></strong>
                                            <?php echo $comment['comment_text']; ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>

                                <!-- Comment Input -->
                                <form method="POST" class="mt-2 d-flex">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <input type="text" name="comment_text" class="form-control form-control-sm border-0 bg-light rounded-pill me-2" placeholder="Write a comment..." required>
                                    <button name="submit_comment" class="btn btn-sm text-primary"><i class="bi bi-send-fill"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Create Post Modal -->
    <div class="modal fade" id="postModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?php echo $my_pic; ?>" class="rounded-circle me-2" width="40" height="40">
                            <span class="fw-bold"><?php echo $_SESSION['user_name']; ?></span>
                        </div>
                        <textarea name="content" class="form-control border-0 fs-5" rows="4" placeholder="What's on your mind?" required style="resize: none;"></textarea>
                    </div>
                    <div class="modal-footer border-0">
                        <button name="submit_post" class="btn btn-primary w-100 fw-bold py-2">Post to Feed</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>