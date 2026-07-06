<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'teacher' && $_SESSION['role'] != 'admin')){
    header("Location: assignments.php");
    exit();
}

if(!isset($_GET['id'])){
    header("Location: assignments.php");
    exit();
}

$assign_id = $_GET['id'];

$assign_info = mysqli_query($conn, "SELECT title FROM assignments WHERE id='$assign_id'");
$assign = mysqli_fetch_assoc($assign_info);

if(!$assign){
    echo "Assignment not found!";
    exit();
}

$submissions_query = "SELECT assignment_submissions.*, users.full_name, users.university_id, users.dept 
                      FROM assignment_submissions 
                      JOIN users ON assignment_submissions.student_id = users.id 
                      WHERE assignment_submissions.assignment_id = '$assign_id' 
                      ORDER BY submitted_at ASC";
$submissions = mysqli_query($conn, $submissions_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Submissions - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .table-card { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary shadow mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="assignments.php">← Back to Assignments</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="mb-4">
            <h5 class="text-muted mb-1">Submissions for:</h5>
            <h3 class="fw-bold text-dark"><?php echo $assign['title']; ?></h3>
        </div>

        <div class="card table-card bg-white p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Department</th>
                            <th>Submission Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($submissions) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($submissions)): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo $row['full_name']; ?></div>
                                    </td>
                                    <td><span class="text-muted small"><?php echo $row['university_id']; ?></span></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?php echo $row['dept']; ?></span></td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('M d, Y', strtotime($row['submitted_at'])); ?> <br>
                                            <?php echo date('h:i A', strtotime($row['submitted_at'])); ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <a href="../<?php echo $row['submission_file']; ?>" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" download>
                                            <i class="bi bi-download"></i> Download Work
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-person-x display-4 d-block mb-2"></i>
                                    No student has submitted this assignment yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>