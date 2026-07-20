<?php
include '../config.php'; 
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// Fetch user info
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusConnect - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --sidebar-width: 280px;
            --bg-light: #f0f2f5;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            padding-top: 70px;
        }

        /* --- Navbar & Dropdown Styling --- */
        .navbar { background-color: var(--primary-color) !important; }
        
        .dropdown-menu {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            padding: 10px;
            min-width: 230px;
        }

        .dropdown-header-custom {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 10px 15px;
            font-weight: 500;
            color: #4b4f56;
            transition: 0.2s;
        }

        .dropdown-item:hover {
            background-color: #f0f2f5;
            color: var(--primary-color);
        }

        .dropdown-item i {
            font-size: 1.1rem;
            margin-right: 10px;
        }

        /* Avatar Styling */
        .nav-profile-img {
            width: 38px;
            height: 38px;
            object-fit: cover;
            border: 2px solid white;
            cursor: pointer;
            transition: 0.2s;
        }
        .nav-profile-img:hover { opacity: 0.9; transform: scale(1.05); }

        /* Sidebar & Post styling stays same as before */
        .sidebar { position: fixed; top: 70px; left: 0; bottom: 0; width: var(--sidebar-width); background: white; padding: 20px; border-right: 1px solid #dee2e6; overflow-y: auto; z-index: 1000; }
        .nav-link { display: flex; align-items: center; padding: 12px 15px; color: #4b4f56; font-weight: 500; border-radius: 10px; margin-bottom: 5px; transition: all 0.2s; }
        .nav-link:hover { background-color: #f2f2f2; color: var(--primary-color); }
        .nav-link.active { background-color: #e7f3ff; color: var(--primary-color); }
        .nav-link i { font-size: 1.3rem; margin-right: 12px; }
        .main-content { margin-left: var(--sidebar-width); padding: 20px; }
        .feed-container { max-width: 680px; margin: 0 auto; }
        .post-card { background: white; border-radius: 12px; border: none; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 20px; overflow: hidden; }
        .post-input-box { background: #f0f2f5; border-radius: 25px; padding: 10px 20px; cursor: pointer; border: none; width: 100%; text-align: left; color: #65676b; }
        .avatar-md { width: 45px; height: 45px; object-fit: cover; border-radius: 50%; }

        @media (max-width: 992px) {
            .sidebar { width: 85px; }
            .sidebar span, .sidebar h6, .sidebar p, .sidebar hr { display: none; }
            .main-content { margin-left: 85px; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="dashboard.php">
                <i class="bi bi-connectdevelop"></i> CampusConnect
            </a>
            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <!-- Updated Nav Profile Avatar -->
                    <img src="<?php echo $my_pic; ?>" class="rounded-circle nav-profile-img" data-bs-toggle="dropdown">
                    
                    <!-- Modernized Dropdown Menu -->
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 animate slideIn">
                        <div class="dropdown-header-custom">
                            <h6 class="fw-bold mb-0 text-dark"><?php echo $_SESSION['user_name']; ?></h6>
                            <small class="text-muted"><?php echo $_SESSION['dept']; ?> | <?php echo ucfirst($_SESSION['role']); ?></small>
                        </div>
                        
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="profile.php">
                                <i class="bi bi-person-circle text-primary"></i>
                                <span>My Profile</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="edit_profile.php">
                                <i class="bi bi-gear text-secondary"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center text-danger" href="../auth/logout.php">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Log Out</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="text-center mb-4">
            <a href="profile.php">
                <img src="<?php echo $my_pic; ?>" class="rounded-circle border border-3 border-primary mb-2" width="80" height="80" style="object-fit: cover;">
            </a>
            <h6 class="fw-bold mb-0 text-dark"><?php echo $_SESSION['user_name']; ?></h6>
            <p class="text-muted small"><?php echo strtoupper($_SESSION['role']); ?> | <?php echo $_SESSION['dept']; ?></p>
        </div>
        <hr>
        <nav class="nav flex-column">
            <a href="dashboard.php" class="nav-link active"><i class="bi bi-house-door-fill text-primary"></i> <span>Campus Feed</span></a>
            <a href="../notice/view_notice_list.php" class="nav-link"><i class="bi bi-megaphone text-warning"></i> <span>Notices</span></a>
            <a href="../lost_found/index.php" class="nav-link"><i class="bi bi-search text-info"></i> <span>Lost & Found</span></a>
            <a href="../academic/index.php" class="nav-link"><i class="bi bi-mortarboard text-success"></i> <span>Academic Hub</span></a>
            <a href="requests.php" class="nav-link"><i class="bi bi-person-plus text-danger"></i> <span>Requests</span></a>
            <a href="my_connections.php" class="nav-link"><i class="bi bi-people text-primary"></i> <span>Network</span></a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="feed-container">
            
            <!-- Create Post -->
            <div class="card post-card p-3 mb-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="<?php echo $my_pic; ?>" class="avatar-md me-2">
                    <button class="post-input-box" data-bs-toggle="modal" data-bs-target="#postModal">
                        What's on your mind, <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>?
                    </button>
                </div>
                <div class="d-flex border-top pt-2">
                    <div class="col text-center"><button class="btn btn-link text-decoration-none text-muted fw-bold btn-sm"><i class="bi bi-image text-success me-1"></i> Photo</button></div>
                    <div class="col text-center"><button class="btn btn-link text-decoration-none text-muted fw-bold btn-sm"><i class="bi bi-tag text-primary me-1"></i> Tag</button></div>
                    <div class="col text-center"><button class="btn btn-link text-decoration-none text-muted fw-bold btn-sm"><i class="bi bi-emoji-smile text-warning me-1"></i> Feeling</button></div>
                </div>
            </div>

            <!-- Feed Posts (Like logic merged) -->
            <?php while($post = mysqli_fetch_assoc($all_posts)): 
                $pid = $post['id'];
                $like_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM likes WHERE post_id='$pid'");
                $total_likes = mysqli_fetch_assoc($like_res)['total'];
                $is_liked = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM likes WHERE post_id='$pid' AND user_id='$current_user_id'")) > 0;
                $c_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM comments WHERE post_id='$pid'");
                $total_comments = mysqli_fetch_assoc($c_res)['total'];
            ?>
                <div class="card post-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-3">
                            <a href="profile.php?id=<?php echo $post['user_id']; ?>">
                                <?php $p_pic = ($post['profile_pic'] != 'default.png') ? "../" . $post['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($post['full_name']); ?>
                                <img src="<?php echo $p_pic; ?>" class="avatar-md me-2 border">
                            </a>
                            <div>
                                <h6 class="mb-0 fw-bold">
                                    <a href="profile.php?id=<?php echo $post['user_id']; ?>" class="text-decoration-none text-dark"><?php echo $post['full_name']; ?></a>
                                    <span class="badge bg-light text-dark border fw-normal ms-1" style="font-size: 10px;"><?php echo strtoupper($post['role']); ?></span>
                                </h6>
                                <small class="text-muted" style="font-size: 11px;"><?php echo date('M d, h:i A', strtotime($post['created_at'])); ?> • <i class="bi bi-globe"></i></small>
                            </div>
                        </div>
                        <p class="card-text px-1" style="font-size: 15px;"><?php echo nl2br($post['content']); ?></p>
                        
                        <div class="d-flex justify-content-around border-top border-bottom py-2 my-3">
                            <a href="toggle_like.php?post_id=<?php echo $pid; ?>" class="btn btn-link text-decoration-none fw-bold btn-sm p-0 <?php echo $is_liked ? 'text-primary' : 'text-muted'; ?>">
                                <i class="bi <?php echo $is_liked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up'; ?> me-1"></i> <?php echo $total_likes; ?> Like
                            </a>
                            <a href="../post/view_post.php?id=<?php echo $post['id']; ?>" class="btn btn-link text-decoration-none text-muted fw-bold btn-sm p-0">
                                <i class="bi bi-chat-left me-1"></i> Comment
                            </a>
                            <button class="btn btn-link text-decoration-none text-muted fw-bold btn-sm p-0"><i class="bi bi-share me-1"></i> Share</button>
                        </div>
                        <a href="../post/view_post.php?id=<?php echo $pid; ?>" class="text-decoration-none text-muted small fw-bold px-1">
                            <?php echo ($total_comments > 0) ? "View all $total_comments comments" : "Be the first to comment"; ?>
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
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Create Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <textarea name="content" class="form-control border-0 fs-5" rows="4" placeholder="What's on your mind?" required style="resize: none;"></textarea>
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