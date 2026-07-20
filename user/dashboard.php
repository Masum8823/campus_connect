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
        body { background-color: var(--bg-color); font-family: 'Segoe UI', Tahoma, sans-serif; padding-top: 80px; }
        
        .sidebar { position: fixed; top: 70px; left: 0; bottom: 0; width: 280px; padding: 20px; overflow-y: auto; background: white; border-right: 1px solid #ddd; z-index: 100; }
        .nav-link { color: #444; font-weight: 500; padding: 12px 15px; border-radius: 10px; transition: 0.3s; margin-bottom: 5px; }
        .nav-link:hover { background-color: #f0f2f5; color: var(--primary-color); transform: translateX(5px); }
        .nav-link.active { background-color: #e7f1ff; color: var(--primary-color); }
        .nav-link i { font-size: 1.2rem; margin-right: 15px; }

        .main-content { margin-left: 280px; padding-top: 10px; }
        .feed-container { max-width: 680px; margin: 0 auto; }
        .post-card { border-radius: 15px; border: none; box-shadow: 0 1px 2px rgba(0,0,0,0.1); background: white; margin-bottom: 20px; overflow: hidden; }
        .profile-img-sidebar { width: 80px; height: 80px; object-fit: cover; border: 3px solid var(--primary-color); padding: 2px; }
        .post-input-box { border-radius: 25px; background-color: #f0f2f5; border: none; padding: 12px 20px; cursor: pointer; text-align: left; }
        
        @media (max-width: 992px) {
            .sidebar { width: 80px; text-align: center; }
            .sidebar span, .sidebar h5, .sidebar p, .sidebar hr { display: none; }
            .main-content { margin-left: 80px; }
        }
    </style>
</head>
<body>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="dashboard.php">CampusConnect</a>
            <div class="ms-auto">
                <div class="dropdown">
                    <img src="<?php echo $my_pic; ?>" class="rounded-circle border border-white" width="35" height="35" role="button" data-bs-toggle="dropdown">
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../auth/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar d-none d-md-block shadow-sm">
        <div class="text-center mb-4">
            <a href="profile.php"><img src="<?php echo $my_pic; ?>" class="rounded-circle profile-img-sidebar mb-3"></a>
            <h6 class="fw-bold mb-0"><?php echo $_SESSION['user_name']; ?></h6>
            <p class="text-muted small"><?php echo strtoupper($_SESSION['role']); ?> | <?php echo $_SESSION['dept']; ?></p>
        </div>
        <hr>
        <nav class="nav flex-column">
            <a href="dashboard.php" class="nav-link active"><i class="bi bi-house-door-fill"></i> <span>Campus Feed</span></a>
            <a href="../notice/view_notice_list.php" class="nav-link"><i class="bi bi-megaphone text-warning"></i> <span>Official Notices</span></a>
            <a href="../lost_found/index.php" class="nav-link"><i class="bi bi-search text-info"></i> <span>Lost & Found</span></a>
            <a href="../academic/index.php" class="nav-link"><i class="bi bi-book text-success"></i> <span>Academic Hub</span></a>
            <a href="requests.php" class="nav-link"><i class="bi bi-person-plus text-danger"></i> <span>Requests</span></a>
            <a href="my_connections.php" class="nav-link"><i class="bi bi-people text-primary"></i> <span>Network</span></a>
        </nav>
    </div>

    <!-- Main Feed -->
    <div class="main-content">
        <div class="container feed-container">
            
            <!-- Create Post Card -->
            <div class="card p-3 post-card mb-4">
                <div class="d-flex align-items-center">
                    <img src="<?php echo $my_pic; ?>" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                    <button class="post-input-box text-muted w-100 btn btn-light" data-bs-toggle="modal" data-bs-target="#postModal">
                        What's on your mind, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>?
                    </button>
                </div>
            </div>

            <!-- Posts Feed Loop -->
            <?php while($post = mysqli_fetch_assoc($all_posts)): ?>
                <div class="card post-card shadow-sm">
                    <div class="card-body">
                        <!-- Post Header -->
                        <div class="d-flex align-items-center mb-3">
                            <a href="profile.php?id=<?php echo $post['user_id']; ?>">
                                <?php $p_pic = ($post['profile_pic'] != 'default.png') ? "../" . $post['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($post['full_name']); ?>
                                <img src="<?php echo $p_pic; ?>" class="rounded-circle me-2 border" width="45" height="45" style="object-fit: cover;">
                            </a>
                            <div>
                                <h6 class="mb-0 fw-bold">
                                    <a href="profile.php?id=<?php echo $post['user_id']; ?>" class="text-decoration-none text-dark"><?php echo $post['full_name']; ?></a>
                                    <span class="badge bg-light text-dark border fw-normal" style="font-size: 9px;"><?php echo strtoupper($post['role']); ?></span>
                                </h6>
                                <small class="text-muted" style="font-size: 11px;"><?php echo date('M d, h:i A', strtotime($post['created_at'])); ?> • <i class="bi bi-globe"></i></small>
                            </div>
                        </div>

                        <!-- Post Content -->
                        <p class="card-text mb-3" style="font-size: 15px;"><?php echo nl2br($post['content']); ?></p>
                        
                        <!-- Interaction Buttons -->
                        <div class="d-flex justify-content-around text-muted border-top border-bottom py-2" style="font-size: 14px;">
                            <span role="button" class="fw-bold"><i class="bi bi-hand-thumbs-up"></i> Like</span>
                            <a href="../post/view_post.php?id=<?php echo $post['id']; ?>" class="text-decoration-none text-muted fw-bold">
                                <i class="bi bi-chat-left"></i> Comment
                            </a>
                        </div>

                        <!-- Comment Count & View Link -->
                        <div class="mt-2 px-1">
                            <?php
                            $pid = $post['id'];
                            $count_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM comments WHERE post_id='$pid'");
                            $count_data = mysqli_fetch_assoc($count_res);
                            $total_comments = $count_data['total'];
                            ?>
                            <a href="../post/view_post.php?id=<?php echo $pid; ?>" class="text-decoration-none text-muted small fw-bold">
                                <?php if($total_comments > 0): ?>
                                    View all <?php echo $total_comments; ?> comments
                                <?php else: ?>
                                    Write a comment...
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Post Modal -->
    <div class="modal fade" id="postModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Create Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
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