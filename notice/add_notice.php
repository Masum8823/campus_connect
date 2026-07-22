<?php
include '../config.php';
session_start();

// Security: Only Teachers/Admins
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'teacher' && $_SESSION['role'] != 'admin')) {
    header("Location: view_notice_list.php");
    exit();
}

if (isset($_POST['publish_notice'])) {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $audience = $_POST['audience'];
    $external_link = mysqli_real_escape_string($conn, $_POST['external_link']);

    $db_image_path = NULL;

    // Handle Image Upload (Optional)
    if (!empty($_FILES['notice_image']['name'])) {
        $file_name = time() . "_" . $_FILES['notice_image']['name'];
        $target = "../uploads/notices/" . $file_name;
        $db_image_path = "uploads/notices/" . $file_name;

        // Create folder if not exists
        if (!file_exists('../uploads/notices')) {
            mkdir('../uploads/notices', 0777, true);
        }
        move_uploaded_file($_FILES['notice_image']['tmp_name'], $target);
    }

    $query = "INSERT INTO notices (user_id, title, description, image_path, external_link, target_audience) 
              VALUES ('$user_id', '$title', '$desc', " . ($db_image_path ? "'$db_image_path'" : "NULL") . ", " . ($external_link ? "'$external_link'" : "NULL") . ", '$audience')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Official Notice Published!'); window.location='view_notice_list.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Notice - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .upload-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
        .form-label { font-weight: 600; color: #444; font-size: 14px; }
        .form-control, .form-select { border-radius: 10px; padding: 12px; border: 1px solid #ddd; background: #fdfdfd; }
        .form-control:focus { box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1); border-color: #0d6efd; }
        .input-icon-box { background: #f8f9fa; border-radius: 10px; padding: 15px; border: 1px dashed #ccc; margin-bottom: 20px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="view_notice_list.php">← Back to Notices</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card upload-card shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 text-center">
                        <h3 class="fw-bold text-dark mb-1">Create Official Notice</h3>
                        <p class="text-muted small">Broadcast information to the campus community</p>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data">
                            
                            <!-- Title -->
                            <div class="mb-3">
                                <label class="form-label">Notice Title</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g., Mid-Term Examination Schedule" required>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label class="form-label">Detailed Description</label>
                                <textarea name="desc" class="form-control" rows="5" placeholder="Explain the notice in detail..." required></textarea>
                            </div>

                            <div class="row">
                                <!-- Target Audience -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Who can see this?</label>
                                    <select name="audience" class="form-select">
                                        <option value="all">Everyone</option>
                                        <option value="students">Students Only</option>
                                        <option value="teachers">Teachers Only</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Image Upload Box -->
                            <div class="input-icon-box">
                                <label class="form-label mb-2"><i class="bi bi-image text-primary me-2"></i> Attach Notice Poster/Image (Optional)</label>
                                <input type="file" name="notice_image" class="form-control" accept="image/*">
                                <small class="text-muted mt-1 d-block">Recommended: High quality JPG or PNG</small>
                            </div>

                            <!-- Link Input Box -->
                            <div class="input-icon-box">
                                <label class="form-label mb-2"><i class="bi bi-link-45deg text-success me-2"></i> External Resource Link (Optional)</label>
                                <input type="url" name="external_link" class="form-control" placeholder="https://drive.google.com/..." >
                                <small class="text-muted mt-1 d-block">Add Google Drive, Form, or Website links</small>
                            </div>

                            <!-- Submit Button -->
                            <button name="publish_notice" class="btn btn-primary w-100 fw-bold py-3 shadow rounded-pill">
                                <i class="bi bi-megaphone-fill me-2"></i> PUBLISH NOTICE
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>