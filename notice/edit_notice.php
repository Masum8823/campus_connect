<?php
include '../config.php';
session_start();

// Security Check: Only Teachers/Admins can edit
if(!isset($_SESSION['user_id']) || $_SESSION['role'] == 'student' || !isset($_GET['id'])){
    header("Location: view_notice_list.php");
    exit();
}

$notice_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch current notice data
$query = mysqli_query($conn, "SELECT * FROM notices WHERE id='$notice_id' AND user_id='$user_id'");
$notice = mysqli_fetch_assoc($query);

if(!$notice){
    echo "<script>alert('Permission Denied or Notice Not Found!'); window.location='view_notice_list.php';</script>";
    exit();
}

// Update Logic
if(isset($_POST['update_notice'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $audience = $_POST['audience'];
    $external_link = mysqli_real_escape_string($conn, $_POST['external_link']);

    $image_sql = ""; 

    if (!empty($_FILES['notice_image']['name'])) {
        $file_name = time() . "_" . $_FILES['notice_image']['name'];
        $target = "../uploads/notices/" . $file_name;
        $db_image_path = "uploads/notices/" . $file_name;

        if (move_uploaded_file($_FILES['notice_image']['tmp_name'], $target)) {
            $image_sql = ", image_path='$db_image_path'";
            
            if(!empty($notice['image_path']) && file_exists("../" . $notice['image_path'])){
                unlink("../" . $notice['image_path']);
            }
        }
    }

    $update_query = "UPDATE notices SET 
                    title='$title', 
                    description='$desc', 
                    target_audience='$audience', 
                    external_link='$external_link' 
                    $image_sql 
                    WHERE id='$notice_id' AND user_id='$user_id'";
    
    if(mysqli_query($conn, $update_query)){
        echo "<script>alert('Notice Updated Successfully!'); window.location='view_notice.php?id=$notice_id';</script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Notice - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .edit-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; color: #444; font-size: 14px; }
        .form-control, .form-select { border-radius: 10px; padding: 12px; border: 1px solid #ddd; }
        .input-icon-box { background: #f8f9fa; border-radius: 10px; padding: 15px; border: 1px dashed #ccc; margin-bottom: 20px; }
        .current-img-preview { width: 100px; height: 60px; object-fit: cover; border-radius: 5px; margin-top: 5px; border: 1px solid #ddd; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="view_notice_list.php">← Cancel Editing</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card edit-card shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 text-center">
                        <h3 class="fw-bold text-primary mb-1">Edit Official Notice</h3>
                        <p class="text-muted small">Update the information below</p>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data">
                            
                            <!-- Title -->
                            <div class="mb-3">
                                <label class="form-label">Notice Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo $notice['title']; ?>" required>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label class="form-label">Detailed Description</label>
                                <textarea name="desc" class="form-control" rows="6" required><?php echo $notice['description']; ?></textarea>
                            </div>

                            <!-- Audience -->
                            <div class="mb-3">
                                <label class="form-label">Target Audience</label>
                                <select name="audience" class="form-select">
                                    <option value="all" <?php if($notice['target_audience'] == 'all') echo 'selected'; ?>>Everyone</option>
                                    <option value="students" <?php if($notice['target_audience'] == 'students') echo 'selected'; ?>>Students Only</option>
                                    <option value="teachers" <?php if($notice['target_audience'] == 'teachers') echo 'selected'; ?>>Teachers Only</option>
                                </select>
                            </div>

                            <!-- Image Update -->
                            <div class="input-icon-box">
                                <label class="form-label mb-2"><i class="bi bi-image text-primary me-2"></i> Update Notice Poster (Optional)</label>
                                <input type="file" name="notice_image" class="form-control" accept="image/*">
                                <?php if(!empty($notice['image_path'])): ?>
                                    <div class="d-flex align-items-center mt-2">
                                        <small class="text-muted me-2">Current Image:</small>
                                        <img src="../<?php echo $notice['image_path']; ?>" class="current-img-preview">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Link Update -->
                            <div class="input-icon-box">
                                <label class="form-label mb-2"><i class="bi bi-link-45deg text-success me-2"></i> Update Resource Link (Optional)</label>
                                <input type="url" name="external_link" class="form-control" value="<?php echo $notice['external_link']; ?>" placeholder="https://..." >
                            </div>

                            <!-- Action Buttons -->
                            <div class="row g-2">
                                <div class="col-8">
                                    <button name="update_notice" class="btn btn-primary w-100 fw-bold py-3 shadow rounded-pill">
                                        SAVE CHANGES
                                    </button>
                                </div>
                                <div class="col-4">
                                    <a href="view_notice_list.php" class="btn btn-light w-100 fw-bold py-3 border rounded-pill">CANCEL</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>