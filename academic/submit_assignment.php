<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: assignments.php");
    exit();
}

if(!isset($_GET['id'])){
    header("Location: assignments.php");
    exit();
}

$assign_id = $_GET['id'];
$student_id = $_SESSION['user_id'];

$assign_query = mysqli_query($conn, "SELECT * FROM assignments WHERE id='$assign_id'");
$assign = mysqli_fetch_assoc($assign_query);

if(!$assign){
    echo "Assignment not found!";
    exit();
}

$check_sub = mysqli_query($conn, "SELECT * FROM assignment_submissions WHERE assignment_id='$assign_id' AND student_id='$student_id'");
$existing_sub = mysqli_fetch_assoc($check_sub);

if(isset($_POST['submit_work'])){
    $current_time = date('Y-m-d H:i:s');
    
    if($current_time > $assign['deadline']){
        echo "<script>alert('Error: Deadline has passed!'); window.location='assignments.php';</script>";
        exit();
    }

    if(!empty($_FILES['sub_file']['name'])){
        $file_name = time() . "_sub_" . $_FILES['sub_file']['name'];
        $target = "../uploads/submissions/" . $file_name;
        $db_path = "uploads/submissions/" . $file_name;

        if(move_uploaded_file($_FILES['sub_file']['tmp_name'], $target)){
            if($existing_sub){
                mysqli_query($conn, "UPDATE assignment_submissions SET submission_file='$db_path', submitted_at='$current_time' WHERE id='".$existing_sub['id']."'");
            } else {
                mysqli_query($conn, "INSERT INTO assignment_submissions (assignment_id, student_id, submission_file) VALUES ('$assign_id', '$student_id', '$db_path')");
            }
            echo "<script>alert('Assignment Submitted Successfully!'); window.location='assignments.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Assignment - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary shadow mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="assignments.php">← Back to Hub</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow border-0" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold text-dark mb-1"><?php echo $assign['title']; ?></h4>
                        <p class="text-danger small fw-bold mb-4">
                            <i class="bi bi-alarm"></i> Deadline: <?php echo date('M d, Y - h:i A', strtotime($assign['deadline'])); ?>
                        </p>

                        <div class="alert alert-secondary small">
                            <strong>Instructions Summary:</strong><br>
                            <?php echo nl2br(substr($assign['description'], 0, 200)); ?>...
                        </div>

                        <?php 
                        $is_late = date('Y-m-d H:i:s') > $assign['deadline'];
                        if($is_late): 
                        ?>
                            <div class="alert alert-danger fw-bold text-center">
                                <i class="bi bi-x-circle"></i> Submission Closed! Deadline passed.
                            </div>
                        <?php else: ?>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Upload Your Work (PDF, ZIP, or Image)</label>
                                    <input type="file" name="sub_file" class="form-control" required>
                                    <?php if($existing_sub): ?>
                                        <div class="mt-2 small text-success">
                                            <i class="bi bi-check-circle"></i> You already submitted: 
                                            <a href="../<?php echo $existing_sub['submission_file']; ?>" target="_blank">View Old File</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <button name="submit_work" class="btn btn-success w-100 fw-bold py-2">
                                    <?php echo $existing_sub ? 'Update Submission' : 'Submit Assignment'; ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>