<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'teacher' && $_SESSION['role'] != 'admin')){
    header("Location: assignments.php");
    exit();
}

if(isset($_POST['create_assignment'])){
    $teacher_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $deadline = $_POST['deadline'];
    $dept = $_POST['dept'];

    $db_path = NULL;
    if(!empty($_FILES['instruction_file']['name'])){
        $file_name = time() . "_" . $_FILES['instruction_file']['name'];
        $target = "../uploads/academic/" . $file_name;
        if(move_uploaded_file($_FILES['instruction_file']['tmp_name'], $target)){
            $db_path = "uploads/academic/" . $file_name;
        }
    }

    $query = "INSERT INTO assignments (teacher_id, title, description, deadline, file_path, dept) 
              VALUES ('$teacher_id', '$title', '$desc', '$deadline', " . ($db_path ? "'$db_path'" : "NULL") . ", '$dept')";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Assignment Posted!'); window.location='assignments.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container card p-4 shadow-sm mx-auto" style="max-width: 600px; border-radius: 15px;">
        <h4 class="text-center text-primary mb-4 fw-bold">Post New Assignment</h4>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" class="form-control mb-3" placeholder="Assignment Title" required>
            <textarea name="description" class="form-control mb-3" rows="4" placeholder="Detailed Instructions..." required></textarea>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="small fw-bold">Deadline</label>
                    <input type="datetime-local" name="deadline" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="small fw-bold">Department</label>
                    <select name="dept" class="form-control" required>
                        <option value="CSE">CSE</option>
                        <option value="EEE">EEE</option>
                        <option value="BBA">BBA</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="small fw-bold">Instruction File (Optional)</label>
                <input type="file" name="instruction_file" class="form-control">
            </div>

            <button name="create_assignment" class="btn btn-primary w-100 fw-bold py-2">Publish Assignment</button>
            <a href="assignments.php" class="btn btn-link w-100 text-decoration-none mt-2">Cancel</a>
        </form>
    </div>
</body>
</html>