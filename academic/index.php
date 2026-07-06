<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

// Fetch files based on category if selected, else fetch all
$category_filter = isset($_GET['cat']) ? $_GET['cat'] : '';
$sql = "SELECT academic_files.*, users.full_name FROM academic_files 
        JOIN users ON academic_files.user_id = users.id";

if($category_filter) {
    $sql .= " WHERE category = '$category_filter'";
}
$sql .= " ORDER BY uploaded_at DESC";
$files = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Academic Hub - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary shadow mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../user/dashboard.php">CampusConnect Academic</a>
            <a href="../user/dashboard.php" class="btn btn-light btn-sm">Dashboard</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-md-3">
                <div class="list-group shadow-sm">
                    <a href="index.php" class="list-group-item list-group-item-action <?php echo !$category_filter ? 'active' : ''; ?>">All Files</a>
                    <a href="index.php?cat=class_routine" class="list-group-item list-group-item-action">Class Routines</a>
                    <a href="index.php?cat=exam_routine" class="list-group-item list-group-item-action">Exam Routines</a>
                    <a href="index.php?cat=course_material" class="list-group-item list-group-item-action">Course Materials</a>
                </div>
                
                <?php if($_SESSION['role'] != 'student'): ?>
                    <a href="upload_file.php" class="btn btn-primary w-100 mt-3 fw-bold">+ Upload New File</a>
                <?php endif; ?>
            </div>

            <!-- Files List -->
            <div class="col-md-9">
                <h4 class="mb-4">Academic Resources</h4>
                <div class="table-responsive bg-white p-3 rounded shadow-sm">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Uploaded By</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($files)): ?>
                                <tr>
                                    <td><i class="bi bi-file-earmark-pdf text-danger"></i> <?php echo $row['title']; ?></td>
                                    <td><span class="badge bg-info text-dark"><?php echo str_replace('_', ' ', $row['category']); ?></span></td>
                                    <td><?php echo $row['full_name']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['uploaded_at'])); ?></td>
                                    <td>
                                        <a href="../<?php echo $row['file_path']; ?>" class="btn btn-sm btn-success" download>Download</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>