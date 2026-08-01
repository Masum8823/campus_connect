<?php
include '../config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php"); exit();
}

if(isset($_GET['delete_file'])){
    $file_id = $_GET['delete_file'];
    
    $file_query = mysqli_query($conn, "SELECT file_path FROM academic_files WHERE id='$file_id'");
    $file_data = mysqli_fetch_assoc($file_query);
    if($file_data['file_path'] && file_exists("../".$file_data['file_path'])){
        unlink("../".$file_data['file_path']);
    }

    mysqli_query($conn, "DELETE FROM academic_files WHERE id='$file_id'");
    header("Location: manage_academic.php?msg=deleted");
}

$files = mysqli_query($conn, "SELECT academic_files.*, users.full_name FROM academic_files JOIN users ON academic_files.user_id = users.id ORDER BY uploaded_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Academic Resources | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: #212529; padding: 20px; color: white; }
        .main-content { margin-left: 260px; padding: 40px; }
        .table-card { background: white; border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <div class="sidebar">
        <h4 class="fw-bold text-center mb-4 text-primary">Admin Control</h4>
        <nav class="nav flex-column">
            <a href="index.php" class="nav-link active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="manage_users.php" class="nav-link"><i class="bi bi-people me-2"></i> Manage Users</a>
            <a href="manage_lost_found.php" class="nav-link text-white"><i class="bi bi-search me-2"></i> Lost & Found</a>
            <a href="manage_academic.php" class="nav-link text-white"><i class="bi bi-mortarboard me-2"></i> Academic Resources</a>
            <a href="manage_content.php" class="nav-link"><i class="bi bi-file-post me-2"></i> Content Moderation</a>
            <a href="../user/dashboard.php" class="nav-link"><i class="bi bi-arrow-left-circle me-2"></i> User View</a>
            <hr>
            <a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-power me-2"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <h3 class="fw-bold mb-4">Academic Resource Management</h3>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success">Resource deleted successfully.</div>
        <?php endif; ?>

        <div class="card table-card p-3">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>Resource Title</th>
                        <th>Category</th>
                        <th>Dept</th>
                        <th>Uploaded By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($files)): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?php echo $row['title']; ?></div>
                                <small class="text-muted"><?php echo date('M d, Y', strtotime($row['uploaded_at'])); ?></small>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">
                                    <?php echo str_replace('_', ' ', $row['category']); ?>
                                </span>
                            </td>
                            <td><span class="fw-bold"><?php echo $row['dept']; ?></span></td>
                            <td><small><?php echo $row['full_name']; ?></small></td>
                            <td>
                                <div class="btn-group">
                                    <?php if($row['file_path']): ?>
                                        <a href="../<?php echo $row['file_path']; ?>" class="btn btn-sm btn-light border" download><i class="bi bi-download"></i></a>
                                    <?php endif; ?>
                                    <a href="?delete_file=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this file permanently?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>