<?php
include '../config.php'; 
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// 1. Fetch user info
$user_info_query = mysqli_query($conn, "SELECT * FROM users WHERE id='$current_user_id'");
$user_res = mysqli_fetch_assoc($user_info_query);
$my_pic = ($user_res['profile_pic'] != 'default.png') ? "../" . $user_res['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($_SESSION['user_name'])."&background=random";

// 2. Handle Post Submission (Text + Image)
if(isset($_POST['submit_post'])){
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $post_image = NULL;

    // Image Upload Logic
    if(!empty($_FILES['post_img']['name'])){
        $img_name = time() . "_" . $_FILES['post_img']['name'];
        $target = "../uploads/posts/" . $img_name;
        $db_path = "uploads/posts/" . $img_name;

        if (!file_exists('../uploads/posts')) { mkdir('../uploads/posts', 0777, true); }

        if(move_uploaded_file($_FILES['post_img']['tmp_name'], $target)){
            $post_image = $db_path;
        }
    }

    if(!empty($content) || $post_image){
        $query = "INSERT INTO posts (user_id, content, post_image) VALUES ('$current_user_id', '$content', " . ($post_image ? "'$post_image'" : "NULL") . ")";
        mysqli_query($conn, $query);
        header("Location: dashboard.php"); 
        exit();
    }
}

// 3. Handle Comment Submission
if(isset($_POST['submit_comment'])){
    $post_id = $_POST['post_id'];
    $comment_text = mysqli_real_escape_string($conn, $_POST['comment_text']);
    if(!empty($comment_text)){
        mysqli_query($conn, "INSERT INTO comments (post_id, user_id, comment_text) VALUES ('$post_id', '$current_user_id', '$comment_text')");
        header("Location: dashboard.php");
        exit();
    }
}

// 4. Fetch Feed Posts
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusConnect - Feed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { --primary-color: #0d6efd; --sidebar-width: 280px; --bg-light: #f0f2f5; }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        
        /* Sidebar Navigation */
        .sidebar { position: fixed; top: 70px; left: 0; bottom: 0; width: var(--sidebar-width); background: white; padding: 20px; border-right: 1px solid #dee2e6; overflow-y: auto; z-index: 1000; }
        .nav-link { display: flex; align-items: center; padding: 12px 15px; color: #4b4f56; font-weight: 500; border-radius: 10px; margin-bottom: 5px; transition: 0.2s; }
        .nav-link:hover { background-color: #f2f2f2; color: var(--primary-color); }
        .nav-link.active { background-color: #e7f3ff; color: var(--primary-color); }
        .nav-link i { font-size: 1.3rem; margin-right: 12px; }

        /* Content Area */
        .main-content { margin-left: var(--sidebar-width); padding: 20px; }
        .feed-container { max-width: 680px; margin: 0 auto; }
        
        /* Cards */
        .post-card { background: white; border-radius: 15px; border: none; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 20px; overflow: hidden; }
        .post-input-box { background: #f0f2f5; border-radius: 25px; padding: 12px 20px; cursor: pointer; border: none; width: 100%; text-align: left; color: #65676b; }
        .post-img-display { width: 100%; object-fit: cover; max-height: 500px; border-radius: 8px; margin-top: 10px; border: 1px solid #eee; }
        
        /* Dropdown */
        .nav-profile-img { width: 35px; height: 35px; object-fit: cover; border: 2px solid white; cursor: pointer; }
        .dropdown-menu { border-radius: 12px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1); padding: 10px; }

        @media (max-width: 992px) {
            .sidebar { width: 85px; }
            .sidebar span, .sidebar h6, .sidebar p, .sidebar hr { display: none; }
            .main-content { margin-left: 85px; }
        }
    </style>
</head>
<body>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="dashboard.php"><i class="bi bi-connectdevelop"></i> CampusConnect</a>
            <div class="ms-auto">
                <div class="dropdown">
                    <img src="<?php echo $my_pic; ?>" class="rounded-circle nav-profile-img" data-bs-toggle="dropdown">
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <div class="px-3 py-2 border-bottom">
                            <h6 class="fw-bold mb-0 small"><?php echo $_SESSION['user_name']; ?></h6>
                            <small class="text-muted"><?php echo $_SESSION['dept']; ?></small>
                        </div>
                        <li><a class="dropdown-item mt-2" href="profile.php"><i class="bi bi-person me-2"></i> My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Fixed Sidebar -->
    <div class="sidebar d-none d-md-block shadow-sm">
        <div class="text-center mb-4">
            <a href="profile.php"><img src="<?php echo $my_pic; ?>" class="rounded-circle border border-3 border-primary mb-2" width="80" height="80" style="object-fit: cover;"></a>
            <h6 class="fw-bold mb-0"><?php echo $_SESSION['user_name']; ?></h6>
            <p class="text-muted small"><?php echo strtoupper($_SESSION['role']); ?> | <?php echo $_SESSION['dept']; ?></p>
        </div>
        <hr>
        <nav class="nav flex-column">
            <a href="dashboard.php" class="nav-link active"><i class="bi bi-house-door-fill"></i> <span>Campus Feed</span></a>
            <a href="../notice/view_notice_list.php" class="nav-link"><i class="bi bi-megaphone text-warning"></i> <span>Notices</span></a>
            <a href="../lost_found/index.php" class="nav-link"><i class="bi bi-search text-info"></i> <span>Lost & Found</span></a>
            <a href="../academic/index.php" class="nav-link"><i class="bi bi-mortarboard text-success"></i> <span>Academic Hub</span></a>
            <a href="requests.php" class="nav-link"><i class="bi bi-person-plus text-danger"></i> <span>Requests</span></a>
            <a href="my_connections.php" class="nav-link"><i class="bi bi-people text-primary"></i> <span>Network</span></a>
            <a href="../alumni/index.php" class="nav-link"><i class="bi bi-award-fill text-dark"></i> <span>Alumni Hub</span></a>
        </nav>
    </div>

    <!-- News Feed Content -->
    <div class="main-content">
        <div class="feed-container">
            
            <!-- Create Post Card -->
            <div class="card post-card p-3 mb-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="<?php echo $my_pic; ?>" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                    <button class="post-input-box" data-bs-toggle="modal" data-bs-target="#postModal">
                        What's on your mind, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>?
                    </button>
                </div>
                <div class="d-flex border-top pt-2">
                    <button class="btn btn-link text-decoration-none text-muted fw-bold btn-sm w-100" data-bs-toggle="modal" data-bs-target="#postModal">
                        <i class="bi bi-image text-success me-1"></i> Add Photo to post
                    </button>
                </div>
            </div>

            <!-- Posts Feed Loop -->
            <?php while($post = mysqli_fetch_assoc($all_posts)): 
                $pid = $post['id'];
                $is_liked = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM likes WHERE post_id='$pid' AND user_id='$current_user_id'")) > 0;
                $total_likes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM likes WHERE post_id='$pid'"))['total'];
                $total_comments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM comments WHERE post_id='$pid'"))['total'];
            ?>
                <div class="card post-card shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-3">
                            <a href="profile.php?id=<?php echo $post['user_id']; ?>">
                                <?php $p_pic = ($post['profile_pic'] != 'default.png') ? "../" . $post['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($post['full_name']); ?>
                                <img src="<?php echo $p_pic; ?>" class="rounded-circle me-2 border" width="45" height="45" style="object-fit: cover;">
                            </a>
                            <div>
                                <h6 class="mb-0 fw-bold small">
                                    <a href="profile.php?id=<?php echo $post['user_id']; ?>" class="text-decoration-none text-dark"><?php echo $post['full_name']; ?></a>
                                    <span class="badge bg-light text-dark border fw-normal ms-1" style="font-size: 9px;"><?php echo strtoupper($post['role']); ?></span>
                                </h6>
                                <small class="text-muted" style="font-size: 11px;"><?php echo date('M d, h:i A', strtotime($post['created_at'])); ?> • <i class="bi bi-globe"></i></small>
                            </div>
                        </div>

                        <p class="card-text px-1 mb-2" style="font-size: 15px;"><?php echo nl2br($post['content']); ?></p>

                        <!-- Display Post Image -->
                        <?php if(!empty($post['post_image'])): ?>
                            <div class="post-image-container">
                                <img src="../<?php echo $post['post_image']; ?>" class="post-img-display shadow-sm">
                            </div>
                        <?php endif; ?>
                        
                        <!-- Like & Comment Buttons -->
                        <div class="d-flex justify-content-around border-top border-bottom py-2 mt-3 mb-2">
                            <a href="toggle_like.php?post_id=<?php echo $pid; ?>" class="btn btn-link text-decoration-none fw-bold btn-sm <?php echo $is_liked ? 'text-primary' : 'text-muted'; ?>">
                                <i class="bi <?php echo $is_liked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up'; ?> me-1"></i> <?php echo $total_likes; ?> Like
                            </a>
                            <a href="../post/view_post.php?id=<?php echo $pid; ?>" class="btn btn-link text-decoration-none text-muted fw-bold btn-sm">
                                <i class="bi bi-chat-left me-1"></i> <?php echo $total_comments; ?> Comment
                            </a>
                        </div>

                        <!-- View Comments Count -->
                        <a href="../post/view_post.php?id=<?php echo $pid; ?>" class="text-decoration-none text-muted small fw-bold px-1">
                            <?php echo ($total_comments > 0) ? "View all ".$total_comments." comments" : "Write a comment..."; ?>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Create Post Modal -->
    <div class="modal fade" id="postModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Create Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <textarea name="content" class="form-control border-0 fs-5 mb-3" rows="4" placeholder="What's on your mind, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>?" style="resize: none;"></textarea>
                        
                        <div class="p-3 border rounded bg-light">
                            <label class="form-label small fw-bold text-muted"><i class="bi bi-image text-success"></i> Add a photo to your post</label>
                            <input type="file" name="post_img" class="form-control form-control-sm" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button name="submit_post" class="btn btn-primary w-100 fw-bold py-2 shadow">Post to Feed</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>