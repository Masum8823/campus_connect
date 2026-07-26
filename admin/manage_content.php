<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php"); exit();
}

if(isset($_GET['delete_post'])){
    $p_id = $_GET['delete_post'];
    
    $img_query = mysqli_query($conn, "SELECT post_image FROM posts WHERE id='$p_id'");
    $img_data = mysqli_fetch_assoc($img_query);
    if($img_data['post_image'] && file_exists("../".$img_data['post_image'])){
        unlink("../".$img_data['post_image']);
    }

    mysqli_query($conn, "DELETE FROM posts WHERE id='$p_id'");
    header("Location: manage_content.php?msg=post_deleted");
}

$all_posts_query = "SELECT posts.*, users.full_name, users.dept FROM posts 
                    JOIN users ON posts.user_id = users.id 
                    ORDER BY created_at DESC";
$all_posts = mysqli_query($conn, $all_posts_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Content Moderation | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: #212529; padding: 20px; color: white; }
        .main-content { margin-left: 260px; padding: 40px; }
        .content-card { background: white; border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .post-img-preview { width: 60px; height: 40px; object-fit: cover; border-radius: 5px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h4 class="fw-bold text-center mb-4 text-primary">Admin Control</h4>
        <nav class="nav flex-column">
            <a href="index.php" class="nav-link text-white"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="manage_users.php" class="nav-link text-white"><i class="bi bi-people me-2"></i> Manage Users</a>
            <a href="manage_content.php" class="nav-link active bg-primary text-white shadow-sm"><i class="bi bi-file-post me-2"></i> Content Moderation</a>
            <hr>
            <a href="../user/dashboard.php" class="nav-link text-white"><i class="bi bi-arrow-left-circle me-2"></i> User View</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Feed Moderation</h3>
            <span class="badge bg-dark px-3 py-2"><?php echo mysqli_num_rows($all_posts); ?> Total Posts</span>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'post_deleted'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Post has been removed from the feed.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card content-card p-3">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>Post Content</th>
                        <th>Author</th>
                        <th>Image</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($post = mysqli_fetch_assoc($all_posts)): ?>
                        <tr>
                            <td style="max-width: 300px;">
                                <div class="text-truncate" title="<?php echo $post['content']; ?>">
                                    <?php echo nl2br(substr($post['content'], 0, 80)); ?>...
                                </div>
                            </td>
                            <td>
                                <strong><?php echo $post['full_name']; ?></strong><br>
                                <small class="text-muted"><?php echo $post['dept']; ?></small>
                            </td>
                            <td>
                                <?php if($post['post_image']): ?>
                                    <img src="../<?php echo $post['post_image']; ?>" class="post-img-preview border">
                                <?php else: ?>
                                    <span class="text-muted small">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></small></td>
                            <td>
                                <a href="?delete_post=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this post permanently?')">
                                    <i class="bi bi-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>