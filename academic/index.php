<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

// URL parameters
$category_filter = $_GET['cat'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'desc'; 

// Basic Query
$sql = "SELECT academic_files.*, users.full_name FROM academic_files 
        JOIN users ON academic_files.user_id = users.id WHERE 1=1";

if($category_filter) {
    $safe_cat = mysqli_real_escape_string($conn, $category_filter);
    $sql .= " AND category = '$safe_cat'";
}

if($search) {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (title LIKE '%$safe_search%' OR academic_files.dept LIKE '%$safe_search%')";
}

$sql .= " ORDER BY uploaded_at " . ($sort == 'asc' ? 'ASC' : 'DESC');
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
        .sidebar-list .list-group-item { border: none; border-radius: 10px; margin-bottom: 5px; font-size: 14px; }
        .sidebar-list .list-group-item.active { background-color: #0d6efd; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2); }
        .table-card { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .section-title { font-size: 11px; font-weight: bold; color: #888; text-transform: uppercase; letter-spacing: 1px; margin: 15px 0 10px 10px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow sticky-top mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../user/dashboard.php">
                <i class="bi bi-mortarboard-fill"></i> CampusConnect Academic
            </a>
            <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold">Back to Feed</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row">
            <!-- Updated Sidebar -->
            <div class="col-md-3 mb-4">
                <div class="list-group shadow-sm sidebar-list p-2 bg-white rounded">
                    
                    <div class="section-title">Resources</div>
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

                    <!-- New Tools Section -->
                    <div class="section-title">Academic Tools</div>
                    <a href="assignments.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-file-earmark-check text-danger me-2"></i> Assignments Hub
                    </a>
                    <a href="gpa_calculator.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-calculator text-success me-2"></i> GPA Calculator
                    </a>
                </div>
                
                <?php if($_SESSION['role'] == 'teacher' || $_SESSION['role'] == 'admin'): ?>
                    <a href="upload_file.php" class="btn btn-primary w-100 mt-3 fw-bold shadow-sm py-2 rounded-pill">
                        <i class="bi bi-plus-circle me-1"></i> Upload Resource
                    </a>
                <?php endif; ?>
            </div>

            <!-- Content Area -->
            <div class="col-md-9">
                <!-- Search and Sort Section -->
                <div class="card p-3 mb-4 table-card">
                    <form method="GET" class="row g-2">
                        <input type="hidden" name="cat" value="<?php echo $category_filter; ?>">
                        <div class="col-md-7 position-relative">
                            <input type="text" name="search" class="form-control rounded-pill ps-4" placeholder="Search resources..." value="<?php echo $search; ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="sort" class="form-select rounded-pill" onchange="this.form.submit()">
                                <option value="desc" <?php echo ($sort == 'desc') ? 'selected' : ''; ?>>Newest First</option>
                                <option value="asc" <?php echo ($sort == 'asc') ? 'selected' : ''; ?>>Oldest First</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark w-100 fw-bold rounded-pill">Search</button>
                        </div>
                    </form>
                </div>

                <h4 class="fw-bold text-secondary mb-4 px-2">
                    <?php 
                        if($search) echo "Search: '$search'";
                        elseif($category_filter == '') echo "Academic Resources";
                        else echo ucwords(str_replace('_', ' ', $category_filter));
                    ?>
                </h4>

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
                                                <i class="bi bi-file-earmark-pdf text-primary me-2"></i>
                                                <span class="fw-semibold text-dark"><?php echo $row['title']; ?></span>
                                            </td>
                                            <td><span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill small"><?php echo str_replace('_', ' ', $row['category']); ?></span></td>
                                            <td><small class="text-muted"><?php echo $row['full_name']; ?></small></td>
                                            <td><small class="text-muted"><?php echo date('M d, Y', strtotime($row['uploaded_at'])); ?></small></td>
                                            <td class="text-center">
                                                <?php if(!empty($row['file_path'])): ?>
                                                    <a href="../<?php echo $row['file_path']; ?>" class="btn btn-sm btn-success px-2 rounded-circle" download><i class="bi bi-download"></i></a>
                                                <?php endif; ?>
                                                <?php if(!empty($row['external_link'])): ?>
                                                    <a href="<?php echo $row['external_link']; ?>" target="_blank" class="btn btn-sm btn-info text-white px-2 rounded-circle ms-1"><i class="bi bi-link-45deg"></i></a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-5 text-muted">No resources found.</td></tr>
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