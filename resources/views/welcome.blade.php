<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxe Interiors - Premium Design Studio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }

        /* Header Styles */
        header {
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 1px;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: #667eea;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.95) 100%),
            url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,149.3C960,160,1056,160,1152,138.7C1248,117,1344,75,1392,53.3L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveGrid 20s linear infinite;
        }

        @keyframes moveGrid {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(50px, 50px);
            }
        }

        .hero-content {
            max-width: 800px;
            padding: 2rem;
            position: relative;
            z-index: 1;
            animation: fadeInUp 1s ease;
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

        .hero h1 {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2.5rem;
            opacity: 0.95;
        }

        .cta-button {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        /* Services Section */
        .services {
            padding: 6rem 5%;
            background: #f9fafb;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 4rem;
            font-size: 1.1rem;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
        }

        .service-card {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid rgba(102, 126, 234, 0.1);
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(102, 126, 234, 0.2);
        }

        .service-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }

        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .service-card p {
            color: #666;
            line-height: 1.8;
        }

        /* Portfolio Section */
        .portfolio {
            padding: 6rem 5%;
            background: white;
        }

        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        .portfolio-item {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            height: 400px;
            cursor: pointer;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .portfolio-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .portfolio-item:hover::before {
            opacity: 1;
        }

        .portfolio-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .portfolio-item:hover .portfolio-img {
            transform: scale(1.1);
        }

        .portfolio-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 2rem;
            color: white;
            z-index: 2;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .portfolio-item:hover .portfolio-overlay {
            transform: translateY(0);
        }

        .portfolio-overlay h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        /* About Section */
        .about {
            padding: 6rem 5%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .about-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .about-content h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .about-content p {
            font-size: 1.2rem;
            line-height: 1.8;
            opacity: 0.95;
        }

        /* Contact Section */
        .contact {
            padding: 6rem 5%;
            background: #f9fafb;
        }

        .contact-form {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 150px;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        /* Footer */
        footer {
            background: #1f2937;
            color: white;
            padding: 3rem 5%;
            text-align: center;
        }

        footer p {
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                gap: 1.5rem;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .portfolio-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<!-- Header -->
<header>
    <nav>
        <div class="logo">LUXE INTERIORS</div>
        <ul class="nav-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#portfolio">Portfolio</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </nav>
</header>

<!-- Hero Section -->
<section id="home" class="hero">
    <div class="hero-content">
        <h1>Elevate Your Space</h1>
        <p>We create stunning interiors that blend luxury, functionality, and timeless elegance to transform your vision
            into reality</p>
        <a href="#contact" class="cta-button">Start Your Project</a>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="services">
    <div class="container">
        <h2 class="section-title">Our Services</h2>
        <p class="section-subtitle">Comprehensive design solutions tailored to your unique style</p>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">🏠</div>
                <h3>Residential Design</h3>
                <p>Transform your home into a personalized sanctuary with our expert residential design services. We
                    focus on creating spaces that reflect your lifestyle.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🏢</div>
                <h3>Commercial Spaces</h3>
                <p>Elevate your business environment with sophisticated commercial interiors that enhance productivity
                    and leave lasting impressions on clients.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">✨</div>
                <h3>Luxury Renovations</h3>
                <p>Breathe new life into existing spaces with our comprehensive renovation services, combining modern
                    aesthetics with functional design.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🎨</div>
                <h3>Custom Furniture</h3>
                <p>Discover bespoke furniture pieces designed and crafted specifically for your space, ensuring perfect
                    harmony with your interior vision.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">💡</div>
                <h3>Lighting Design</h3>
                <p>Master the art of ambiance with our innovative lighting solutions that enhance mood, functionality,
                    and architectural features.</p>
            </div>
            <div class="service-card">
                <div class="service-icon">🪴</div>
                <h3>Space Planning</h3>
                <p>Optimize your layout with strategic space planning that maximizes functionality while maintaining
                    aesthetic appeal and flow.</p>
            </div>
        </div>
    </div>
</section>

<!-- Portfolio Section -->
<section id="portfolio" class="portfolio">
    <div class="container">
        <h2 class="section-title">Our Work</h2>
        <p class="section-subtitle">Explore our latest projects and design innovations</p>
        <div class="portfolio-grid">
            <div class="portfolio-item">
                <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=800" alt="Modern Living Room"
                     class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Modern Living Room</h3>
                    <p>Contemporary elegance meets comfort</p>
                </div>
            </div>
            <div class="portfolio-item">
                <img src="https://images.unsplash.com/photo-1556912173-46c336c7fd55?w=800" alt="Luxury Bedroom"
                     class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Luxury Bedroom Suite</h3>
                    <p>Serene sophistication and relaxation</p>
                </div>
            </div>
            <div class="portfolio-item">
                <img src="https://images.unsplash.com/photo-1556911220-bff31c812dba?w=800" alt="Executive Office"
                     class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Executive Office</h3>
                    <p>Professional style and productivity</p>
                </div>
            </div>
            <div class="portfolio-item">
                <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800" alt="Dining Space"
                     class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Elegant Dining Space</h3>
                    <p>Where culinary art meets design</p>
                </div>
            </div>
            <div class="portfolio-item">
                <img src="https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?w=800" alt="Kitchen Design"
                     class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Modern Kitchen</h3>
                    <p>Functionality meets aesthetic beauty</p>
                </div>
            </div>
            <div class="portfolio-item">
                <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800" alt="Bathroom Design"
                     class="portfolio-img">
                <div class="portfolio-overlay">
                    <h3>Spa-Like Bathroom</h3>
                    <p>Your personal wellness retreat</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about">
    <div class="about-content">
        <h2>About Our Studio</h2>
        <p>With over 15 years of experience in luxury interior design, we've transformed hundreds of spaces into
            stunning environments that inspire and delight. Our team of award-winning designers combines creativity with
            technical expertise to deliver exceptional results that exceed expectations. We believe every space tells a
            story, and we're here to help you write yours.</p>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="contact">
    <div class="container">
        <h2 class="section-title">Get In Touch</h2>
        <p class="section-subtitle">Let's bring your vision to life</p>
        <form class="contact-form">
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone">
            </div>
            <div class="form-group">
                <label for="message">Project Details</label>
                <textarea id="message" required></textarea>
            </div>
            <button type="submit" class="submit-btn">Send Message</button>
        </form>
    </div>
</section>

<!-- Footer -->
<footer>
    <p>&copy; 2025 Luxe Interiors. All rights reserved. Crafted with passion and precision.</p>
</footer>

<script>
    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({behavior: 'smooth', block: 'start'});
            }
        });
    });

    // Header background on scroll
    window.addEventListener('scroll', () => {
        const header = document.querySelector('header');
        if (window.scrollY > 100) {
            header.style.background = 'rgba(255, 255, 255, 0.98)';
            header.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
        } else {
            header.style.background = 'rgba(255, 255, 255, 0.98)';
        }
    });

    // Form submission
    document.querySelector('.contact-form').addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Thank you for your message! We will get back to you soon.');
        e.target.reset();
    });
</script>
</body>
</html>