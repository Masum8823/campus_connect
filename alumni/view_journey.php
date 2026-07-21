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

$profile_img = ($story['profile_pic'] != 'default.png') ? "../" . $story['profile_pic'] : "https://ui-avatars.com/api/?name=".urlencode($story['full_name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $story['full_name']; ?>'s Journey</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 80px; font-family: 'Plus Jakarta Sans', sans-serif; }
        .journey-card { border-radius: 25px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
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
            <div class="col-md-9">
                <!-- Main Journey Content -->
                <div class="card journey-card mb-5">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <img src="<?php echo $profile_img; ?>" class="rounded-circle border border-4 border-light shadow-sm" width="120" height="130" style="object-fit: cover;">
                            <h2 class="fw-bold mt-3 mb-1"><?php echo $story['full_name']; ?></h2>
                            <p class="text-primary fw-bold"><?php echo $story['current_job_title']; ?> @ <?php echo $story['company_name']; ?></p>
                        </div>
                        
                        <h5 class="fw-bold border-bottom pb-2">The Journey</h5>
                        <p class="text-secondary" style="white-space: pre-line; line-height: 1.8;"><?php echo $story['journey_story']; ?></p>
                        
                        <div class="bg-light p-4 rounded-4 mt-4 border">
                            <h6 class="fw-bold text-primary"><i class="bi bi-map-fill"></i> Career Roadmap</h6>
                            <p class="mb-0 small text-dark"><?php echo nl2br($story['career_roadmap']); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Q&A Section -->
                <div class="mt-5">
                    <h4 class="fw-bold mb-4"><i class="bi bi-chat-dots-fill text-primary"></i> Mentorship: Ask a Question</h4>
                    
                    <!-- Ask Form -->
                    <?php if($current_user_id != $story['alumni_id']): ?>
                        <div class="card p-3 shadow-sm border-0 rounded-4 mb-4">
                            <form method="POST">
                                <label class="small fw-bold mb-2 text-muted">Got a question for <?php echo explode(' ', $story['full_name'])[0]; ?>?</label>
                                <div class="input-group">
                                    <textarea name="question" class="form-control border-0 bg-light" rows="2" placeholder="Ask about career, skills or company..." required></textarea>
                                    <button name="ask_question" class="btn btn-primary"><i class="bi bi-send"></i></button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <!-- Display Q&A List -->
                    <div class="qna-list">
                        <?php while($q = mysqli_fetch_assoc($qna_query)): ?>
                            <div class="qna-box shadow-sm <?php echo $q['answer_text'] ? 'answered' : ''; ?>">
                                <div class="d-flex align-items-center mb-2">
                                    <small class="fw-bold text-dark me-2"><?php echo $q['full_name']; ?> asked:</small>
                                    <small class="text-muted" style="font-size: 11px;"><?php echo date('M d, Y', strtotime($q['created_at'])); ?></small>
                                </div>
                                <p class="mb-2 fw-semibold" style="font-size: 15px;"><?php echo $q['question_text']; ?></p>

                                <?php if($q['answer_text']): ?>
                                    <div class="answer-area">
                                        <small class="text-success fw-bold d-block mb-1"><i class="bi bi-check-circle-fill"></i> Alumni Answered:</small>
                                        <p class="mb-0 text-dark" style="font-size: 14px;"><?php echo $q['answer_text']; ?></p>
                                    </div>
                                <?php elseif($current_user_id == $story['alumni_id']): ?>
                                    <!-- Answer Form (Only shown to the story owner) -->
                                    <form method="POST" class="mt-3">
                                        <input type="hidden" name="q_id" value="<?php echo $q['id']; ?>">
                                        <div class="input-group">
                                            <input type="text" name="answer" class="form-control form-control-sm border-success" placeholder="Write your answer..." required>
                                            <button name="submit_answer" class="btn btn-sm btn-success">Reply</button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <small class="text-muted italic">Waiting for answer...</small>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>