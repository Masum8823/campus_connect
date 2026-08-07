<?php
include '../config.php';

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php?view=jobs");
    exit();
}

$job_id = $_GET['id'];
$u_id = $_SESSION['user_id'];

// ১. ডাটাবেস থেকে বর্তমান তথ্য তুলে আনা
$query = mysqli_query($conn, "SELECT * FROM alumni_jobs WHERE id='$job_id' AND alumni_id='$u_id'");
$job = mysqli_fetch_assoc($query);

if(!$job){ header("Location: index.php?view=jobs"); exit(); }

// --- এই সেই লাইনটি: ডাটাবেসের কমা দেওয়া স্ট্রিং থেকে অ্যারে বানানো ---
$selected_depts = explode(', ', $job['target_dept']); 

// ২. আপডেট লজিক
if(isset($_POST['update_job'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    $type = $_POST['type'];
    $vacancy = mysqli_real_escape_string($conn, $_POST['vacancy']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $link = mysqli_real_escape_string($conn, $_POST['apply_link']);

    // চেক-বক্স থেকে আসা ডিপার্টমেন্টগুলো হ্যান্ডেল করা
    if(!empty($_POST['target_dept'])){
        $target_dept = implode(', ', $_POST['target_dept']);
    } else {
        $target_dept = "Any Department";
    }

    $update_query = "UPDATE alumni_jobs SET 
                    job_title='$title', company='$company', location='$loc', 
                    target_dept='$target_dept', job_type='$type', vacancy='$vacancy', 
                    description='$desc', apply_link='$link' 
                    WHERE id='$job_id' AND alumni_id='$u_id'";
    
    if(mysqli_query($conn, $update_query)){
        echo "<script>alert('Job post updated!'); window.location='index.php?view=jobs';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Job Post | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding: 40px 0; }
        .edit-card { border-radius: 25px; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.1); background: white; width: 100%; max-width: 650px; margin: auto; overflow: hidden; }
        .card-header-premium { background: linear-gradient(135deg, #0d6efd 0%, #4b0082 100%); padding: 30px; text-align: center; color: white; }
        .form-label { font-weight: 700; color: #444; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .premium-input { border-radius: 12px; background: #f8f9fa; border: 1px solid #eee; padding: 12px; }
        .dept-checkbox-group { background: #f8f9fa; border-radius: 12px; padding: 15px; border: 1px solid #eee; }
    </style>
</head>
<body>

    <div class="container">
        <div class="card edit-card">
            <div class="card-header-premium">
                <i class="bi bi-pencil-square display-4 mb-2 d-block"></i>
                <h3 class="fw-bold mb-0">Edit Job Opportunity</h3>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label">Job Title</label>
                        <input type="text" name="title" class="form-control premium-input" value="<?php echo $job['job_title']; ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Company</label>
                            <input type="text" name="company" class="form-control premium-input" value="<?php echo $job['company']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Job Type</label>
                            <select name="type" class="form-select premium-input">
                                <option value="Full-time" <?php if($job['job_type']=='Full-time') echo 'selected'; ?>>Full-time</option>
                                <option value="Internship" <?php if($job['job_type']=='Internship') echo 'selected'; ?>>Internship</option>
                                <option value="Part-time" <?php if($job['job_type']=='Part-time') echo 'selected'; ?>>Part-time</option>
                            </select>
                        </div>
                    </div>

                    <!-- ৩. মাল্টিপল ডিপার্টমেন্ট চেক-বক্স (অটো-টিক দেওয়া) -->
                    <div class="mb-4">
                        <label class="form-label">Update Target Department(s)</label>
                        <div class="dept-checkbox-group">
                            <div class="row">
                                <?php 
                                $depts = ['CSE', 'EEE', 'BBA', 'English', 'Civil', 'Pharmacy', 'Law'];
                                foreach($depts as $d): ?>
                                    <div class="col-6 col-md-4 mb-2">
                                        <div class="form-check">
                                            <!-- এখানে in_array ব্যবহার করে চেক করা হয়েছে সেটি আগে সিলেক্ট করা ছিল কি না -->
                                            <input class="form-check-input" type="checkbox" name="target_dept[]" value="<?php echo $d; ?>" id="dept_<?php echo $d; ?>" <?php echo in_array($d, $selected_depts) ? 'checked' : ''; ?>>
                                            <label class="form-check-label small fw-bold" for="dept_<?php echo $d; ?>"><?php echo $d; ?></label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">No. of Vacancies</label>
                            <input type="number" name="vacancy" class="form-control premium-input" value="<?php echo $job['vacancy']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control premium-input" value="<?php echo $job['location']; ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control premium-input" rows="4" required><?php echo $job['description']; ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Apply Link</label>
                        <input type="text" name="apply_link" class="form-control premium-input" value="<?php echo $job['apply_link']; ?>" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button name="update_job" class="btn btn-primary rounded-pill fw-bold py-3 shadow">UPDATE CHANGES</button>
                        <a href="index.php?view=jobs" class="text-center text-muted mt-2 text-decoration-none small">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>