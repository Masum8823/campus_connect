<?php
include 'config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

$user_info_query = mysqli_query($conn, "SELECT * FROM users WHERE id='$current_user_id'");
$user_res = mysqli_fetch_assoc($user_info_query);

$my_pic = ($user_res['profile_pic'] != 'default.png') ? $user_res['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($_SESSION['user_name'])."&background=random";

if(isset($_POST['submit_post'])){
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    
    if(!empty($content)){
        $query = "INSERT INTO posts (user_id, content) VALUES ('$current_user_id', '$content')";
        mysqli_query($conn, $query);
        header("Location: dashboard.php"); 
        exit();
    }
}

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
    <title>Campus Feed - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .post-card { border-radius: 12px; border: none; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .navbar { position: sticky; top: 0; z-index: 1000; }
        .profile-img { object-fit: cover; border: 2px solid #0d6efd; padding: 2px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">CampusConnect</a>
            <div class="d-flex align-items-center text-white">
                <span class="me-3">Hi, <?php echo $_SESSION['user_name']; ?></span>
                <a href="logout.php" class="btn btn-light btn-sm text-primary fw-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            
            <!-- Left Sidebar (User Profile Card) -->
            <div class="col-md-3">
                <div class="card p-3 post-card text-center">
                    <img src="<?php echo $my_pic; ?>" class="rounded-circle mx-auto mb-3 profile-img" width="100" height="100">
                    <h5 class="mb-0"><?php echo $_SESSION['user_name']; ?></h5>
                    <p class="text-muted small"><?php echo strtoupper($_SESSION['role']); ?><br>Dept: <?php echo $_SESSION['dept']; ?></p>
                    <hr>
                    <a href="profile.php" class="btn btn-outline-primary btn-sm w-100">Update Profile Picture</a>
                </div>
            </div>

            <!-- Middle Section (Feed) -->
            <div class="col-md-6">
                <!-- Post Box -->
                <div class="card p-3 post-card">
                    <form method="POST">
                        <textarea name="content" class="form-control mb-2" rows="3" placeholder="What's on your mind, <?php echo $_SESSION['user_name']; ?>?" required></textarea>
                        <button name="submit_post" class="btn btn-primary w-100 fw-bold">Post to Feed</button>
                    </form>
                </div>

                <h5 class="mb-3 text-secondary">Campus Activity</h5>
                
                <?php while($post = mysqli_fetch_assoc($all_posts)): ?>
                    <div class="card p-3 post-card">
                        <div class="d-flex align-items-center mb-3">
                            <?php 
                                $post_user_pic = ($post['profile_pic'] != 'default.png') ? $post['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($post['full_name'])."&background=random";
                            ?>
                            <img src="<?php echo $post_user_pic; ?>" class="rounded-circle me-2 profile-img" width="45" height="45">
                            
                            <div>
                                <h6 class="mb-0"><?php echo $post['full_name']; ?> 
                                    <small class="badge bg-light text-dark border ms-1" style="font-size: 10px;"><?php echo $post['role']; ?></small>
                                </h6>
                                <small class="text-muted"><?php echo date('M d, h:i A', strtotime($post['created_at'])); ?> | <?php echo $post['dept']; ?></small>
                            </div>
                        </div>
                        
                        <p class="card-text" style="font-size: 1.05rem;"><?php echo nl2br($post['content']); ?></p>
                        
                        <hr class="text-muted">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                <small class="me-3" style="cursor:pointer;">Like (soon)</small>
                                <small style="cursor:pointer;">Comment (soon)</small>
                            </div>

                            <?php if($post['user_id'] == $_SESSION['user_id']): ?>
                                <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this post?')">
                                    Delete
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Right Sidebar (Notices) -->
            <div class="col-md-3">
                <div class="card p-3 post-card">
                    <h6 class="fw-bold"><i class="text-primary">●</i> Upcoming Events / Notices</h6>
                    <hr>
                    <p class="text-muted small">No official notices yet. Please check back later.</p>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>