<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php"); exit();
}

if(isset($_POST['update_role'])){
    $u_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];
    mysqli_query($conn, "UPDATE users SET role='$new_role' WHERE id='$u_id'");
    echo "<script>alert('Role updated!'); window.location='manage_users.php';</script>";
}

if(isset($_GET['verify'])){
    $u_id = $_GET['verify'];
    mysqli_query($conn, "UPDATE users SET is_verified=1 WHERE id='$u_id'");
    header("Location: manage_users.php");
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
        .user-table-card { background: white; border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="fw-bold text-center mb-4 text-primary">Admin Control</h4>
        <nav class="nav flex-column">
            <a href="index.php" class="nav-link text-white"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
            <a href="manage_users.php" class="nav-link active bg-primary text-white shadow-sm"><i class="bi bi-people me-2"></i> Manage Users</a>
            <a href="manage_lost_found.php" class="nav-link text-white"><i class="bi bi-search me-2"></i> Lost & Found</a>
            <a href="manage_content.php" class="nav-link text-white"><i class="bi bi-file-post me-2"></i> Content Moderation</a>
            <hr>
            <a href="../user/dashboard.php" class="nav-link text-white"><i class="bi bi-arrow-left-circle me-2"></i> User View</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">User Management</h3>
            <span class="badge bg-dark px-3 py-2"><?php echo mysqli_num_rows($users); ?> Total Registered Users</span>
        </div>

        <div class="card user-table-card p-3">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>User Profile</th>
                        <th>Department</th>
                        <th>Verification</th>
                        <th>Change Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($u = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="fw-bold"><?php echo $u['full_name']; ?></div>
                                    <div class="ms-2 small text-muted">(ID: <?php echo $u['university_id']; ?>)</div>
                                </div>
                                <small class="text-primary"><?php echo $u['email']; ?></small>
                            </td>
                            <td><?php echo $u['dept']; ?></td>
                            <td>
                                <?php if($u['is_verified']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Verified</span>
                                <?php else: ?>
                                    <a href="?verify=<?php echo $u['id']; ?>" class="btn btn-xs btn-warning py-0" style="font-size: 10px;">Verify Manually</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" class="d-flex gap-1">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <select name="new_role" class="form-select form-select-sm" style="width: 100px;">
                                        <option value="student" <?php if($u['role'] == 'student') echo 'selected'; ?>>Student</option>
                                        <option value="teacher" <?php if($u['role'] == 'teacher') echo 'selected'; ?>>Teacher</option>
                                        <option value="alumni" <?php if($u['role'] == 'alumni') echo 'selected'; ?>>Alumni</option>
                                    </select>
                                    <button name="update_role" class="btn btn-sm btn-dark"><i class="bi bi-check-lg"></i></button>
                                </form>
                            </td>
                            <td>
                                <a href="?delete_user=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this user permanently?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>