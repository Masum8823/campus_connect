<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusConnect | Your University Network</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-bg: #0d6efd;
            --secondary-bg: #4b0082;
            --text-dark: #2d3436;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
            background-color: #fff;
            color: var(--text-dark);
        }

        /* Hero Wrapper */
        .hero-wrapper {
            position: relative;
            background: linear-gradient(135deg, var(--primary-bg) 0%, var(--secondary-bg) 100%);
            min-height: 100vh;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.05) !important;
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px 0;
        }

        .hero-title {
            font-weight: 800;
            font-size: 4.2rem;
            line-height: 1.1;
            background: linear-gradient(to right, #ffffff, #a5c9ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            background-color: #ffffff;
        }

        .feature-card {
            padding: 30px;
            border-radius: 20px;
            border: 1px solid #f0f0f0;
            background: #fff;
            transition: all 0.3s ease;
            height: 100%;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            border-color: var(--primary-bg);
        }

        .icon-wrapper {
            width: 60px;
            height: 60px;
            background: #e7f1ff;
            color: var(--primary-bg);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 20px;
        }

        .floating-icon {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .wave-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            line-height: 0;
        }

        .btn-premium {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 700;
            transition: 0.3s;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .btn-glow { background: white; color: var(--primary-bg); border: none; }
        .btn-glow:hover { transform: scale(1.05); box-shadow: 0 0 20px rgba(255,255,255,0.4); }

    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-connectdevelop me-2"></i>CampusConnect
            </a>
            <div class="ms-auto">
                <a href="auth/login.php" class="btn btn-link text-white text-decoration-none fw-bold me-3">Login</a>
                <a href="auth/register.php" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">Sign Up</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-wrapper">
        <div class="container mt-5">
            <div class="row align-items-center">
                <div class="col-lg-7 text-start">
                    <h1 class="hero-title animate__animated animate__fadeInDown">Connect. <br>Collaborate. <br>Succeed.</h1>
                    <p class="lead text-white opacity-75 mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                        The all-in-one digital platform for university students and teachers to bridge communication and academic management.
                    </p>
                    <div class="d-flex gap-3 animate__animated animate__fadeInUp animate__delay-1s">
                        <a href="auth/register.php" class="btn btn-premium btn-glow">Join Community</a>
                        <a href="#features" class="btn btn-premium btn-outline-light">Learn More</a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <div class="floating-icon animate__animated animate__zoomIn animate__delay-1s">
                        <i class="bi bi-rocket-takeoff-fill" style="font-size: 250px; color: rgba(255,255,255,0.15);"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="wave-bottom">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120L1440 120V60C1440 60 1120 0 720 0C320 0 0 60 0 60V120Z" fill="white"/>
            </svg>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="text-center mb-5 animate__animated animate__fadeIn">
                <h6 class="text-primary fw-bold text-uppercase letter-spacing-1">Features</h6>
                <h2 class="fw-bold display-5">What makes us special?</h2>
            </div>

            <div class="row g-4">
                <!-- Feed -->
                <div class="col-md-4 animate__animated animate__fadeInUp">
                    <div class="feature-card shadow-sm">
                        <div class="icon-wrapper"><i class="bi bi-people-fill"></i></div>
                        <h4 class="fw-bold">Campus Feed</h4>
                        <p class="text-muted small">Stay connected with your peers. Share thoughts, like, and comment on real-time campus activities.</p>
                    </div>
                </div>
                <!-- Academic Hub -->
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="feature-card shadow-sm">
                        <div class="icon-wrapper"><i class="bi bi-book-half"></i></div>
                        <h4 class="fw-bold">Academic Hub</h4>
                        <p class="text-muted small">Access class routines, exam schedules, and course materials all in one dedicated section.</p>
                    </div>
                </div>
                <!-- Lost & Found -->
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="feature-card shadow-sm">
                        <div class="icon-wrapper"><i class="bi bi-search"></i></div>
                        <h4 class="fw-bold">Lost & Found</h4>
                        <p class="text-muted small">Helping students recover lost items through a community-driven reporting system.</p>
                    </div>
                </div>
                <!-- Assignments -->
                <div class="col-md-4 animate__animated animate__fadeInUp">
                    <div class="feature-card shadow-sm">
                        <div class="icon-wrapper"><i class="bi bi-file-earmark-check-fill"></i></div>
                        <h4 class="fw-bold">Assignment Portal</h4>
                        <p class="text-muted small">Teachers can post tasks and students can submit their work securely with deadline tracking.</p>
                    </div>
                </div>
                <!-- GPA Calc -->
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="feature-card shadow-sm">
                        <div class="icon-wrapper"><i class="bi bi-calculator-fill"></i></div>
                        <h4 class="fw-bold">GPA Calculator</h4>
                        <p class="text-muted small">Calculate and save your semester results with our advanced, subject-wise GPA tool.</p>
                    </div>
                </div>
                <!-- Security -->
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="feature-card shadow-sm">
                        <div class="icon-wrapper"><i class="bi bi-shield-lock-fill"></i></div>
                        <h4 class="fw-bold">Secure Access</h4>
                        <p class="text-muted small">Integrated with Real-time Email OTP verification and role-based access for maximum security.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5 border-top">
        <div class="container text-center">
            <h5 class="fw-bold text-primary mb-2">CampusConnect</h5>
            <p class="text-muted small">&copy; 2026 CampusConnect Platform. Designed for Students & Teachers.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>