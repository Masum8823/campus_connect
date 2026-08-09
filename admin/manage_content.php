<?php
include '../config.php';
// সেশন অলরেডি config.php-তে চেক করা আছে

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php"); exit();
}

// ১. পোস্ট ডিলিট করার লজিক (ডাটাবেস + ইমেজ ক্লিনআপ)
if(isset($_GET['delete_post'])){
    $p_id = mysqli_real_escape_string($conn, $_GET['delete_post']);
    
    // ইমেজ খুঁজে বের করা
    $img_query = mysqli_query($conn, "SELECT post_image FROM posts WHERE id='$p_id'");
    $img_data = mysqli_fetch_assoc($img_query);

    // সার্ভার থেকে ছবি মুছে ফেলা (Storage cleanup)
    if(!empty($img_data['post_image']) && file_exists("../".$img_data['post_image'])){
        unlink("../".$img_data['post_image']);
    }

    // ডাটাবেস থেকে ডিলিট
    if(mysqli_query($conn, "DELETE FROM posts WHERE id='$p_id'")){
        header("Location: manage_content.php?msg=deleted");
        exit();
    }
}

// ২. সব পোস্ট ডাটাবেস থেকে আনা
$all_posts_query = "SELECT posts.*, users.full_name, users.dept, users.role 
                    FROM posts 
                    JOIN users ON posts.user_id = users.id 
                    ORDER BY created_at DESC";
$all_posts = mysqli_query($conn, $all_posts_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Moderation | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --sidebar-bg: #1a1d20; --bg-light: #f4f7f6; }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 20px; }
        
        /* Fixed Sidebar Styling */
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: var(--sidebar-bg); padding: 20px; color: white; z-index: 1000; }
        .main-content { margin-left: 260px; padding: 30px; }
        .nav-link { color: #adb5bd; padding: 12px; border-radius: 12px; margin-bottom: 5px; transition: 0.3s; border: none; text-align: left; display: flex; align-items: center; text-decoration: none; }
        .nav-link:hover, .nav-link.active { background: var(--primary-color); color: white; }
        .nav-link i { font-size: 1.2rem; margin-right: 12px; }

        /* Table Card Styling */
        .table-card { border-radius: 25px; border: none; background: white; box-shadow: 0 10px 40px rgba(0,0,0,0.05); overflow: hidden; }
        .table thead { background-color: #f8f9fa; }
        .table thead th { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #888; padding: 18px 15px; border: none; }
        .table tbody td { padding: 15px; border-bottom: 1px solid #f1f2f4; vertical-align: middle; }
        
        .post-preview-img { width: 60px; height: 45px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; }
        .author-badge { font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 3px 10px; border-radius: 50px; background: #e7f1ff; color: #0d6efd; }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar shadow">
        <h4 class="fw-bold text-center mb-4 text-primary mt-2">Admin Control</h4>
        <nav class="nav flex-column">
            <a href="index.php" class="nav-link"><i class="bi bi-grid-1x2-fill"></i> <span>Dashboard</span></a>
            <a href="manage_users.php" class="nav-link"><i class="bi bi-people-fill"></i> <span>Manage Users</span></a>
            <a href="manage_lost_found.php" class="nav-link"><i class="bi bi-search"></i> <span>Lost & Found</span></a>
            <a href="manage_academic.php" class="nav-link"><i class="bi bi-mortarboard-fill"></i> <span>Academic Hub</span></a>
            <a href="manage_content.php" class="nav-link active"><i class="bi bi-file-post"></i> <span>Content Moderation</span></a>
            <a href="manage_marketplace.php" class="nav-link"><i class="bi bi-shop me-2"></i> Marketplace</a>
            <a href="suggestions.php" class="nav-link"><i class="bi bi-lightbulb-fill"></i> <span>Suggestions</span></a>
            <hr class="text-secondary">
            <a href="../user/dashboard.php" class="nav-link"><i class="bi bi-display"></i> <span>User View</span></a>
            <a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-power"></i> <span>Logout</span></a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Feed Moderation</h2>
                <p class="text-muted small">Monitor and remove inappropriate campus posts.</p>
            </div>
            <span class="badge bg-white text-dark border shadow-sm rounded-pill px-3 py-2 small">
                <i class="bi bi-chat-dots-fill text-primary me-1"></i> <?php echo mysqli_num_rows($all_posts); ?> Total Posts
            </span>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">Post has been successfully removed from the system.</div>
        <?php endif; ?>

        <!-- Content Moderation Table -->
        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Post Snippet</th>
                            <th>Author Info</th>
                            <th>Attached Media</th>
                            <th>Posted Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($all_posts) > 0): ?>
                            <?php while($post = mysqli_fetch_assoc($all_posts)): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div style="max-width: 280px;" class="text-truncate fw-medium text-dark" title="<?php echo $post['content']; ?>">
                                            <?php echo nl2br(substr($post['content'], 0, 80)); ?>...
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark small mb-1"><?php echo $post['full_name']; ?></div>
                                        <span class="author-badge"><?php echo $post['role']; ?> | <?php echo $post['dept']; ?></span>
                                    </td>
                                    <td>
                                        <?php if(!empty($post['post_image'])): ?>
                                            <a href="../<?php echo $post['post_image']; ?>" target="_blank">
                                                <img src="../<?php echo $post['post_image']; ?>" class="post-preview-img shadow-sm">
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small italic">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small class="text-muted"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></small></td>
                                    <td class="text-center">
                                        <a href="?delete_post=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-danger px-3 rounded-pill fw-bold" onclick="return confirm('Permanently delete this post?')">
                                            <i class="bi bi-trash me-1"></i> REMOVE
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">No posts found on the feed.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>