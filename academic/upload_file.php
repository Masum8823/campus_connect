<?php
include '../config.php';
session_start();

// Security: Only Teachers/Admins
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'teacher' && $_SESSION['role'] != 'admin')) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['upload_resource'])) {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = $_POST['category'];
    $dept = mysqli_real_escape_string($conn, $_POST['dept']);
    $external_link = mysqli_real_escape_string($conn, $_POST['external_link']);

    $db_save_path = NULL;

    // Handle File Upload if selected
    if (!empty($_FILES['resource_file']['name'])) {
        $file_name = $_FILES['resource_file']['name'];
        $file_tmp = $_FILES['resource_file']['tmp_name'];
        $new_file_name = time() . "_" . $file_name;
        $upload_path = "../uploads/academic/" . $new_file_name;
        $db_save_path = "uploads/academic/" . $new_file_name;

        move_uploaded_file($file_tmp, $upload_path);
    }

    // Insert into database if either file or link is provided
    if ($db_save_path || !empty($external_link)) {
        // SQL query with NULL handling for empty fields
        $file_val = $db_save_path ? "'$db_save_path'" : "NULL";
        $link_val = !empty($external_link) ? "'$external_link'" : "NULL";

        $query = "INSERT INTO academic_files (user_id, title, category, dept, file_path, external_link) 
                  VALUES ('$user_id', '$title', '$category', '$dept', $file_val, $link_val)";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Resource Shared Successfully!'); window.location='index.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "<script>alert('Please provide either a file or a link!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Resource - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .upload-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .divider { display: flex; align-items: center; text-align: center; color: #aaa; margin: 20px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid #ddd; }
        .divider:not(:empty)::before { margin-right: .25em; }
        .divider:not(:empty)::after { margin-left: .25em; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary shadow mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Academic Hub</a>
            <a href="index.php" class="btn btn-light btn-sm fw-bold">← Back</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card upload-card p-4">
                    <h3 class="text-center fw-bold text-primary mb-4">Share Resource</h3>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <!-- Resource Title -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Resource Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. CSE 302 Syllabus" required>
                        </div>

                        <div class="row">
                            <!-- Category Selection -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Category</label>
                                <select name="category" class="form-control" required>
                                    <option value="class_routine">Class Routine</option>
                                    <option value="exam_routine">Exam Routine</option>
                                    <option value="course_material">Course Material</option>
                                </select>
                            </div>
                            <!-- Department Selection -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Department</label>
                                <select name="dept" class="form-control" required>
                                    <option value="CSE">CSE</option>
                                    <option value="EEE">EEE</option>
                                    <option value="BBA">BBA</option>
                                    <option value="English">English</option>
                                </select>
                            </div>
                        </div>

                        <!-- Option 1: File Upload -->
                        <div class="mt-3 p-3 border rounded bg-light">
                            <label class="form-label fw-bold text-dark"><i class="bi bi-file-earmark-arrow-up"></i> Option 1: Upload File</label>
                            <input type="file" name="resource_file" class="form-control">
                            <small class="text-muted">PDF, Image, or ZIP</small>
                        </div>

                        <!-- OR Divider -->
                        <div class="divider fw-bold">OR</div>

                        <!-- Option 2: Link Upload -->
                        <div class="p-3 border rounded bg-light">
                            <label class="form-label fw-bold text-dark"><i class="bi bi-link-45deg"></i> Option 2: External Link</label>
                            <input type="url" name="external_link" class="form-control" placeholder="https://drive.google.com/...">
                            <small class="text-muted">Google Drive, YouTube, or Blog link</small>
                        </div>

                        <!-- Submit Button -->
                        <button name="upload_resource" class="btn btn-primary w-100 fw-bold mt-4 py-2 shadow">
                            <i class="bi bi-cloud-check-fill"></i> Publish Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>