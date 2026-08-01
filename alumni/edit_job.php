<?php
include '../config.php';

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php?view=jobs");
    exit();
}

$job_id = $_GET['id'];
$u_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM alumni_jobs WHERE id='$job_id' AND alumni_id='$u_id'");
$job = mysqli_fetch_assoc($query);

if(!$job){ 
    header("Location: index.php?view=jobs"); 
    exit(); 
}

if(isset($_POST['update_job'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    $target_dept = $_POST['target_dept']; // নতুন ফিল্ড
    $type = $_POST['type'];
    $vacancy = mysqli_real_escape_string($conn, $_POST['vacancy']); // নতুন ফিল্ড
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $link = mysqli_real_escape_string($conn, $_POST['apply_link']);

    $update_query = "UPDATE alumni_jobs SET 
                    job_title='$title', 
                    company='$company', 
                    location='$loc', 
                    target_dept='$target_dept', 
                    job_type='$type', 
                    vacancy='$vacancy', 
                    description='$desc', 
                    apply_link='$link' 
                    WHERE id='$job_id' AND alumni_id='$u_id'";
    
    if(mysqli_query($conn, $update_query)){
        echo "<script>alert('Job post updated successfully!'); window.location='index.php?view=jobs';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job Post | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; min-height: 100vh; padding: 40px 0; }
        .edit-card { border-radius: 25px; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.1); background: white; width: 100%; max-width: 650px; margin: auto; overflow: hidden; }
        .card-header-premium { background: linear-gradient(135deg, #0d6efd 0%, #4b0082 100%); padding: 30px; text-align: center; color: white; }
        .form-label { font-weight: 700; color: #444; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .premium-input { border-radius: 12px; background: #f8f9fa; border: 1px solid #eee; padding: 12px; font-size: 15px; }
        .premium-input:focus { background: white; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); border-color: #0d6efd; }
        .btn-update { background: linear-gradient(135deg, #0d6efd 0%, #004dc7 100%); border: none; border-radius: 50px; padding: 14px; font-weight: 700; transition: 0.3s; }
        .btn-update:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3); }
    </style>
</head>
<body>

    <div class="container">
        <div class="card edit-card">
            <div class="card-header-premium">
                <i class="bi bi-briefcase-fill display-4 mb-2 d-block"></i>
                <h3 class="fw-bold mb-0">Edit Job Opportunity</h3>
                <p class="small opacity-75 mb-0">Update the details of your job post</p>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form method="POST">
                    <!-- Job Title -->
                    <div class="mb-3">
                        <label class="form-label">Job or Internship Title</label>
                        <input type="text" name="title" class="form-control premium-input" value="<?php echo $job['job_title']; ?>" required>
                    </div>

                    <div class="row">
                        <!-- Company -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company" class="form-control premium-input" value="<?php echo $job['company']; ?>" required>
                        </div>
                        <!-- Job Type -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employment Type</label>
                            <select name="type" class="form-select premium-input">
                                <option value="Full-time" <?php if($job['job_type']=='Full-time') echo 'selected'; ?>>Full-time</option>
                                <option value="Internship" <?php if($job['job_type']=='Internship') echo 'selected'; ?>>Internship</option>
                                <option value="Part-time" <?php if($job['job_type']=='Part-time') echo 'selected'; ?>>Part-time</option>
                                <option value="Contract" <?php if($job['job_type']=='Contract') echo 'selected'; ?>>Contract</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Target Dept -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Target Department</label>
                            <select name="target_dept" class="form-select premium-input" required>
                                <option value="Any" <?php if($job['target_dept']=='Any') echo 'selected'; ?>>Any Department</option>
                                <option value="CSE" <?php if($job['target_dept']=='CSE') echo 'selected'; ?>>CSE</option>
                                <option value="EEE" <?php if($job['target_dept']=='EEE') echo 'selected'; ?>>EEE</option>
                                <option value="BBA" <?php if($job['target_dept']=='BBA') echo 'selected'; ?>>BBA</option>
                                <option value="English" <?php if($job['target_dept']=='English') echo 'selected'; ?>>English</option>
                            </select>
                        </div>
                        <!-- Vacancy -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. of Vacancies</label>
                            <input type="number" name="vacancy" class="form-control premium-input" value="<?php echo $job['vacancy']; ?>" min="1" required>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control premium-input" value="<?php echo $job['location']; ?>" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control premium-input" rows="4" required><?php echo $job['description']; ?></textarea>
                    </div>

                    <!-- Link -->
                    <div class="mb-4">
                        <label class="form-label">Apply Link or Contact Info</label>
                        <input type="text" name="apply_link" class="form-control premium-input" value="<?php echo $job['apply_link']; ?>" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button name="update_job" class="btn btn-primary btn-update text-white shadow">
                            SAVE CHANGES
                        </button>
                        <a href="index.php?view=jobs" class="btn btn-link text-muted text-decoration-none small text-center mt-2">
                            Cancel & Go Back
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>