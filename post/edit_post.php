<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: ../user/dashboard.php");
    exit();
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM posts WHERE id='$post_id' AND user_id='$user_id'");
$post = mysqli_fetch_assoc($query);

if(!$post){ echo "Access Denied!"; exit(); }

if(isset($_POST['update_post'])){
    $new_content = mysqli_real_escape_string($conn, $_POST['content']);
    $image_sql = "";

    if(!empty($_FILES['post_img']['name'])){
        $img_name = time() . "_" . $_FILES['post_img']['name'];
        $target = "../uploads/posts/" . $img_name;
        if(move_uploaded_file($_FILES['post_img']['tmp_name'], $target)){
            $image_sql = ", post_image='uploads/posts/$img_name'";
        }
    }

    $update = "UPDATE posts SET content='$new_content', is_edited=1 $image_sql WHERE id='$post_id'";
    if(mysqli_query($conn, $update)){
        header("Location: ../user/dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; min-height: 100vh; }
        .edit-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 600px; margin: auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card edit-card p-4">
            <h4 class="fw-bold text-primary mb-4 text-center">Edit Post</h4>
            <form method="POST" enctype="multipart/form-data">
                <textarea name="content" class="form-control mb-3" rows="5" required><?php echo $post['content']; ?></textarea>
                
                <div class="p-3 border rounded bg-light mb-4">
                    <label class="small fw-bold">Update Photo (Optional)</label>
                    <input type="file" name="post_img" class="form-control form-control-sm mt-2">
                </div>

                <div class="d-flex gap-2">
                    <button name="update_post" class="btn btn-primary w-100 fw-bold rounded-pill">SAVE CHANGES</button>
                    <a href="../user/dashboard.php" class="btn btn-light w-100 border rounded-pill">CANCEL</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>