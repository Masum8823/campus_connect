<?php
include '../config.php';
session_start();

// Redirect if not logged in
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

// Get category filter from URL
$category_filter = isset($_GET['cat']) ? $_GET['cat'] : '';

// Build Query (academic_files.* includes the new external_link column)
$sql = "SELECT academic_files.*, users.full_name FROM academic_files 
        JOIN users ON academic_files.user_id = users.id";

if($category_filter) {
    $safe_cat = mysqli_real_escape_string($conn, $category_filter);
    $sql .= " WHERE category = '$safe_cat'";
}

$sql .= " ORDER BY uploaded_at DESC";
$files = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Academic Hub - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar-list .list-group-item.active { background-color: #0d6efd; border-color: #0d6efd; }
        .table-card { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../user/dashboard.php">
                <i class="bi bi-mortarboard-fill"></i> CampusConnect Academic
            </a>
            <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold">Back to Dashboard</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row">
            <!-- Sidebar Filter -->
            <div class="col-md-3 mb-4">
                <div class="list-group shadow-sm sidebar-list">
                    <a href="index.php" class="list-group-item list-group-item-action <?php echo ($category_filter == '') ? 'active' : ''; ?>">
                        <i class="bi bi-files me-2"></i> All Resources
                    </a>
                    <a href="index.php?cat=class_routine" class="list-group-item list-group-item-action <?php echo ($category_filter == 'class_routine') ? 'active' : ''; ?>">
                        <i class="bi bi-calendar3 me-2"></i> Class Routines
                    </a>
                    <a href="index.php?cat=exam_routine" class="list-group-item list-group-item-action <?php echo ($category_filter == 'exam_routine') ? 'active' : ''; ?>">
                        <i class="bi bi-file-earmark-text me-2"></i> Exam Routines
                    </a>
                    <a href="index.php?cat=course_material" class="list-group-item list-group-item-action <?php echo ($category_filter == 'course_material') ? 'active' : ''; ?>">
                        <i class="bi bi-journal-bookmark me-2"></i> Course Materials
                    </a>
                </div>
                
                <!-- Upload button for Teachers/Admins -->
                <?php if($_SESSION['role'] == 'teacher' || $_SESSION['role'] == 'admin'): ?>
                    <a href="upload_file.php" class="btn btn-primary w-100 mt-3 fw-bold shadow-sm py-2">
                        <i class="bi bi-cloud-arrow-up-fill"></i> Upload New Resource
                    </a>
                <?php endif; ?>
            </div>

            <!-- Content Area -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-secondary">
                        <?php 
                            if($category_filter == '') echo "All Academic Resources";
                            else echo ucwords(str_replace('_', ' ', $category_filter));
                        ?>
                    </h4>
                    <span class="badge bg-secondary rounded-pill px-3"><?php echo mysqli_num_rows($files); ?> Resources</span>
                </div>

                <div class="card table-card bg-white p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Uploaded By</th>
                                    <th>Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($files) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($files)): ?>
                                        <tr>
                                            <td>
                                                <i class="bi bi-file-earmark-zip-fill text-primary me-2"></i>
                                                <span class="fw-semibold text-dark"><?php echo $row['title']; ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">
                                                    <?php echo str_replace('_', ' ', $row['category']); ?>
                                                </span>
                                            </td>
                                            <td><small class="text-muted"><?php echo $row['full_name']; ?></small></td>
                                            <td><small class="text-muted"><?php echo date('M d, Y', strtotime($row['uploaded_at'])); ?></small></td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <!-- Download Button (If File Exists) -->
                                                    <?php if(!empty($row['file_path'])): ?>
                                                        <a href="../<?php echo $row['file_path']; ?>" class="btn btn-sm btn-success px-3 shadow-sm" download title="Download File">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                    <!-- Link Button (If External Link Exists) -->
                                                    <?php if(!empty($row['external_link'])): ?>
                                                        <a href="<?php echo $row['external_link']; ?>" target="_blank" class="btn btn-sm btn-info text-white px-3 shadow-sm" title="Open Link">
                                                            <i class="bi bi-box-arrow-up-right"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-folder-x display-4 d-block mb-2"></i>
                                            No resources found in this category.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>