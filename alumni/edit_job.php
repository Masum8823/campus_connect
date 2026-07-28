<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php?view=jobs");
    exit();
}

$job_id = $_GET['id'];
$u_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM alumni_jobs WHERE id='$job_id' AND alumni_id='$u_id'");
$job = mysqli_fetch_assoc($query);

if(!$job){ header("Location: index.php?view=jobs"); exit(); }

if(isset($_POST['update_job'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    $type = $_POST['type'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $link = mysqli_real_escape_string($conn, $_POST['apply_link']);

    mysqli_query($conn, "UPDATE alumni_jobs SET job_title='$title', company='$company', location='$loc', job_type='$type', description='$desc', apply_link='$link' WHERE id='$job_id'");
    header("Location: index.php?view=jobs");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Job Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; min-height: 100vh; }
        .edit-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 600px; margin: auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card edit-card p-4">
            <h4 class="fw-bold text-primary mb-4 text-center">Edit Job Post</h4>
            <form method="POST">
                <input type="text" name="title" class="form-control mb-3" value="<?php echo $job['job_title']; ?>" required>
                <div class="row">
                    <div class="col-6 mb-3"><input type="text" name="company" class="form-control" value="<?php echo $job['company']; ?>" required></div>
                    <div class="col-6 mb-3">
                        <select name="type" class="form-select">
                            <option value="Full-time" <?php if($job['job_type']=='Full-time') echo 'selected'; ?>>Full-time</option>
                            <option value="Internship" <?php if($job['job_type']=='Internship') echo 'selected'; ?>>Internship</option>
                        </select>
                    </div>
                </div>
                <input type="text" name="location" class="form-control mb-3" value="<?php echo $job['location']; ?>" required>
                <textarea name="description" class="form-control mb-3" rows="4" required><?php echo $job['description']; ?></textarea>
                <input type="text" name="apply_link" class="form-control mb-4" value="<?php echo $job['apply_link']; ?>" required>
                
                <div class="d-grid gap-2">
                    <button name="update_job" class="btn btn-primary rounded-pill fw-bold">UPDATE POST</button>
                    <a href="index.php?view=jobs" class="btn btn-light rounded-pill">CANCEL</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>