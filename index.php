<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to CampusConnect</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .navbar { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #003d99 100%);
            color: white;
            padding: 120px 0 100px;
            clip-path: ellipse(150% 100% at 50% 0%);
        }
        .feature-card {
            border: none;
            border-radius: 20px;
            transition: 0.3s;
            background: #f8f9fa;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            background: #fff;
        }
        .icon-box {
            width: 60px;
            height: 60px;
            background: #e7f1ff;
            color: #0d6efd;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">
                <i class="bi bi-connectdevelop"></i> CampusConnect
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-collapse ms-auto mb-2 mb-lg-0 list-unstyled d-flex justify-content-end align-items-center gap-3">
                    <li><a href="auth/login.php" class="btn btn-outline-primary px-4 fw-bold rounded-pill">Login</a></li>
                    <li><a href="auth/register.php" class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm">Join Now</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold mb-4">Connect. Collaborate. Succeed.</h1>
                    <p class="lead mb-5 opacity-90">CampusConnect is the ultimate digital hub for students and teachers. Stay updated with campus life, manage academic resources, and grow together in a unified community.</p>
                    <div class="d-flex gap-3">
                        <a href="auth/register.php" class="btn btn-light btn-lg px-4 fw-bold text-primary rounded-pill">Get Started</a>
                        <a href="#features" class="btn btn-outline-light btn-sm px-4 fw-bold rounded-pill d-flex align-items-center">Learn More</a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block text-center">
                    <i class="bi bi-people-fill" style="font-size: 250px; opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-5 mt-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Everything you need in one place</h2>
                <p class="text-muted">Designed specifically for university academic and social needs.</p>
            </div>
            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="card feature-card p-4 h-100 text-center">
                        <div class="icon-box mx-auto"><i class="bi bi- megaphone-fill"></i></div>
                        <h4 class="fw-bold">Campus Feed</h4>
                        <p class="text-muted">Share thoughts, ask questions, and stay updated with what's happening around you.</p>
                    </div>
                </div>
                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="card feature-card p-4 h-100 text-center">
                        <div class="icon-box mx-auto"><i class="bi bi-book-half"></i></div>
                        <h4 class="fw-bold">Academic Hub</h4>
                        <p class="text-muted">Access class routines, study materials, and manage assignments without any hassle.</p>
                    </div>
                </div>
                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="card feature-card p-4 h-100 text-center">
                        <div class="icon-box mx-auto"><i class="bi bi-search"></i></div>
                        <h4 class="fw-bold">Lost & Found</h4>
                        <p class="text-muted">Lost something? Found someone's ID? Our community-driven system helps items find their home.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container text-center">
            <h5 class="fw-bold mb-3">CampusConnect</h5>
            <p class="small opacity-50 mb-0">&copy; 2026 CampusConnect Platform. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>