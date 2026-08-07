<?php
include '../config.php';

// ১. সিকিউরিটি চেক: শুধু অ্যাডমিন ঢুকতে পারবে
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php"); exit();
}

$current_user_id = $_SESSION['user_id'];

// ২. ফাইল ডিলিট করার লজিক (ডাটাবেস + সার্ভার ক্লিনআপ)
if(isset($_GET['delete_file'])){
    $file_id = mysqli_real_escape_string($conn, $_GET['delete_file']);
    
    // ডাটাবেস থেকে আগে ফাইল পাথ বের করা
    $file_query = mysqli_query($conn, "SELECT file_path FROM academic_files WHERE id='$file_id'");
    $file_data = mysqli_fetch_assoc($file_query);
    
    // সার্ভার থেকে আসল ফাইলটি ডিলিট করা (যদি থাকে)
    if(!empty($file_data['file_path']) && file_exists("../".$file_data['file_path'])){
        unlink("../".$file_data['file_path']);
    }

    // ডাটাবেস থেকে রেকর্ড ডিলিট করা
    if(mysqli_query($conn, "DELETE FROM academic_files WHERE id='$file_id'")){
        header("Location: manage_academic.php?msg=deleted");
        exit();
    }
}

// ৩. কুইক স্ট্যাটাস কাউন্ট
$routine_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM academic_files WHERE category IN ('class_routine', 'exam_routine')"))['total'];
$material_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM academic_files WHERE category = 'course_material'"))['total'];

// ৪. সব একাডেমিক ফাইল তুলে আনা (ইউজারের নামসহ)
$files_query = "SELECT academic_files.*, users.full_name FROM academic_files 
                JOIN users ON academic_files.user_id = users.id 
                ORDER BY uploaded_at DESC";
$files = mysqli_query($conn, $files_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Academic Resources | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d6efd; --sidebar-bg: #1a1d20; --bg-light: #f4f7f6; }
        body { background-color: var(--bg-light); font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 20px; }
        
        /* Sidebar Styling */
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: var(--sidebar-bg); padding: 20px; color: white; z-index: 1000; }
        .main-content { margin-left: 260px; padding: 30px; }
        .nav-link { color: #adb5bd; padding: 12px; border-radius: 12px; margin-bottom: 5px; transition: 0.3s; border: none; text-align: left; display: flex; align-items: center; }
        .nav-link:hover, .nav-link.active { background: var(--primary-color); color: white; }
        .nav-link i { font-size: 1.2rem; margin-right: 12px; }

        /* Stats & Table Cards */
        .stat-mini-card { border-radius: 20px; border: none; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.03); padding: 20px; display: flex; align-items: center; }
        .table-card { border-radius: 25px; border: none; background: white; box-shadow: 0 10px 40px rgba(0,0,0,0.05); overflow: hidden; }
        
        .resource-icon { width: 40px; height: 40px; background: #f0f7ff; color: var(--primary-color); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .badge-cat { font-size: 10px; text-transform: uppercase; font-weight: 700; padding: 5px 12px; border-radius: 50px; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar shadow">
        <h4 class="fw-bold text-center mb-4 text-primary mt-2">Admin Control</h4>
        <nav class="nav flex-column">
            <a href="index.php" class="nav-link"><i class="bi bi-grid-1x2-fill"></i> <span>Dashboard</span></a>
            <a href="manage_users.php" class="nav-link"><i class="bi bi-people-fill"></i> <span>Manage Users</span></a>
            <a href="manage_lost_found.php" class="nav-link"><i class="bi bi-search"></i> <span>Lost & Found</span></a>
            <a href="manage_academic.php" class="nav-link active"><i class="bi bi-mortarboard-fill"></i> <span>Academic Hub</span></a>
            <a href="manage_content.php" class="nav-link"><i class="bi bi-file-post"></i> <span>Content Moderation</span></a>
            <a href="suggestions.php" class="nav-link"><i class="bi bi-lightbulb-fill"></i> <span>Suggestions</span></a>
            <hr class="text-secondary">
            <a href="../user/dashboard.php" class="nav-link"><i class="bi bi-display"></i> <span>User View</span></a>
            <a href="../auth/logout.php" class="nav-link text-danger"><i class="bi bi-power"></i> <span>Logout</span></a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="fw-bold text-dark">Academic Hub Management</h2>
                <p class="text-muted small">Monitor and manage all routines and course materials.</p>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end gap-3">
                    <div class="stat-mini-card">
                        <div class="resource-icon me-3 bg-primary text-white"><i class="bi bi-calendar3"></i></div>
                        <div><h5 class="mb-0 fw-bold"><?php echo $routine_count; ?></h5><small class="text-muted">Routines</small></div>
                    </div>
                    <div class="stat-mini-card">
                        <div class="resource-icon me-3 bg-success text-white"><i class="bi bi-journal-text"></i></div>
                        <div><h5 class="mb-0 fw-bold"><?php echo $material_count; ?></h5><small class="text-muted">Materials</small></div>
                    </div>
                </div>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">Resource deleted successfully.</div>
        <?php endif; ?>

        <div class="card table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-muted">
                            <th class="ps-4">Resource Info</th>
                            <th>Category</th>
                            <th>Uploaded By</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($files) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($files)): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="resource-icon me-3"><i class="bi bi-file-earmark-zip"></i></div>
                                            <div>
                                                <span class="fw-bold text-dark d-block"><?php echo $row['title']; ?></span>
                                                <small class="text-muted"><?php echo $row['dept']; ?> Department</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary badge-cat">
                                            <?php echo str_replace('_', ' ', $row['category']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-bold"><?php echo $row['full_name']; ?></div>
                                    </td>
                                    <td><small class="text-muted"><?php echo date('M d, Y', strtotime($row['uploaded_at'])); ?></small></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <?php if($row['file_path']): ?>
                                                <a href="../<?php echo $row['file_path']; ?>" class="btn btn-sm btn-light border" download title="Download"><i class="bi bi-download"></i></a>
                                            <?php endif; ?>
                                            <?php if($row['external_link']): ?>
                                                <a href="<?php echo $row['external_link']; ?>" target="_blank" class="btn btn-sm btn-light border text-info" title="Open Link"><i class="bi bi-link-45deg"></i></a>
                                            <?php endif; ?>
                                            <a href="?delete_file=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border text-danger" onclick="return confirm('Delete permanently?')" title="Delete"><i class="bi bi-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted">No academic resources found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>