<?php
include '../config.php';
// config.php-তে অলরেডি সেশন স্টার্ট করা আছে

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php"); exit();
}

if(isset($_POST['send_suggestion'])){
    $user_id = $_SESSION['user_id'];
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $text = mysqli_real_escape_string($conn, $_POST['suggestion_text']);
    $anonymous = isset($_POST['is_anonymous']) ? 1 : 0;

    $query = "INSERT INTO suggestions (user_id, subject, suggestion_text, is_anonymous) 
              VALUES ('$user_id', '$subject', '$text', '$anonymous')";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Thank you! Your feedback has been sent to the Admin.'); window.location='dashboard.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Suggestion Box | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; padding-top: 50px; }
        .form-card { border-radius: 25px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        .premium-input { border-radius: 12px; background: #f8f9fa; border: 1px solid #eee; padding: 12px; }
        .premium-input:focus { background: white; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); border-color: #0d6efd; }
    </style>
</head>
<body>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="card form-card p-4 p-md-5 bg-white mt-5">
                    <div class="text-center mb-5">
                        <div class="mx-auto mb-3" style="width:70px; height:70px; background:#e7f1ff; color:#0d6efd; border-radius:20px; display:flex; align-items:center; justify-content:center; font-size: 32px;">
                            <i class="bi bi-chat-heart-fill"></i>
                        </div>
                        <h2 class="fw-bold text-dark">Suggestion Box</h2>
                        <p class="text-muted small">Share your ideas or report issues anonymously to help us improve CampusConnect.</p>
                    </div>

                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Subject</label>
                            <input type="text" name="subject" class="form-control premium-input" placeholder="What is this about?" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase">Your Message</label>
                            <textarea name="suggestion_text" class="form-control premium-input" rows="5" placeholder="Describe your suggestion or feedback in detail..." required></textarea>
                        </div>

                        <div class="form-check form-switch mb-5 p-3 rounded-4 border bg-light">
                            <input class="form-check-input ms-0 me-3" type="checkbox" name="is_anonymous" id="anonCheck" style="width: 45px; height: 22px;">
                            <label class="form-check-label fw-bold text-dark" for="anonCheck" style="padding-top: 2px;">Submit Anonymously</label>
                            <p class="text-muted small mb-0 mt-1">If checked, Admin won't see your name or ID.</p>
                        </div>

                        <div class="d-grid gap-2">
                            <button name="send_suggestion" class="btn btn-primary btn-lg fw-bold rounded-pill shadow py-3">
                                <i class="bi bi-send-fill me-2"></i> SUBMIT TO ADMIN
                            </button>
                            <a href="dashboard.php" class="btn btn-link text-muted text-decoration-none small mt-2">← Back to Dashboard</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>