<?php
include '../config.php';

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: stories.php");
    exit();
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM alumni_stories WHERE id='$id' AND user_id='$user_id'");
$story = mysqli_fetch_assoc($query);

if(!$story){ header("Location: stories.php"); exit(); }

if(isset($_POST['update_journey'])){
    $job = mysqli_real_escape_string($conn, $_POST['job']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $story_text = mysqli_real_escape_string($conn, $_POST['story']);
    $roadmap = mysqli_real_escape_string($conn, $_POST['roadmap']);
    $mistake = mysqli_real_escape_string($conn, $_POST['mistake']);
    $advice = mysqli_real_escape_string($conn, $_POST['advice']);
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);

    $update = "UPDATE alumni_stories SET 
               current_job_title='$job', company_name='$company', journey_story='$story_text', 
               career_roadmap='$roadmap', biggest_mistake='$mistake', advice_to_juniors='$advice', 
               skills_used='$skills', first_salary='$salary', is_edited=1 
               WHERE id='$id'";
    
    if(mysqli_query($conn, $update)){
        header("Location: view_journey.php?id=$id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit My Journey | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding: 40px 0; }
        .edit-card { border-radius: 25px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); background: white; width: 100%; max-width: 800px; margin: auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card edit-card p-4 p-md-5">
            <h3 class="fw-bold text-primary mb-4 text-center">Edit Your Career Journey</h3>
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="small fw-bold">Job Title</label><input type="text" name="job" class="form-control" value="<?php echo $story['current_job_title']; ?>" required></div>
                    <div class="col-md-6 mb-3"><label class="small fw-bold">Company</label><input type="text" name="company" class="form-control" value="<?php echo $story['company_name']; ?>" required></div>
                </div>
                <div class="mb-3"><label class="small fw-bold">Full Success Story</label><textarea name="story" class="form-control" rows="6" required><?php echo $story['journey_story']; ?></textarea></div>
                <div class="mb-3"><label class="small fw-bold">Career Roadmap</label><textarea name="roadmap" class="form-control" rows="4"><?php echo $story['career_roadmap']; ?></textarea></div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="small fw-bold text-danger">Biggest Mistake</label><textarea name="mistake" class="form-control" rows="2"><?php echo $story['biggest_mistake']; ?></textarea></div>
                    <div class="col-md-6 mb-3"><label class="small fw-bold text-success">Golden Advice</label><textarea name="advice" class="form-control" rows="2"><?php echo $story['advice_to_juniors']; ?></textarea></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-4"><label class="small fw-bold">Tech Stack</label><input type="text" name="skills" class="form-control" value="<?php echo $story['skills_used']; ?>"></div>
                    <div class="col-md-6 mb-4"><label class="small fw-bold">First Salary</label><input type="text" name="salary" class="form-control" value="<?php echo $story['first_salary']; ?>"></div>
                </div>
                <div class="d-grid gap-2">
                    <button name="update_journey" class="btn btn-primary rounded-pill fw-bold py-2">UPDATE JOURNEY</button>
                    <a href="stories.php" class="btn btn-light rounded-pill">CANCEL</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>