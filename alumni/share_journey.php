<?php
include '../config.php';
session_start();

// Security: Only Alumni and Admin can access this page
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
        echo "<script>alert('Your journey has been shared!'); window.location='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Share Your Journey - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; padding-top: 50px; padding-bottom: 50px; }
        .form-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card form-card p-4">
                    <h2 class="fw-bold text-primary mb-4 text-center">Share Your Career Journey</h2>
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Current Job Title</label>
                                <input type="text" name="job" class="form-control" placeholder="e.g. Software Engineer" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Company Name</label>
                                <input type="text" name="company" class="form-control" placeholder="e.g. Google / Freelance" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Your Success Story</label>
                            <textarea name="story" class="form-control" rows="5" placeholder="How did you start? Tell us your journey..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">Career Roadmap (1st Year to Job)</label>
                            <textarea name="roadmap" class="form-control" rows="4" placeholder="Advice on what to do in each year of university..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Biggest Mistake in Uni Life</label>
                                <textarea name="mistake" class="form-control" rows="2" placeholder="What should juniors avoid?"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Advice to Juniors</label>
                                <textarea name="advice" class="form-control" rows="2" placeholder="One piece of gold advice..."></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">Current Tech Stack / Skills</label>
                                <input type="text" name="skills" class="form-control" placeholder="e.g. React, Node.js, AWS">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">First Salary (Optional)</label>
                                <input type="text" name="salary" class="form-control" placeholder="e.g. 30,000 BDT">
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button name="post_journey" class="btn btn-primary btn-lg fw-bold rounded-pill">Publish My Journey</button>
                            <a href="index.php" class="btn btn-link mt-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>