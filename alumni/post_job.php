<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'alumni' && $_SESSION['role'] != 'admin')){
    header("Location: index.php"); exit();
}

if(isset($_POST['post_job'])){
    $alumni_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $type = $_POST['type'];
    $vacancy = mysqli_real_escape_string($conn, $_POST['vacancy']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $link = mysqli_real_escape_string($conn, $_POST['apply_link']);

    // মাল্টিপল ডিপার্টমেন্ট হ্যান্ডেল করা
    if(!empty($_POST['target_dept'])){
        $target_dept = implode(', ', $_POST['target_dept']); // অ্যারে থেকে স্ট্রিং বানানো
    } else {
        $target_dept = "Any Department";
    }

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
        .form-label { font-weight: 700; color: #444; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .premium-input { border-radius: 12px; background: #f8f9fa; border: 1px solid #eee; padding: 12px; font-size: 15px; }
        .dept-checkbox-group { background: #f8f9fa; border-radius: 12px; padding: 15px; border: 1px solid #eee; }
        .form-check-label { font-size: 14px; font-weight: 500; cursor: pointer; }
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
                        <div class="mb-4">
                            <label class="form-label">Job or Internship Title</label>
                            <input type="text" name="title" class="form-control premium-input" placeholder="e.g. Junior Software Engineer" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company" class="form-control premium-input" placeholder="e.g. Google" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Employment Type</label>
                                <select name="type" class="form-select premium-input">
                                    <option value="Full-time">Full-time</option>
                                    <option value="Internship">Internship</option>
                                    <option value="Part-time">Part-time</option>
                                </select>
                            </div>
                        </div>

                        <!-- NEW: Multiple Dept Selection with Checkboxes -->
                        <div class="mb-4">
                            <label class="form-label">Target Department(s)</label>
                            <div class="dept-checkbox-group shadow-sm">
                                <div class="row">
                                    <?php 
                                    $departments = ['CSE', 'EEE', 'BBA', 'English', 'Civil', 'Pharmacy', 'Law'];
                                    foreach($departments as $d): ?>
                                        <div class="col-6 col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="target_dept[]" value="<?php echo $d; ?>" id="dept_<?php echo $d; ?>">
                                                <label class="form-check-label" for="dept_<?php echo $d; ?>"><?php echo $d; ?></label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <small class="text-muted mt-2 d-block border-top pt-2">Select all that apply. If none, it defaults to 'Any Department'.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">No. of Vacancies</label>
                                <input type="number" name="vacancy" class="form-control premium-input" placeholder="e.g. 5" min="1" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Location</label>
                                <input type="text" name="location" class="form-control premium-input" placeholder="e.g. Dhaka (On-site)" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control premium-input" rows="4" required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Apply Link or Contact Info</label>
                            <input type="text" name="apply_link" class="form-control premium-input" placeholder="https://..." required>
                        </div>

                        <button name="post_job" class="btn btn-primary w-100 fw-bold py-3 rounded-pill shadow">PUBLISH NOW</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>