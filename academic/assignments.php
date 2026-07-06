<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$user_dept = $_SESSION['dept'];

// Fetch assignments based on role/dept
if($user_role == 'teacher' || $user_role == 'admin'){
    $query = "SELECT assignments.*, users.full_name FROM assignments 
              JOIN users ON assignments.teacher_id = users.id 
              ORDER BY created_at DESC";
} else {
    $query = "SELECT assignments.*, users.full_name FROM assignments 
              JOIN users ON assignments.teacher_id = users.id 
              WHERE assignments.dept = '$user_dept' 
              ORDER BY created_at DESC";
}

$assignments = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assignments - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .assign-card { border-radius: 15px; border: none; transition: 0.3s; height: 100%; }
        .assign-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .deadline-box { font-size: 13px; font-weight: bold; color: #dc3545; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary shadow mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-file-earmark-text"></i> Assignments Hub</a>
            <a href="../user/dashboard.php" class="btn btn-light btn-sm fw-bold">Dashboard</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-secondary">Academic Assignments</h3>
            <?php if($user_role == 'teacher' || $user_role == 'admin'): ?>
                <a href="create_assignment.php" class="btn btn-primary fw-bold shadow-sm">+ Create Assignment</a>
            <?php endif; ?>
        </div>

        <div class="row">
            <?php if(mysqli_num_rows($assignments) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($assignments)): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card assign-card shadow-sm">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3"><?php echo $row['dept']; ?></span>
                                    <div class="deadline-box">
                                        <i class="bi bi-clock-history"></i> Deadline: <?php echo date('M d, h:i A', strtotime($row['deadline'])); ?>
                                    </div>
                                </div>
                                
                                <h5 class="card-title fw-bold text-dark mt-2"><?php echo $row['title']; ?></h5>
                                
                                <!-- Short Description -->
                                <p class="card-text text-muted small mb-3">
                                    <strong>Instructions:</strong><br>
                                    <?php echo nl2br(substr($row['description'], 0, 120)); ?>...
                                    <a href="#" class="text-primary text-decoration-none fw-bold" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $row['id']; ?>">Read Full</a>
                                </p>
                                
                                <div class="p-2 bg-light rounded mb-3 mt-auto">
                                    <small class="text-muted">Posted By: <strong><?php echo $row['full_name']; ?></strong></small>
                                </div>

                                <div class="d-flex gap-2">
                                    <?php if(!empty($row['file_path'])): ?>
                                        <a href="../<?php echo $row['file_path']; ?>" class="btn btn-sm btn-outline-dark" download>
                                            <i class="bi bi-download"></i> Instruction File
                                        </a>
                                    <?php endif; ?>

                                    <?php if($user_role == 'student'): ?>
                                        <a href="submit_assignment.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success flex-grow-1 fw-bold">
                                            <i class="bi bi-cloud-arrow-up"></i> Submit Now
                                        </a>
                                    <?php else: ?>
                                        <a href="view_submissions.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary flex-grow-1 fw-bold">
                                            <i class="bi bi-people"></i> View Submissions
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Full Instructions Modal -->
                    <div class="modal fade" id="viewModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title fw-bold"><?php echo $row['title']; ?></h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="mb-3 d-flex justify-content-between">
                                        <span class="badge bg-secondary">Dept: <?php echo $row['dept']; ?></span>
                                        <span class="text-danger fw-bold"><i class="bi bi-alarm"></i> Deadline: <?php echo date('F d, Y - h:i A', strtotime($row['deadline'])); ?></span>
                                    </div>
                                    <h6 class="fw-bold border-bottom pb-2">Full Instructions:</h6>
                                    <p class="text-dark" style="white-space: pre-line; line-height: 1.6;">
                                        <?php echo $row['description']; ?>
                                    </p>
                                    
                                    <?php if(!empty($row['file_path'])): ?>
                                        <div class="mt-4 p-3 bg-light rounded border">
                                            <h6 class="small fw-bold">Attached Resource:</h6>
                                            <a href="../<?php echo $row['file_path']; ?>" class="btn btn-sm btn-primary" download><i class="bi bi-file-earmark-arrow-down"></i> Download Instruction File</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <?php if($user_role == 'student'): ?>
                                        <a href="submit_assignment.php?id=<?php echo $row['id']; ?>" class="btn btn-success px-4">Go to Submission</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal End -->

                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-journal-x display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">No assignments posted yet.</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>