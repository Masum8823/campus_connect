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
    $target_dept = $_POST['target_dept']; // New field
    $type = $_POST['type'];
    $vacancy = mysqli_real_escape_string($conn, $_POST['vacancy']); // New field
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $link = mysqli_real_escape_string($conn, $_POST['apply_link']);

    $query = "INSERT INTO alumni_jobs (alumni_id, job_title, company, location, target_dept, job_type, vacancy, description, apply_link) 
              VALUES ('$alumni_id', '$title', '$company', '$location', '$target_dept', '$type', '$vacancy', '$desc', '$link')";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Job Opportunity Posted!'); window.location='index.php?view=jobs';</script>";
        exit();
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
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card form-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-briefcase-fill text-primary display-4"></i>
                        <h2 class="fw-bold mt-2">Publish Job Opportunity</h2>
                    </div>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Job or Internship Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Junior Web Developer" required>
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
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Target Department</label>
                                <select name="target_dept" class="form-select" required>
                                    <option value="Any">Any Department</option>
                                    <option value="CSE">CSE</option>
                                    <option value="EEE">EEE</option>
                                    <option value="BBA">BBA</option>
                                    <option value="English">English</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No. of Vacancies</label>
                                <input type="number" name="vacancy" class="form-control" placeholder="e.g. 5" min="1" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Dhaka (On-site)" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Apply Link or Contact Info</label>
                            <input type="text" name="apply_link" class="form-control" placeholder="https://..." required>
                        </div>

                        <button name="post_job" class="btn btn-primary w-100 fw-bold py-3 rounded-pill">PUBLISH NOW</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>