<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Review Paradise - Your Ultimate Movie Guide</title>
    <link rel="icon" href="images/Logo.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --dark-bg: #0a0a23;
            --card-bg: rgba(255, 255, 255, 0.1);
            --text-light: #e6e6e6;
            --accent-color: #ffd700;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Hero Section */
        .hero-section {
            background: var(--dark-bg);
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--primary-gradient);
            opacity: 0.1;
            z-index: 1;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            bottom: 30%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            animation: slideInLeft 1s ease-out;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: var(--text-light);
            margin-bottom: 2rem;
            animation: slideInLeft 1s ease-out 0.3s both;
        }

        .hero-buttons {
            animation: slideInLeft 1s ease-out 0.6s both;
        }

        .btn-custom {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .btn-primary-custom {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-outline-custom {
            background: transparent;
            color: white;
            border: 2px solid;
            border-image: var(--primary-gradient) 1;
        }

        .btn-outline-custom:hover {
            background: var(--primary-gradient);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .hero-image {
            animation: slideInRight 1s ease-out 0.4s both;
            position: relative;
        }

        .hero-image img {
            filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.3));
            transition: transform 0.3s ease;
        }

        .hero-image:hover img {
            transform: scale(1.05);
        }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            position: relative;
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
            color: white;
        }

        .section-title h2 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .section-title p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .feature-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--accent-color);
            margin-bottom: 1.5rem;
        }

        .feature-card h4 {
            color: white;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .feature-card p {
            color: var(--text-light);
            opacity: 0.9;
        }

        /* Stats Section */
        .stats-section {
            padding: 80px 0;
            background: var(--dark-bg);
        }

        .stat-item {
            text-align: center;
            color: white;
            margin-bottom: 2rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--accent-color);
            display: block;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Newsletter Section */
        .newsletter-section {
            padding: 80px 0;
            background: var(--secondary-gradient);
        }

        .newsletter-form {
            max-width: 500px;
            margin: 0 auto;
        }

        .form-control-custom {
            border-radius: 50px;
            padding: 15px 25px;
            border: none;
            font-size: 1.1rem;
        }

        /* Animations */
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .btn-custom {
                padding: 10px 20px;
                font-size: 0.9rem;
            }
        }

        /* Scroll animations */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="floating-shapes">
            <i class="fas fa-film shape" style="font-size: 3rem;"></i>
            <i class="fas fa-star shape" style="font-size: 2rem;"></i>
            <i class="fas fa-ticket-alt shape" style="font-size: 2.5rem;"></i>
        </div>
        
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title">Movie Review Paradise</h1>
                    <p class="hero-subtitle">
                        <i class="fas fa-quote-left me-2"></i>
                        Your Ultimate Guide to the Reel World: Honest Reviews, Fresh Perspectives, Endless Entertainment
                    </p>
                    <div class="hero-buttons">
                        <a href="usermodule/register.php" class="btn btn-outline-custom btn-custom me-3">
                            <i class="fas fa-user-plus me-2"></i>Get Started
                        </a>
                        <a href="usermodule/login.php" class="btn btn-primary-custom btn-custom">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 hero-image">
                    <img src="images/movie show.png" class="img-fluid" alt="Movie Theater Experience" style="max-height: 500px;"/> 
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Why Choose Movie Paradise?</h2>
                <p>Discover what makes us the ultimate destination for movie enthusiasts</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 fade-in">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h4>Expert Reviews</h4>
                        <p>Get in-depth, unbiased reviews from certified movie critics and passionate film enthusiasts.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 fade-in">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Community Driven</h4>
                        <p>Join thousands of movie lovers sharing their thoughts, ratings, and recommendations.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 fade-in">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4>Smart Discovery</h4>
                        <p>Find your next favorite movie with our intelligent recommendation system and advanced filters.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 fade-in">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4>Mobile Friendly</h4>
                        <p>Access reviews and rate movies on the go with our fully responsive design.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 fade-in">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h4>Real-time Updates</h4>
                        <p>Stay updated with the latest movie releases, trailers, and industry news.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 fade-in">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h4>Personalized</h4>
                        <p>Create your watchlist, track your viewing history, and get personalized recommendations.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-3 col-md-6 fade-in">
                    <div class="stat-item">
                        <span class="stat-number" data-count="50000">0</span>
                        <span class="stat-label">Movies Reviewed</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 fade-in">
                    <div class="stat-item">
                        <span class="stat-number" data-count="25000">0</span>
                        <span class="stat-label">Active Users</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 fade-in">
                    <div class="stat-item">
                        <span class="stat-number" data-count="100000">0</span>
                        <span class="stat-label">User Reviews</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 fade-in">
                    <div class="stat-item">
                        <span class="stat-number" data-count="98">0</span>
                        <span class="stat-label">Satisfaction Rate</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="container text-center">
            <div class="fade-in">
                <h3 class="text-white mb-4">Stay Updated with Latest Reviews</h3>
                <p class="text-white mb-4 opacity-75">Get weekly movie recommendations and exclusive reviews delivered to your inbox</p>
                <div class="newsletter-form">
                    <div class="input-group">
                        <input type="email" class="form-control form-control-custom" placeholder="Enter your email address">
                        <button class="btn btn-primary-custom btn-custom" type="button">
                            <i class="fas fa-paper-plane me-2"></i>Subscribe
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        // Scroll animations
        function animateOnScroll() {
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('visible');
                }
            });
        }

        // Counter animation
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    counter.textContent = Math.floor(current).toLocaleString();
                    if (counter.textContent.includes('98')) {
                        counter.textContent += '%';
                    }
                }, 16);
            });
        }

        // Initialize animations
        window.addEventListener('scroll', animateOnScroll);
        window.addEventListener('load', () => {
            animateOnScroll();
            
            // Trigger counter animation when stats section is visible
            const statsSection = document.querySelector('.stats-section');
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) {
                    animateCounters();
                    observer.disconnect();
                }
            });
            observer.observe(statsSection);
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>