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
$sql = "SELECT academic_files.*, users.full_name, users.profile_pic FROM academic_files 
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Hub | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0d6efd;
            --bg-light: #f0f2f5;
            --card-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }

        body { 
            background-color: var(--bg-light); 
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding-top: 80px;
        }

        /* Sidebar Styling */
        .sidebar-card {
            border-radius: 20px;
            border: none;
            box-shadow: var(--card-shadow);
            background: white;
            padding: 15px;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #adb5bd;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin: 20px 0 10px 10px;
        }

        .sidebar-list .list-group-item {
            border: none;
            border-radius: 12px;
            margin-bottom: 4px;
            font-size: 14px;
            font-weight: 500;
            color: #4b4f56;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }

        .sidebar-list .list-group-item i {
            font-size: 1.2rem;
            margin-right: 12px;
        }

        .sidebar-list .list-group-item:hover {
            background-color: #f8f9fa;
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .sidebar-list .list-group-item.active {
            background-color: #e7f3ff;
            color: var(--primary-color);
            box-shadow: none;
        }

        /* Content Area */
        .table-card {
            border-radius: 20px;
            border: none;
            box-shadow: var(--card-shadow);
            background: white;
            overflow: hidden;
        }

        .search-container {
            background: white;
            border-radius: 50px;
            padding: 5px 5px 5px 20px;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
        }

        .search-input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 14px;
        }

        .sort-select {
            border: none;
            font-size: 14px;
            font-weight: 600;
            color: #4b4f56;
            cursor: pointer;
            outline: none;
            background: transparent;
        }

        /* Custom Table Styling */
        .table thead {
            background-color: #f8f9fa;
        }

        .table thead th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            padding: 15px;
            border: none;
        }

        .table tbody tr {
            transition: all 0.2s;
        }

        .table tbody tr:hover {
            background-color: #fcfcfc;
        }

        .table tbody td {
            padding: 18px 15px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        .resource-icon {
            width: 40px;
            height: 40px;
            background: #f0f7ff;
            color: var(--primary-color);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .btn-action {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: 0.2s;
        }

        .btn-action:hover { transform: scale(1.1); }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="../user/dashboard.php">
                <i class="bi bi-mortarboard-fill me-2"></i>CampusConnect Academic
            </a>
            <div class="ms-auto">
                <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold rounded-pill px-4">
                    <i class="bi bi-house-door me-1"></i> Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <!-- Left Sidebar -->
            <div class="col-md-3 mb-4">
                <div class="sidebar-card">
                    <div class="section-title">Resources</div>
                    <div class="list-group sidebar-list">
                        <a href="index.php" class="list-group-item list-group-item-action <?php echo ($category_filter == '') ? 'active' : ''; ?>">
                            <i class="bi bi-grid-fill"></i> All Resources
                        </a>
                        <a href="index.php?cat=class_routine" class="list-group-item list-group-item-action <?php echo ($category_filter == 'class_routine') ? 'active' : ''; ?>">
                            <i class="bi bi-calendar3"></i> Class Routines
                        </a>
                        <a href="index.php?cat=exam_routine" class="list-group-item list-group-item-action <?php echo ($category_filter == 'exam_routine') ? 'active' : ''; ?>">
                            <i class="bi bi-file-earmark-text"></i> Exam Routines
                        </a>
                        <a href="index.php?cat=course_material" class="list-group-item list-group-item-action <?php echo ($category_filter == 'course_material') ? 'active' : ''; ?>">
                            <i class="bi bi-journal-bookmark"></i> Course Materials
                        </a>
                    </div>

                    <div class="section-title">Academic Tools</div>
                    <div class="list-group sidebar-list">
                        <a href="assignments.php" class="list-group-item list-group-item-action">
                            <i class="bi bi-file-earmark-check text-danger"></i> Assignments Hub
                        </a>
                        <a href="gpa_calculator.php" class="list-group-item list-group-item-action">
                            <i class="bi bi-calculator text-success"></i> GPA Calculator
                        </a>
                    </div>
                    
                    <?php if($_SESSION['role'] == 'teacher' || $_SESSION['role'] == 'admin'): ?>
                        <a href="upload_file.php" class="btn btn-primary w-100 mt-4 fw-bold py-2 rounded-pill shadow-sm">
                            <i class="bi bi-plus-circle me-1"></i> Upload Resource
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9">
                <!-- Advanced Search & Sort Bar -->
                <div class="search-container mb-4">
                    <form method="GET" action="" class="w-100 d-flex align-items-center">
                        <input type="hidden" name="cat" value="<?php echo $category_filter; ?>">
                        <i class="bi bi-search text-muted me-2"></i>
                        <input type="text" name="search" class="search-input" placeholder="Search by title or department..." value="<?php echo $search; ?>">
                        
                        <div class="vr mx-3 h-50 my-auto text-muted"></div>
                        
                        <select name="sort" class="sort-select me-3" onchange="this.form.submit()">
                            <option value="desc" <?php echo ($sort == 'desc') ? 'selected' : ''; ?>>Newest</option>
                            <option value="asc" <?php echo ($sort == 'asc') ? 'selected' : ''; ?>>Oldest</option>
                        </select>
                        
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Search</button>
                    </form>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                    <h4 class="fw-bold text-dark mb-0">
                        <?php 
                            if($search) echo "Results for '$search'";
                            elseif($category_filter == '') echo "Academic Resources";
                            else echo ucwords(str_replace('_', ' ', $category_filter));
                        ?>
                    </h4>
                    <span class="badge bg-white text-dark border shadow-sm rounded-pill px-3 py-2"><?php echo mysqli_num_rows($files); ?> Items</span>
                </div>

                <!-- Table Card -->
                <div class="table-card">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Resource Title</th>
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
                                                <div class="d-flex align-items-center">
                                                    <div class="resource-icon me-3">
                                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-bold text-dark d-block"><?php echo $row['title']; ?></span>
                                                        <small class="text-muted" style="font-size: 11px;">Dept: <?php echo $row['dept'] ?? 'N/A'; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3">
                                                    <?php echo str_replace('_', ' ', $row['category']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <small class="fw-bold text-secondary"><?php echo $row['full_name']; ?></small>
                                                </div>
                                            </td>
                                            <td><small class="text-muted"><?php echo date('M d, Y', strtotime($row['uploaded_at'])); ?></small></td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <?php if(!empty($row['file_path'])): ?>
                                                        <a href="../<?php echo $row['file_path']; ?>" class="btn-action bg-success text-white" download title="Download">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if(!empty($row['external_link'])): ?>
                                                        <a href="<?php echo $row['external_link']; ?>" target="_blank" class="btn-action bg-info text-white" title="Open Link">
                                                            <i class="bi bi-link-45deg"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-folder-x display-1 opacity-25"></i>
                                            <p class="mt-3 fw-bold">No resources found matching your criteria.</p>
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