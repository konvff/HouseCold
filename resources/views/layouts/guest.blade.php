<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HouseCall Pro') - Professional Home Services</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #3b82f6 0%, #10b981 50%, #06b6d4 100%);
            --secondary-gradient: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
            --accent-gradient: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);

            --primary-color: #3b82f6;
            --secondary-color: #10b981;
            --success-color: #059669;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;

            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-primary: #2d3748;
            --text-secondary: #4a5568;
            --shadow-light: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 10px 25px rgba(0, 0, 0, 0.15);
            --shadow-heavy: 0 20px 40px rgba(0, 0, 0, 0.2);
            --border-radius: 12px;
            --border-radius-lg: 20px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            line-height: 1.6;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        .guest-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Header Styles */
        .guest-header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 0.75rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: var(--transition);
        }

        .guest-header.scrolled {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: var(--shadow-light);
        }

        .navbar-brand {
            color: rgb(70, 124, 223) !important;
            font-weight: 800;
            font-size: 1.75rem;
            text-decoration: none;
            transition: var(--transition);
        }

        .navbar-brand:hover {
            transform: scale(1.05);
            color: rgb(108, 117, 233) !important;
        }

        .navbar-toggler {
            border: none;
            padding: 0.25rem 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: var(--border-radius);
            transition: var(--transition);
            position: relative;
        }

        .nav-link:hover {
            color: white !important;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white !important;
        }

        .dropdown-menu {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-medium);
            margin-top: 0.5rem;
        }

        .dropdown-item {
            color: var(--text-primary);
            padding: 0.75rem 1.5rem;
            transition: var(--transition);
        }

        .dropdown-item:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        /* Content Styles */
        .guest-content {
            flex: 1;
            padding: 2rem 0;
            position: relative;
        }

        .card {
            border: none;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-medium);
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.95);
            transition: var(--transition);
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-heavy);
        }

        /* Button Styles */
        .btn {
            border-radius: var(--border-radius);
            font-weight: 600;
            padding: 0.75rem 2rem;
            transition: var(--transition);
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--accent-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
            color: white;
        }

        .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.8);
            color: white;
            background: transparent;
        }

        .btn-outline-light:hover {
            background: white;
            color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
        }

        /* Form Styles */
        .form-control, .form-select {
            border-radius: var(--border-radius);
            border: 2px solid #e2e8f0;
            padding: 0.875rem 1.25rem;
            transition: var(--transition);
            background: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        /* Alert Styles */
        .alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 1rem 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(45deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.2));
            color: #064e3b;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .alert-danger {
            background: linear-gradient(45deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.2));
            color: #7f1d1d;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .alert-info {
            background: linear-gradient(45deg, rgba(6, 182, 212, 0.1), rgba(6, 182, 212, 0.2));
            color: #164e63;
            border: 1px solid rgba(6, 182, 212, 0.3);
        }

        /* Utility Classes */
        .text-primary {
            color: var(--primary-color) !important;
        }

        .bg-primary {
            background: var(--accent-gradient) !important;
        }

        .glass-effect {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
        }

        /* Footer Styles */
        .guest-footer {
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(20px);
            border-top: 1px solid var(--glass-border);
            padding: 3rem 0 2rem;
            color: rgba(255, 255, 255, 0.9);
            margin-top: auto;
        }

        .guest-footer h6 {
            color: white;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .guest-footer p {
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .guest-footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
        }

        .guest-footer a:hover {
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .container {
                max-width: 100%;
                padding: 0 1.5rem;
            }
        }

        @media (max-width: 992px) {
            .navbar-brand {
                font-size: 1.5rem;
            }

            .guest-content {
                padding: 1.5rem 0;
            }

            .card {
                margin: 0 0.5rem;
            }
        }

        @media (max-width: 768px) {
            .guest-header {
                padding: 0.5rem 0;
            }

            .navbar-brand {
                font-size: 1.25rem;
            }

            .guest-content {
                padding: 1rem 0;
            }

            .card {
                margin: 0 0.25rem;
                border-radius: var(--border-radius);
            }

            .btn {
                padding: 0.625rem 1.5rem;
                font-size: 0.9rem;
            }

            .form-control, .form-select {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }

            .guest-footer {
                padding: 2rem 0 1.5rem;
                text-align: center;
            }

            .guest-footer .col-md-6 {
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 0 1rem;
            }

            .navbar-brand {
                font-size: 1.1rem;
            }

            .nav-link {
                padding: 0.5rem 0.75rem !important;
                font-size: 0.9rem;
            }

            .card {
                margin: 0;
                border-radius: var(--border-radius);
            }

            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .btn:last-child {
                margin-bottom: 0;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .slide-up {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Loading States */
        .loading {
            position: relative;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Accessibility */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Focus States */
        .btn:focus,
        .form-control:focus,
        .form-select:focus,
        .nav-link:focus {
            outline: 2px solid #667eea;
            outline-offset: 2px;
        }

        /* Print Styles */
        @media print {
            .guest-header,
            .guest-footer {
                display: none;
            }

            .guest-content {
                padding: 0;
            }

            .card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="guest-container">
        <!-- Header -->
        <header class="guest-header">
            <div class="container">
                <nav class="navbar navbar-expand-lg">
                    <a class="navbar-brand" href="{{ route('home') }}">
                        <i class="fas fa-home me-2"></i>HouseCall Pro
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">
                                    <i class="fas fa-home me-1"></i>Home
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('appointments.create') }}">
                                    <i class="fas fa-calendar-plus me-1"></i>Book Service
                                </a>
                            </li>
                            @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus me-1"></i>Register
                                </a>
                            </li>
                            @endauth
                        </ul>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="guest-content">
            <div class="container">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="guest-footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3">
                            <i class="fas fa-home me-2"></i>HouseCall Pro
                        </h6>
                        <p class="mb-0">Professional home services at your doorstep. Quality, reliability, and convenience you can trust.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="mb-3">Contact Us</h6>
                        <p class="mb-1">
                            <i class="fas fa-phone me-2"></i>(555) 123-4567
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-envelope me-2"></i>support@housecallpro.com
                        </p>
                    </div>
                </div>
                <hr class="my-4" style="border-color: rgba(255, 255, 255, 0.2);">
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="mb-0">
                            &copy; {{ date('Y') }} HouseCall Pro. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Enhanced JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Header scroll effect
            const header = document.querySelector('.guest-header');
            let lastScrollTop = 0;

            window.addEventListener('scroll', function() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                if (scrollTop > 100) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }

                lastScrollTop = scrollTop;
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Form validation enhancement
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.classList.add('loading');
                        submitBtn.disabled = true;
                    }
                });
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 5000);
            });

            // Add animation classes to cards
            const cards = document.querySelectorAll('.card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            }, { threshold: 0.1 });

            cards.forEach(card => {
                observer.observe(card);
            });

            // Mobile menu enhancement
            const navbarToggler = document.querySelector('.navbar-toggler');
            const navbarCollapse = document.querySelector('.navbar-collapse');

            if (navbarToggler && navbarCollapse) {
                navbarToggler.addEventListener('click', function() {
                    navbarCollapse.classList.toggle('show');
                });

                // Close mobile menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!navbarToggler.contains(e.target) && !navbarCollapse.contains(e.target)) {
                        navbarCollapse.classList.remove('show');
                    }
                });
            }

            // Add loading states to buttons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    if (this.type === 'submit' || this.classList.contains('btn-primary')) {
                        this.classList.add('loading');
                        setTimeout(() => {
                            this.classList.remove('loading');
                        }, 2000);
                    }
                });
            });

            // Enhanced form focus states
            const formControls = document.querySelectorAll('.form-control, .form-select');
            formControls.forEach(control => {
                control.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });

                control.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });

            // Keyboard navigation enhancement
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    // Close any open dropdowns or modals
                    const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
                    openDropdowns.forEach(dropdown => {
                        dropdown.classList.remove('show');
                    });
                }
            });

            // Performance optimization: Lazy load images
            const images = document.querySelectorAll('img[data-src]');
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));

            // Add ripple effect to buttons
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.classList.add('ripple');

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        });

        // Add ripple effect CSS
        const style = document.createElement('style');
        style.textContent = `
            .btn {
                position: relative;
                overflow: hidden;
            }

            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                pointer-events: none;
            }

            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }

            .form-group.focused .form-label {
                color: #667eea;
                transform: translateY(-2px);
            }
        `;
        document.head.appendChild(style);
    </script>

    @stack('scripts')
</body>
</html>
