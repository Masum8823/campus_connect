<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: ../user/dashboard.php");
    exit();
}

$post_id = $_GET['id'];
$current_user_id = $_SESSION['user_id'];

// ইউজারের নিজের ছবি আনা
$u_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT profile_pic FROM users WHERE id='$current_user_id'"));
$my_pic = ($u_info['profile_pic'] != 'default.png') ? "../" . $u_info['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($_SESSION['user_name']);

// ১. পোস্টের বিস্তারিত তথ্য আনা
$post_query = mysqli_query($conn, "SELECT posts.*, users.full_name, users.role, users.profile_pic 
                                   FROM posts 
                                   JOIN users ON posts.user_id = users.id 
                                   WHERE posts.id = '$post_id'");
$post = mysqli_fetch_assoc($post_query);

if(!$post){ echo "Post not found!"; exit(); }

// ২. কমেন্ট হ্যান্ডেল করা
if(isset($_POST['submit_comment'])){
    $comment_text = mysqli_real_escape_string($conn, $_POST['comment_text']);
    if(!empty($comment_text)){
        mysqli_query($conn, "INSERT INTO comments (post_id, user_id, comment_text) VALUES ('$post_id', '$current_user_id', '$comment_text')");
        header("Location: view_post.php?id=$post_id");
        exit();
    }
}

// ৩. এই পোস্টের সব কমেন্ট আনা
$comments = mysqli_query($conn, "SELECT comments.*, users.full_name, users.profile_pic 
                                 FROM comments 
                                 JOIN users ON comments.user_id = users.id 
                                 WHERE post_id='$post_id' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Details - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f0f2f5; padding-top: 70px; }
        .post-detail-card { border-radius: 15px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .comment-bubble { background-color: #f0f2f5; border-radius: 15px; padding: 10px 15px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../user/dashboard.php">CampusConnect</a>
            <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold">Back to Feed</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Main Post -->
                <div class="card post-detail-card mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <?php $p_pic = ($post['profile_pic'] != 'default.png') ? "../" . $post['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($post['full_name']); ?>
                            <img src="<?php echo $p_pic; ?>" class="rounded-circle me-3 border" width="50" height="50" style="object-fit: cover;">
                            <div>
                                <h6 class="mb-0 fw-bold"><?php echo $post['full_name']; ?> 
                                    <span class="badge bg-light text-dark border ms-1 fw-normal" style="font-size: 10px;"><?php echo strtoupper($post['role']); ?></span>
                                </h6>
                                <small class="text-muted"><?php echo date('M d, Y • h:i A', strtotime($post['created_at'])); ?></small>
                            </div>
                        </div>
                        <p class="card-text fs-5"><?php echo nl2br($post['content']); ?></p>
                        <hr>
                        
                        <!-- Interaction Summary -->
                        <div class="d-flex text-muted small fw-bold mb-3">
                            <span class="me-3"><i class="bi bi-hand-thumbs-up"></i> Like</span>
                            <span><i class="bi bi-chat-left"></i> <?php echo mysqli_num_rows($comments); ?> Comments</span>
                        </div>

                        <!-- Comment Input -->
                        <form method="POST" class="d-flex align-items-center mb-4">
                            <img src="<?php echo $my_pic; ?>" class="rounded-circle me-2 border" width="35" height="35">
                            <div class="input-group">
                                <input type="text" name="comment_text" class="form-control rounded-pill border-0 bg-light px-3" placeholder="Write a comment..." required>
                                <button name="submit_comment" class="btn btn-sm text-primary position-absolute end-0 me-2" style="z-index: 5;"><i class="bi bi-send-fill"></i></button>
                            </div>
                        </form>

                        <!-- All Comments List -->
                        <div class="comments-list">
                            <?php while($comment = mysqli_fetch_assoc($comments)): 
                                $c_pic = ($comment['profile_pic'] != 'default.png') ? "../" . $comment['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($comment['full_name']);
                            ?>
                                <div class="d-flex mb-3">
                                    <img src="<?php echo $c_pic; ?>" class="rounded-circle me-2 border" width="32" height="32" style="object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <div class="comment-bubble shadow-sm d-inline-block">
                                            <h6 class="mb-0 fw-bold" style="font-size: 12px;"><?php echo $comment['full_name']; ?></h6>
                                            <p class="mb-0 small text-dark"><?php echo $comment['comment_text']; ?></p>
                                        </div>
                                        <div class="mt-1 ms-2">
                                            <small class="text-muted" style="font-size: 10px;"><?php echo date('M d, h:i A', strtotime($comment['created_at'])); ?></small>
                                            <?php if($comment['user_id'] == $current_user_id): ?>
                                                <a href="../user/delete_comment.php?id=<?php echo $comment['id']; ?>&post_id=<?php echo $post_id; ?>" class="text-danger text-decoration-none ms-2" style="font-size: 10px;">Delete</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>