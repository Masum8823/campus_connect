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
            background-color: #f8f9fa;
        }

        .feature-card {
            padding: 30px;
            border-radius: 25px;
            border: 1px solid #eee;
            background: #fff;
            transition: all 0.3s ease;
            height: 100%;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.06);
            border-color: var(--primary-bg);
        }

        .icon-wrapper {
            width: 60px;
            height: 60px;
            background: #e7f1ff;
            color: var(--primary-bg);
            border-radius: 18px;
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

        .section-tag { font-size: 11px; letter-spacing: 2px; font-weight: 800; color: var(--primary-bg); }
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
                <a href="auth/register.php" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">Join Now</a>
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
                        The most powerful digital ecosystem for university life. Bridge the gap between students, teachers, and alumni in one unified platform.
                    </p>
                    <div class="d-flex gap-3 animate__animated animate__fadeInUp animate__delay-1s">
                        <a href="auth/register.php" class="btn btn-premium btn-glow">Get Started</a>
                        <a href="#features" class="btn btn-premium btn-outline-light">Explore Features</a>
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
                <path d="M0 120L1440 120V60C1440 60 1120 0 720 0C320 0 0 60 0 60V120Z" fill="#f8f9fa"/>
            </svg>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="text-center mb-5 animate__animated animate__fadeIn">
                <span class="section-tag text-uppercase">Platform Modules</span>
                <h2 class="fw-bold display-5">A Complete Campus Ecosystem</h2>
                <p class="text-muted">Empowering your university journey with cutting-edge tools.</p>
            </div>

            <div class="row g-4">
                <!-- 1. Real-time Messaging -->
                <div class="col-md-4 animate__animated animate__fadeInUp">
                    <div class="feature-card">
                        <div class="icon-wrapper"><i class="bi bi-chat-left-dots-fill"></i></div>
                        <h4 class="fw-bold">Private Messaging</h4>
                        <p class="text-muted small">Real-time chat with file sharing, message replies, and secure request-based conversation system.</p>
                    </div>
                </div>
                <!-- 2. Academic Hub -->
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="feature-card">
                        <div class="icon-wrapper"><i class="bi bi-book-half"></i></div>
                        <h4 class="fw-bold">Academic Hub</h4>
                        <p class="text-muted small">Routines, course materials, and assignments—manage all your academic needs in one central hub.</p>
                    </div>
                </div>
                <!-- 3. Alumni Hub -->
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="feature-card">
                        <div class="icon-wrapper" style="background:#f3e8ff; color:#6f42c1;"><i class="bi bi-mortarboard-fill"></i></div>
                        <h4 class="fw-bold">Alumni Insights</h4>
                        <p class="text-muted small">Learn from the best. Career roadmaps, success stories, and job opportunities directly from alumni.</p>
                    </div>
                </div>
                <!-- 4. Event Management -->
                <div class="col-md-4 animate__animated animate__fadeInUp">
                    <div class="feature-card">
                        <div class="icon-wrapper" style="background:#fff1f0; color:#d85140;"><i class="bi bi-calendar-event-fill"></i></div>
                        <h4 class="fw-bold">Events & RSVPs</h4>
                        <p class="text-muted small">Never miss a workshop or fest. Real-time notifications and automated email reminders for your events.</p>
                    </div>
                </div>
                <!-- 5. Network & Connections -->
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="feature-card">
                        <div class="icon-wrapper" style="background:#e6fffa; color:#059669;"><i class="bi bi-people-fill"></i></div>
                        <h4 class="fw-bold">Campus Network</h4>
                        <p class="text-muted small">Grow your professional network. Connect with peers, search directory, and manage profile privacy.</p>
                    </div>
                </div>
                <!-- 6. GPA Calculator -->
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="feature-card">
                        <div class="icon-wrapper" style="background:#fff9db; color:#fab005;"><i class="bi bi-calculator-fill"></i></div>
                        <h4 class="fw-bold">Result Tracker</h4>
                        <p class="text-muted small">Interactive GPA calculator with course-wise details and history saving to track your progress.</p>
                    </div>
                </div>
                <!-- 7. Lost & Found -->
                <div class="col-md-4 animate__animated animate__fadeInUp">
                    <div class="feature-card">
                        <div class="icon-wrapper" style="background:#f0f9ff; color:#0369a1;"><i class="bi bi-search"></i></div>
                        <h4 class="fw-bold">Lost & Found</h4>
                        <p class="text-muted small">Community-driven reporting system for lost items with auto-announcement to the main feed.</p>
                    </div>
                </div>
                <!-- 8. Feedback & Sugestions -->
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="feature-card">
                        <div class="icon-wrapper" style="background:#fff5f5; color:#e03131;"><i class="bi bi-lightbulb-fill"></i></div>
                        <h4 class="fw-bold">Suggestion Box</h4>
                        <p class="text-muted small">Submit ideas or report issues anonymously to help admins improve the campus experience.</p>
                    </div>
                </div>
                <!-- 9. Security First -->
                <div class="col-md-4 animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="feature-card">
                        <div class="icon-wrapper" style="background:#f8f9fa; color:#333;"><i class="bi bi-shield-lock-fill"></i></div>
                        <h4 class="fw-bold">Robust Security</h4>
                        <p class="text-muted small">Gmail SMTP integrated OTP verification and role-based access control for total data safety.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <div class="py-5 bg-white border-top">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-md-3">
                    <h3 class="fw-bold mb-0">9+</h3>
                    <p class="text-muted small">Functional Modules</p>
                </div>
                <div class="col-md-3">
                    <h3 class="fw-bold mb-0">Secure</h3>
                    <p class="text-muted small">OTP Verification</p>
                </div>
                <div class="col-md-3">
                    <h3 class="fw-bold mb-0">Real-time</h3>
                    <p class="text-muted small">Instant Interaction</p>
                </div>
                <div class="col-md-3">
                    <h3 class="fw-bold mb-0">Unified</h3>
                    <p class="text-muted small">All-in-one Platform</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-5 bg-dark text-white">
        <div class="container text-center">
            <h5 class="fw-bold mb-3">CampusConnect</h5>
            <p class="small opacity-50 mb-0">&copy; 2026 CampusConnect Hub. All rights reserved.</p>
            <div class="mt-3">
                <a href="auth/login.php" class="text-white-50 small me-3 text-decoration-none">Login</a>
                <a href="auth/register.php" class="text-white-50 small text-decoration-none">Create Account</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>