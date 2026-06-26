<?php
include 'config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

if(isset($_POST['submit_post'])){
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    
    if(!empty($content)){
        $query = "INSERT INTO posts (user_id, content) VALUES ('$current_user_id', '$content')";
        mysqli_query($conn, $query);
        header("Location: dashboard.php"); // পেজ রিফ্রেশ করে পোস্ট দেখানোর জন্য
    }
}

$posts_query = "SELECT posts.*, users.full_name, users.dept, users.role 
                FROM posts 
                JOIN users ON posts.user_id = users.id 
                ORDER BY posts.created_at DESC";
$all_posts = mysqli_query($conn, $posts_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Campus Feed - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .post-card { border-radius: 10px; border: none; margin-bottom: 20px; }
        .navbar { sticky-top; z-index: 1000; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">CampusConnect</a>
            <div class="d-flex align-items-center text-white">
                <span class="me-3">Hi, <?php echo $_SESSION['user_name']; ?></span>
                <a href="logout.php" class="btn btn-light btn-sm text-primary fw-bold">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            
            <!-- Left Sidebar (User Info) -->
            <div class="col-md-3">
                <div class="card p-3 post-card shadow-sm text-center">
                    <img src="https://ui-avatars.com/api/?name=<?php echo $_SESSION['user_name']; ?>&background=random" class="rounded-circle mx-auto mb-2" width="80">
                    <h5><?php echo $_SESSION['user_name']; ?></h5>
                    <p class="text-muted"><?php echo strtoupper($_SESSION['role']); ?><br>Dept: <?php echo $_SESSION['dept'] ?? 'N/A'; ?></p>
                </div>
            </div>

            <!-- Middle Section (Feed) -->
            <div class="col-md-6">
                <!-- Post Box -->
                <div class="card p-3 post-card shadow-sm">
                    <form method="POST">
                        <textarea name="content" class="form-control mb-2" rows="3" placeholder="What's on your mind, <?php echo $_SESSION['user_name']; ?>?" required></textarea>
                        <button name="submit_post" class="btn btn-primary w-100 fw-bold">Post to Feed</button>
                    </form>
                </div>

                <!-- Display Posts -->
                <h5 class="mb-3">Recent Activity</h5>
                <?php while($post = mysqli_fetch_assoc($all_posts)): ?>
                <div class="card p-3 post-card shadow-sm">
                    <div class="d-flex align-items-center mb-2">
                        <img src="https://ui-avatars.com/api/?name=<?php echo $post['full_name']; ?>&background=random" class="rounded-circle me-2" width="40">
                        <div>
                            <h6 class="mb-0"><?php echo $post['full_name']; ?> 
                                <small class="text-muted font-monospace" style="font-size: 10px;">(<?php echo $post['role']; ?>)</small>
                            </h6>
                            <small class="text-muted"><?php echo date('M d, h:i A', strtotime($post['created_at'])); ?> | <?php echo $post['dept']; ?></small>
                        </div>
                    </div>
                    <p class="card-text"><?php echo nl2br($post['content']); ?></p>
                    <hr>
                    <div class="d-flex justify-content-around text-muted">
                        <small style="cursor:pointer;">Like (Coming soon)</small>
                        <small style="cursor:pointer;">Comment (Coming soon)</small>
                            <?php if($post['user_id'] == $_SESSION['user_id']): ?>
                             <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="text-danger text-decoration-none shadow-sm" onclick="return confirm('Are you sure?')">
                        <small>Delete</small>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- Right Sidebar (Notices) -->
            <div class="col-md-3">
                <div class="card p-3 post-card shadow-sm">
                    <h6>Upcoming Events / Notices</h6>
                    <hr>
                    <p class="text-muted small">No notices yet.</p>
                </div>
            </div>

        </div>
    </div>
</body>
</html>