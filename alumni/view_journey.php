<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$current_user_id = $_SESSION['user_id'];

$query = "SELECT alumni_stories.*, users.full_name, users.profile_pic, users.dept, users.id as alumni_id 
          FROM alumni_stories 
          JOIN users ON alumni_stories.user_id = users.id 
          WHERE alumni_stories.id = '$id'";
$result = mysqli_query($conn, $query);
$story = mysqli_fetch_assoc($result);

if(!$story){ echo "Journey not found!"; exit(); }

if(isset($_POST['ask_question'])){
    $q_text = mysqli_real_escape_string($conn, $_POST['question']);
    mysqli_query($conn, "INSERT INTO alumni_qna (story_id, student_id, question_text) VALUES ('$id', '$current_user_id', '$q_text')");
    header("Location: view_journey.php?id=$id&msg=q_sent");
    exit();
}

if(isset($_POST['submit_answer'])){
    $ans_text = mysqli_real_escape_string($conn, $_POST['answer']);
    $q_id = $_POST['q_id'];
    mysqli_query($conn, "UPDATE alumni_qna SET answer_text='$ans_text' WHERE id='$q_id'");
    header("Location: view_journey.php?id=$id&msg=ans_sent");
    exit();
}

$qna_query = mysqli_query($conn, "SELECT alumni_qna.*, users.full_name, users.profile_pic FROM alumni_qna JOIN users ON alumni_qna.student_id = users.id WHERE story_id='$id' ORDER BY created_at DESC");

$is_inspired = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM alumni_inspired WHERE story_id='$id' AND user_id='$current_user_id'")) > 0;
$total_inspired = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM alumni_inspired WHERE story_id='$id'"))['total'];

$profile_img = ($story['profile_pic'] != 'default.png') ? "../" . $story['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($story['full_name'])."&background=random";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $story['full_name']; ?>'s Journey - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 80px; }
        .main-card { border-radius: 25px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.05); overflow: hidden; background: #fff; }
        .header-bg { background: linear-gradient(135deg, #0d6efd 0%, #6f42c1 100%); height: 180px; }
        .profile-avatar { width: 130px; height: 130px; object-fit: cover; border: 6px solid #fff; margin-top: -65px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .section-title { font-weight: 800; border-left: 5px solid #0d6efd; padding-left: 15px; margin-bottom: 20px; color: #333; }
        .roadmap-box { background: #f0f7ff; border-radius: 20px; padding: 25px; border: 1px dashed #0d6efd; }
        .salary-tag { background: #fff3cd; color: #856404; padding: 5px 15px; border-radius: 50px; font-weight: bold; font-size: 14px; }
        .qna-box { background: white; border-radius: 15px; padding: 20px; margin-bottom: 15px; border-left: 5px solid #ddd; }
        .qna-box.answered { border-left-color: #198754; }
        .answer-area { background: #f0fdf4; border-radius: 10px; padding: 15px; margin-top: 10px; border: 1px solid #d1fae5; }
    </style>
</head>
<body>

    <nav class="navbar navbar-dark bg-primary fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-arrow-left me-2"></i> Back to Hub</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-9">
                
                <!-- Main Content Card -->
                <div class="card main-card">
                    <div class="header-bg"></div>
                    <div class="card-body p-4 p-lg-5">
                        
                        <!-- Profile Header -->
                        <div class="text-center mb-5">
                            <img src="<?php echo $profile_img; ?>" class="rounded-circle profile-avatar mb-3">
                            <h2 class="fw-bold mb-1"><?php echo $story['full_name']; ?></h2>
                            <p class="text-primary fw-bold mb-1"><?php echo $story['current_job_title']; ?> @ <?php echo $story['company_name']; ?></p>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <span class="badge bg-light text-dark border"><?php echo $story['dept']; ?> Graduate</span>
                                <?php if($story['first_salary']): ?>
                                    <span class="salary-tag"><i class="bi bi-cash-stack"></i> First Salary: <?php echo $story['first_salary']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- 1. The Success Story -->
                        <div class="mb-5">
                            <h4 class="section-title">The Success Story</h4>
                            <p class="text-secondary" style="font-size: 17px; line-height: 1.9; white-space: pre-line;">
                                <?php echo $story['journey_story']; ?>
                            </p>
                        </div>

                        <!-- 2. Career Roadmap -->
                        <div class="mb-5">
                            <h4 class="section-title">Career Roadmap & Guidance</h4>
                            <div class="roadmap-box">
                                <p class="mb-0 text-dark" style="font-size: 16px; line-height: 1.8; white-space: pre-line;">
                                    <?php echo $story['career_roadmap']; ?>
                                </p>
                            </div>
                        </div>

                        <!-- 3. Mistakes & Advice Grid -->
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="p-4 bg-danger bg-opacity-10 rounded-4 border-start border-danger border-5 h-100">
                                    <h5 class="fw-bold text-danger small mb-3"><i class="bi bi-x-circle-fill"></i> MISTAKES TO AVOID</h5>
                                    <p class="text-dark small mb-0"><?php echo nl2br($story['biggest_mistake']); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-4 bg-success bg-opacity-10 rounded-4 border-start border-success border-5 h-100">
                                    <h5 class="fw-bold text-success small mb-3"><i class="bi bi-lightbulb-fill"></i> PRO ADVICE</h5>
                                    <p class="text-dark small mb-0"><?php echo nl2br($story['advice_to_juniors']); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Tech Stack -->
                        <div class="p-4 bg-light rounded-4 text-center border mb-5">
                            <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 1px; font-size: 12px;">Recommended Tech Stack / Skills</h6>
                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                <?php 
                                    $skills = explode(',', $story['skills_used']);
                                    foreach($skills as $skill):
                                ?>
                                    <span class="badge bg-white text-primary border border-primary px-3 py-2"><?php echo trim($skill); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Inspired Button -->
                        <div class="text-center">
                            <a href="toggle_inspire.php?id=<?php echo $id; ?>" class="btn <?php echo $is_inspired ? 'btn-danger' : 'btn-outline-danger'; ?> rounded-pill px-5 fw-bold shadow-sm">
                                <i class="bi <?php echo $is_inspired ? 'bi-heart-fill' : 'bi-heart'; ?> me-2"></i> Inspired (<?php echo $total_inspired; ?>)
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 5. Q&A / Mentorship Section -->
                <div class="mt-5">
                    <h4 class="fw-bold mb-4"><i class="bi bi-chat-dots-fill text-primary"></i> Mentorship: Ask a Question</h4>
                    
                    <?php if($current_user_id != $story['alumni_id']): ?>
                        <div class="card p-3 shadow-sm border-0 rounded-4 mb-4">
                            <form method="POST">
                                <label class="small fw-bold mb-2 text-muted">Got a question for <?php echo explode(' ', $story['full_name'])[0]; ?>?</label>
                                <div class="input-group shadow-sm">
                                    <textarea name="question" class="form-control border-0 bg-light" rows="2" placeholder="Ask about career, skills or company..." required></textarea>
                                    <button name="ask_question" class="btn btn-primary px-4"><i class="bi bi-send"></i></button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <div class="qna-list">
                        <?php if(mysqli_num_rows($qna_query) > 0): ?>
                            <?php while($q = mysqli_fetch_assoc($qna_query)): ?>
                                <div class="qna-box shadow-sm <?php echo $q['answer_text'] ? 'answered' : ''; ?>">
                                    <div class="d-flex align-items-center mb-2">
                                        <small class="fw-bold text-dark me-2"><?php echo $q['full_name']; ?> asked:</small>
                                        <small class="text-muted" style="font-size: 11px;"><?php echo date('M d, Y', strtotime($q['created_at'])); ?></small>
                                    </div>
                                    <p class="mb-2 fw-semibold" style="font-size: 15px; color: #444;"><?php echo $q['question_text']; ?></p>

                                    <?php if($q['answer_text']): ?>
                                        <div class="answer-area">
                                            <small class="text-success fw-bold d-block mb-1"><i class="bi bi-patch-check-fill"></i> Alumni Answered:</small>
                                            <p class="mb-0 text-dark" style="font-size: 14.5px;"><?php echo $q['answer_text']; ?></p>
                                        </div>
                                    <?php elseif($current_user_id == $story['alumni_id']): ?>
                                        <form method="POST" class="mt-3">
                                            <input type="hidden" name="q_id" value="<?php echo $q['id']; ?>">
                                            <div class="input-group">
                                                <input type="text" name="answer" class="form-control form-control-sm border-success" placeholder="Write your answer..." required>
                                                <button name="submit_answer" class="btn btn-sm btn-success px-3">Reply</button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <small class="text-muted"><i class="bi bi-hourglass-split"></i> Waiting for answer...</small>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-center text-muted py-3">No questions yet. Be the first to ask!</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>