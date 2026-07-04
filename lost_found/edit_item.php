<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM lost_found WHERE id='$id' AND user_id='$user_id'");
$item = mysqli_fetch_assoc($query);

if(!$item){ header("Location: index.php"); exit(); }

if(isset($_POST['update_item'])){
    $name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $resolved = isset($_POST['is_resolved']) ? 1 : 0;

    mysqli_query($conn, "UPDATE lost_found SET item_name='$name', description='$desc', is_resolved='$resolved' WHERE id='$id'");
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container card p-4 shadow-sm mx-auto" style="max-width: 500px; border-radius: 15px;">
        <h4 class="text-center mb-4">Update Item Status</h4>
        <form method="POST">
            <input type="text" name="item_name" class="form-control mb-3" value="<?php echo $item['item_name']; ?>" required>
            <textarea name="description" class="form-control mb-3" rows="4" required><?php echo $item['description']; ?></textarea>
            <div class="form-check form-switch mb-4 p-2 border rounded bg-white">
                <input class="form-check-input ms-1 me-2" type="checkbox" name="is_resolved" <?php echo $item['is_resolved'] == 1 ? 'checked' : ''; ?>>
                <label class="form-check-label fw-bold text-success">Mark as Resolved / Found</label>
            </div>
            <button name="update_item" class="btn btn-primary w-100 mb-2">Update Post</button>
            <a href="index.php" class="btn btn-secondary w-100">Cancel</a>
        </form>
    </div>
</body>
</html>