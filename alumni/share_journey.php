<?php
include '../config.php';

if(!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'alumni' && $_SESSION['role'] != 'admin')){
    header("Location: index.php");
    exit();
}

if(isset($_POST['post_journey'])){
    $user_id = $_SESSION['user_id'];
    $job = mysqli_real_escape_string($conn, $_POST['job']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $story = mysqli_real_escape_string($conn, $_POST['story']);
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);
    $roadmap = mysqli_real_escape_string($conn, $_POST['roadmap']);
    $mistake = mysqli_real_escape_string($conn, $_POST['mistake']);
    $advice = mysqli_real_escape_string($conn, $_POST['advice']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);

    $query = "INSERT INTO alumni_stories (user_id, current_job_title, company_name, journey_story, skills_used, career_roadmap, biggest_mistake, advice_to_juniors, first_salary) 
              VALUES ('$user_id', '$job', '$company', '$story', '$skills', '$roadmap', '$mistake', '$advice', '$salary')";
    
    if(mysqli_query($conn, $query)){
        $alumni_name = $_SESSION['user_name'];
        $feed_content = "🌟 [ALUMNI SUCCESS STORY] \nI'm $alumni_name, currently working as a $job at $company. I've just shared my full career journey and roadmap in the Alumni Hub. Hope it helps my juniors! \n\nCheck out the Alumni Hub for more details.";
        
        mysqli_query($conn, "INSERT INTO posts (user_id, content) VALUES ('$user_id', '$feed_content')");

        echo "<script>alert('Your journey has been shared and announced on the main feed!'); window.location='index.php';</script>";
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
    <title>Share Your Journey | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 50px; padding-bottom: 50px; }
        .form-card { border-radius: 25px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.05); background: white; }
        .form-label { font-weight: 700; color: #444; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        .premium-input { border-radius: 12px; background: #f8f9fa; border: 1px solid #eee; padding: 12px; font-size: 15px; }
        .premium-input:focus { background: white; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); border-color: #0d6efd; }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card form-card p-4 p-md-5">
                    <div class="text-center mb-5">
                        <i class="bi bi-stars text-warning display-4"></i>
                        <h2 class="fw-bold mt-2">Inspire Your Juniors</h2>
                        <p class="text-muted small">Your story will be shared in the Alumni Hub and on the Main Feed.</p>
                    </div>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Current Job Title</label>
                                <input type="text" name="job" class="form-control premium-input" placeholder="e.g. Software Engineer" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company" class="form-control premium-input" placeholder="e.g. Google / Freelance" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Your Success Story</label>
                            <textarea name="story" class="form-control premium-input" rows="6" placeholder="Tell us how you achieved your goals..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Career Roadmap (Year 1 to Job)</label>
                            <textarea name="roadmap" class="form-control premium-input" rows="4" placeholder="What steps should a student follow?"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label text-danger">Biggest Mistake in Uni Life</label>
                                <textarea name="mistake" class="form-control premium-input" rows="2" placeholder="What should they avoid?"></textarea>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label text-success">Golden Advice</label>
                                <textarea name="advice" class="form-control premium-input" rows="2" placeholder="One important tip..."></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Tech Stack / Skills</label>
                                <input type="text" name="skills" class="form-control premium-input" placeholder="e.g. PHP, Python, UI Design">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">First Salary (Optional)</label>
                                <input type="text" name="salary" class="form-control premium-input" placeholder="e.g. 40k+ BDT">
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button name="post_journey" class="btn btn-primary btn-lg fw-bold rounded-pill py-3 shadow">
                                PUBLISH & ANNOUNCE
                            </button>
                            <a href="index.php" class="btn btn-link text-muted mt-2 text-decoration-none small">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>