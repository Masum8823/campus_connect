<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php"); exit();
}

if(isset($_GET['delete_user'])){
    $u_id = $_GET['delete_user'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$u_id'");
    header("Location: manage_users.php");
}

$users = mysqli_query($conn, "SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        .sidebar { position: fixed; left: 0; top: 0; bottom: 0; width: 260px; background: #212529; padding: 20px; color: white; }
        .main-content { margin-left: 260px; padding: 40px; }
        .user-table { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="fw-bold text-center mb-4 text-primary">Admin Control</h4>
        <nav class="nav flex-column">
            <a href="index.php" class="nav-link text-white"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="manage_users.php" class="nav-link active bg-primary text-white"><i class="bi bi-people me-2"></i> Manage Users</a>
            <a href="manage_content.php" class="nav-link text-white"><i class="bi bi-file-post me-2"></i> Content Moderation</a>
            <hr>
            <a href="../user/dashboard.php" class="nav-link text-white"><i class="bi bi-arrow-left-circle me-2"></i> User View</a>
        </nav>
    </div>

    <div class="main-content">
        <h3 class="fw-bold mb-4">User Management</h3>
        <div class="user-table p-3">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dept</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($u = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td><strong><?php echo $u['full_name']; ?></strong><br><small class="text-muted"><?php echo $u['university_id']; ?></small></td>
                            <td><?php echo $u['email']; ?></td>
                            <td><span class="badge bg-secondary"><?php echo strtoupper($u['role']); ?></span></td>
                            <td><?php echo $u['dept']; ?></td>
                            <td><?php echo $u['is_verified'] ? '<span class="text-success small fw-bold">Verified</span>' : '<span class="text-danger small fw-bold">Pending</span>'; ?></td>
                            <td>
                                <a href="?delete_user=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Permanently delete this user?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>