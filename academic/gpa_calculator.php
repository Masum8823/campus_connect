<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(isset($_POST['save_gpa'])){
    $semester = mysqli_real_escape_string($conn, $_POST['semester_name']);
    $gpa_val = $_POST['final_gpa'];
    $credits = $_POST['total_credits'];

    mysqli_query($conn, "DELETE FROM gpa_records WHERE user_id='$user_id' AND semester_name='$semester'");

    $query = "INSERT INTO gpa_records (user_id, semester_name, gpa, total_credits) VALUES ('$user_id', '$semester', '$gpa_val', '$credits')";
    
    if(mysqli_query($conn, $query)){
        $record_id = mysqli_insert_id($conn);
        $course_names = $_POST['course_names'];
        $course_credits = $_POST['course_credits'];
        $course_grades = $_POST['course_grades'];

        for ($i = 0; $i < count($course_names); $i++) {
            if(!empty($course_names[$i])) {
                $name = mysqli_real_escape_string($conn, $course_names[$i]);
                $c = $course_credits[$i];
                $g = $course_grades[$i];
                mysqli_query($conn, "INSERT INTO gpa_details (record_id, course_name, credits, grade) VALUES ('$record_id', '$name', '$c', '$g')");
            }
        }
        header("Location: gpa_calculator.php?success=1");
        exit();
    }
}

if(isset($_GET['delete'])){
    $del_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM gpa_records WHERE id='$del_id' AND user_id='$user_id'");
    header("Location: gpa_calculator.php");
    exit();
}

$saved_records = mysqli_query($conn, "SELECT * FROM gpa_records WHERE user_id='$user_id' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GPA Calculator - CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .calc-card { border-radius: 15px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .record-card { border-left: 5px solid #0d6efd; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-primary shadow mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-calculator"></i> GPA Calculator</a>
            <a href="index.php" class="btn btn-light btn-sm fw-bold">Academic Hub</a>
        </div>
    </nav>

    <div class="container pb-5">
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                GPA Record Saved Successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-7">
                <form method="POST">
                <div class="card calc-card p-4 mb-4">
                    <h4 class="fw-bold text-primary mb-4">Semester Calculator</h4>
                    <table class="table table-borderless">
                        <thead>
                            <tr class="small text-muted">
                                <th>Subject Name</th>
                                <th width="100">Credits</th>
                                <th width="150">Grade</th>
                            </tr>
                        </thead>
                        <tbody id="courseRows">
                            <tr>
                                <td><input type="text" name="course_names[]" class="form-control form-control-sm" placeholder="e.g. Math" required></td>
                                <td><input type="number" name="course_credits[]" class="form-control form-control-sm credit-input" value="3" oninput="calculateGPA()"></td>
                                <td>
                                    <select name="course_grades[]" class="form-select form-select-sm grade-input" onchange="calculateGPA()">
                                        <option value="4.00">A (4.00)</option><option value="3.75">A- (3.75)</option>
                                        <option value="3.50">B+ (3.50)</option><option value="3.25">B (3.25)</option>
                                        <option value="3.00">B- (3.00)</option><option value="2.75">C+ (2.75)</option>
                                        <option value="2.50">C (2.50)</option><option value="0.00">F (0.00)</option>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-4" onclick="addRow()"><i class="bi bi-plus-lg"></i> Add Course</button>

                    <div class="bg-light p-4 rounded text-center border">
                        <h6 class="text-muted mb-1">Resulting GPA</h6>
                        <h1 class="fw-bold text-primary mb-0" id="displayGPA">4.00</h1>
                        <p class="small text-muted mb-3">Total Credits: <span id="displayCredits">3</span></p>
                        <!-- বাটন যেটা মডাল খুলবে -->
                        <button type="button" class="btn btn-success fw-bold px-4" data-bs-toggle="modal" data-bs-target="#saveModal">
                           Save this Result
                        </button>
                    </div>
                </div>

                <div class="modal fade" id="saveModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Save Semester Result</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <label class="fw-bold">Semester Name</label>
                                <input type="text" name="semester_name" class="form-control mb-3" placeholder="e.g. FALL 2023" required>
                                
                                <input type="hidden" name="final_gpa" id="hiddenGPA">
                                <input type="hidden" name="total_credits" id="hiddenCredits">
                                
                                <div class="p-3 bg-light rounded text-center border">
                                    GPA: <span id="modalGPA" class="fw-bold">4.00</span> | Credits: <span id="modalCredits" class="fw-bold">3</span>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button name="save_gpa" class="btn btn-primary w-100 py-2 fw-bold">Confirm & Save</button>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>

            <div class="col-md-5">
                <div class="card calc-card p-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Saved Records</h5>
                    <?php while($row = mysqli_fetch_assoc($saved_records)): ?>
                        <div class="mb-3 p-3 border rounded bg-white shadow-sm record-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold"><?php echo $row['semester_name']; ?></h6>
                                    <small class="text-muted"><?php echo $row['total_credits']; ?> Credits</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary fs-5"><?php echo $row['gpa']; ?></span>
                                </div>
                            </div>
                            <div class="mt-2 d-flex justify-content-between">
                                <button class="btn btn-sm btn-link p-0 text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#details<?php echo $row['id']; ?>">View Courses</button>
                                <a href="?delete=<?php echo $row['id']; ?>" class="text-danger small" onclick="return confirm('Delete record?')"><i class="bi bi-trash"></i></a>
                            </div>
                            
                            <div class="collapse mt-2" id="details<?php echo $row['id']; ?>">
                                <table class="table table-sm table-bordered mb-0" style="font-size: 12px;">
                                    <?php
                                    $rid = $row['id'];
                                    $details = mysqli_query($conn, "SELECT * FROM gpa_details WHERE record_id='$rid'");
                                    while($d = mysqli_fetch_assoc($details)): ?>
                                        <tr>
                                            <td><?php echo $d['course_name']; ?></td>
                                            <td width="40"><?php echo $d['credits']; ?></td>
                                            <td width="40"><?php echo $d['grade']; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </table>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calculateGPA() {
            let credits = document.querySelectorAll('.credit-input');
            let grades = document.querySelectorAll('.grade-input');
            let totalPoints = 0, totalCredits = 0;

            for (let i = 0; i < credits.length; i++) {
                let c = parseFloat(credits[i].value) || 0;
                let g = parseFloat(grades[i].value) || 0;
                totalPoints += (c * g);
                totalCredits += c;
            }

            let res = totalCredits > 0 ? (totalPoints / totalCredits).toFixed(2) : "0.00";
            
            document.getElementById('displayGPA').innerText = res;
            document.getElementById('displayCredits').innerText = totalCredits;
            document.getElementById('hiddenGPA').value = res;
            document.getElementById('hiddenCredits').value = totalCredits;
            document.getElementById('modalGPA').innerText = res;
            document.getElementById('modalCredits').innerText = totalCredits;
        }

        function addRow() {
            let row = `<tr>
                <td><input type="text" name="course_names[]" class="form-control form-control-sm" required></td>
                <td><input type="number" name="course_credits[]" class="form-control form-control-sm credit-input" value="3" oninput="calculateGPA()"></td>
                <td>
                    <select name="course_grades[]" class="form-select form-select-sm grade-input" onchange="calculateGPA()">
                        <option value="4.00">A (4.00)</option><option value="3.75">A- (3.75)</option>
                        <option value="3.50">B+ (3.50)</option><option value="3.25">B (3.25)</option>
                        <option value="3.00">B- (3.00)</option><option value="2.75">C+ (2.75)</option>
                        <option value="2.50">C (2.50)</option><option value="0.00">F (0.00)</option>
                    </select>
                </td>
            </tr>`;
            document.getElementById('courseRows').insertAdjacentHTML('beforeend', row);
            calculateGPA();
        }

        window.onload = calculateGPA;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>