<?php
include '../config.php';
session_start();

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])){
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "SELECT * FROM lost_found WHERE id='$id' AND user_id='$user_id'");
$item = mysqli_fetch_assoc($query);

if(!$item){ 
    header("Location: index.php"); 
    exit(); 
}

if(isset($_POST['update_item'])){
    $name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $resolved = isset($_POST['is_resolved']) ? 1 : 0;

    $update_query = "UPDATE lost_found SET item_name='$name', description='$desc', is_resolved='$resolved' WHERE id='$id'";
    
    if(mysqli_query($conn, $update_query)){
        echo "<script>alert('Status updated successfully!'); window.location='index.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Item | CampusConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0d6efd;
            --bg-light: #f0f2f5;
        }

        body { 
            background-color: var(--bg-light); 
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex;
            align-items: center;
            min-height: 100vh;
            padding: 20px 0;
        }

        .edit-card {
            border-radius: 25px;
            border: none;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            background: white;
            overflow: hidden;
            width: 100%;
            max-width: 550px;
            margin: auto;
        }

        .card-header-premium {
            background: linear-gradient(135deg, #0d6efd 0%, #4b0082 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .form-label { font-weight: 700; color: #444; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .premium-input {
            border-radius: 12px;
            background: #f8f9fa;
            border: 1px solid #eee;
            padding: 12px 15px;
            font-size: 15px;
            transition: 0.3s;
        }

        .premium-input:focus {
            background: white;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
            border-color: var(--primary-color);
        }

        /* Modern Toggle Switch */
        .status-box {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            border: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-check-input { width: 3em; height: 1.5em; cursor: pointer; }
        .form-check-input:checked { background-color: #198754; border-color: #198754; }

        .btn-update {
            background: linear-gradient(135deg, #0d6efd 0%, #004dc7 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-weight: 700;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-update:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
        }

        .btn-cancel {
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            color: #666;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: 0.2s;
        }

        .btn-cancel:hover { color: #333; background: #eee; }
    </style>
</head>
<body>

    <div class="container">
        <div class="card edit-card">
            <div class="card-header-premium">
                <i class="bi bi-pencil-square display-4 mb-2 d-block"></i>
                <h3 class="fw-bold mb-0">Update Item Status</h3>
                <p class="small opacity-75 mb-0">Refine your report or mark it as resolved</p>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form method="POST">
                    <!-- Item Name -->
                    <div class="mb-4">
                        <label class="form-label">Item Name</label>
                        <input type="text" name="item_name" class="form-control premium-input" value="<?php echo $item['item_name']; ?>" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control premium-input" rows="5" required><?php echo $item['description']; ?></textarea>
                    </div>

                    <!-- Toggle Status -->
                    <div class="status-box mb-5 shadow-sm">
                        <div>
                            <h6 class="mb-1 fw-bold text-dark">Case Status</h6>
                            <p class="small text-muted mb-0">Has this item been found/returned?</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_resolved" id="resSwitch" <?php echo $item['is_resolved'] == 1 ? 'checked' : ''; ?>>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-grid gap-2">
                        <button name="update_item" class="btn btn-primary btn-update">
                            <i class="bi bi-cloud-check-fill me-2"></i> SAVE UPDATES
                        </button>
                        <a href="index.php" class="btn-cancel">
                            CANCEL
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <p class="text-center text-muted mt-4 small">
            CampusConnect Safety & Security Protocol &copy; 2026
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>