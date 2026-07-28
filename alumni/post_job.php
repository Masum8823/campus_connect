<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'alumni' && $_SESSION['role'] != 'admin')){
    header("Location: index.php");
    exit();
}

if(isset($_POST['post_job'])){
    $alumni_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $type = $_POST['type'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $link = mysqli_real_escape_string($conn, $_POST['apply_link']);

    $query = "INSERT INTO alumni_jobs (alumni_id, job_title, company, location, job_type, description, apply_link) 
              VALUES ('$alumni_id', '$title', '$company', '$location', '$type', '$desc', '$link')";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Job Opportunity Posted!'); window.location='index.php?view=jobs';</script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Job Opportunity | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 50px; padding-bottom: 50px; }
        .form-card { border-radius: 25px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.05); background: white; }
        .form-label { font-weight: 600; color: #444; font-size: 14px; }
        .form-control, .form-select { border-radius: 12px; padding: 12px; border: 1px solid #eee; background: #fdfdfd; }
        .form-control:focus { box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1); border-color: #0d6efd; }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card form-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-briefcase-fill text-primary display-4"></i>
                        <h2 class="fw-bold mt-2">Post a Job Opportunity</h2>
                        <p class="text-muted small">Help your juniors find their career path</p>
                    </div>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Job or Internship Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Junior Web Developer / Marketing Intern" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company" class="form-control" placeholder="e.g. Brain Station 23" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Employment Type</label>
                                <select name="type" class="form-select">
                                    <option value="Full-time">Full-time</option>
                                    <option value="Internship">Internship</option>
                                    <option value="Part-time">Part-time</option>
                                    <option value="Contract">Contract</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Job Location</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Dhaka (Remote / On-site)" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Job Description (Short)</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Briefly describe the requirements and responsibilities..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Apply Link or Contact Email</label>
                            <input type="text" name="apply_link" class="form-control" placeholder="https://linkedin.com/jobs/... or email address" required>
                        </div>

                        <button name="post_job" class="btn btn-primary w-100 fw-bold py-3 rounded-pill shadow-sm">
                            <i class="bi bi-cloud-upload-fill me-2"></i> PUBLISH OPPORTUNITY
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="index.php" class="text-decoration-none small text-muted">← Back to Alumni Hub</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>