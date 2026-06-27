<?php
include 'config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: dashboard.php");
    exit();
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM posts WHERE id='$post_id' AND user_id='$user_id'");
$post = mysqli_fetch_assoc($query);

if(!$post){
    echo "You don't have permission to edit this post!";
    exit();
}

if(isset($_POST['update_post'])){
    $new_content = mysqli_real_escape_string($conn, $_POST['content']);
    
    if(!empty($new_content)){
        $update_query = "UPDATE posts SET content='$new_content' WHERE id='$post_id'";
        if(mysqli_query($conn, $update_query)){
            header("Location: dashboard.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4 shadow border-0">
                    <h4 class="mb-4">Edit Your Post</h4>
                    <form method="POST">
                        <textarea name="content" class="form-control mb-3" rows="5" required><?php echo $post['content']; ?></textarea>
                        <div class="d-flex justify-content-between">
                            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                            <button name="update_post" class="btn btn-primary px-4">Update Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>