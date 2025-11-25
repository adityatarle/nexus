<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon - Greenleaf</title>

    <!-- Bootstrap + Poppins + Animate.css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="{{ asset('assets/comming-soon/logo.png') }}" type="image/x-icon">
    <style>
        /* ──────────────────────  SAME STYLES YOU ALREADY HAVE  ────────────────────── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #000;
            color: white;
            overflow-x: hidden
        }

        .demo-banner {
            position: relative;
            min-height: 100vh;
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #1a2a6c);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
            overflow: hidden;
        }

        .demo-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 1
        }

        .demo-item {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
            pointer-events: none;
            opacity: .3;
            animation: float 6s ease-in-out infinite;
        }

        .demo-item img {
            width: 300px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .5);
            transition: transform .5s
        }

        .demo-item img:first-child {
            transform: translate(-180px, -100px) rotate(-15deg)
        }

        .demo-item img:last-child {
            transform: translate(180px, 80px) rotate(15deg)
        }

        .box-table {
            display: table;
            width: 100%;
            height: 100vh
        }

        .box-cell {
            display: table-cell;
            vertical-align: middle;
            position: relative;
            z-index: 2
        }

        .content h2 {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
            text-shadow: 0 4px 10px rgba(0, 0, 0, .5)
        }

        .content h2 span.element {
            color: #4ade80;
            font-weight: 800
        }

        .content p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 40px;
            opacity: .9;
            line-height: 1.7
        }

        .features ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 50px;
            flex-wrap: wrap
        }

        .features li img {
            height: 60px;
            filter: drop-shadow(0 5px 10px rgba(0, 0, 0, .4));
            transition: transform .3s
        }

        .features li:hover img {
            transform: scale(1.15)
        }

        .demo-images {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 40px
        }

        .demo-images img {
            width: 180px;
            border-radius: 16px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, .4);
            transition: transform .4s, box-shadow .4s;
            border: 3px solid rgba(255, 255, 255, .2)
        }

        .demo-images img:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 25px 50px rgba(0, 0, 0, .5)
        }

        .download-button {
            display: inline-block;
            padding: 18px 50px;
            font-size: 1.2rem;
            font-weight: 600;
            color: #1a2a6c;
            background: #fff;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .4);
            transition: all .4s;
            position: relative;
            overflow: hidden;
            margin-top: 30px;
        }

        .download-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(74, 222, 128, .3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width .6s, height .6s;
            z-index: 0;
        }

        .download-button span {
            position: relative;
            z-index: 1
        }

        .download-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, .5)
        }

        .download-button:hover::before {
            width: 300px;
            height: 300px
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(-50%, -50%) translateY(0)
            }

            50% {
                transform: translate(-50%, -50%) translateY(-20px)
            }
        }

        /* ──────────────────────  LOGO STYLES  ────────────────────── */
        .logo-wrapper {
            margin-bottom: 2rem;
        }

        .logo-wrapper img {
            height: 80px;
            /* default for desktop */
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, .4));
            transition: transform .4s ease;
        }

        .logo-wrapper img:hover {
            transform: scale(1.08);
        }

        /* ──────────────────────  RESPONSIVE  ────────────────────── */
        @media (max-width:992px) {
            .content h2 {
                font-size: 2.8rem
            }

            .demo-item img {
                width: 200px
            }

            .demo-item img:first-child {
                transform: translate(-120px, -80px) rotate(-15deg)
            }

            .demo-item img:last-child {
                transform: translate(120px, 60px) rotate(15deg)
            }

            .logo-wrapper img {
                height: 70px
            }
        }

        @media (max-width:768px) {
            .content h2 {
                font-size: 2.3rem
            }

            .content p {
                font-size: 1rem
            }

            .features ul {
                gap: 15px
            }

            .features li img {
                height: 45px
            }

            .demo-images img {
                width: 140px
            }

            .download-button {
                padding: 15px 35px;
                font-size: 1rem
            }

            .logo-wrapper img {
                height: 60px
            }
        }

        @media (max-width:576px) {
            .demo-item img {
                width: 150px
            }

            .demo-item img:first-child {
                transform: translate(-80px, -60px) rotate(-15deg)
            }

            .demo-item img:last-child {
                transform: translate(80px, 40px) rotate(15deg)
            }

            .logo-wrapper img {
                height: 55px
            }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
            transition: all 0.35s ease;
            min-height: 140px;
        }

        .glass-card:hover {
            background: rgba(255, 255, 255, 0.14);
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .social-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            color: white;
            font-size: 1.3rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .social-icon:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-4px) scale(1.1);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.4);
            color: white;
        }

        /* Optional: Brand colors on hover */
        .social-icon[href*="facebook"]:hover {
            background: #1877f2;
        }

        .social-icon[href*="twitter"]:hover {
            background: #000000;
        }

        .social-icon[href*="instagram"]:hover {
            background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
        }

        .social-icon[href*="youtube"]:hover {
            background: #ff0000;
        }

        .social-icon[href*="wa.me"]:hover {
            background: #25d366;
        }

        /* ──────────────────────  BRAND COLOR UPDATE ────────────────────── */
        .content h2 span.element {
            color: #01A54E;
            /* Replaced #4ade80 */
            font-weight: 800;
        }

        /* Hover text opacity utility */
        .hover-opacity {
            transition: opacity 0.3s ease;
        }

        .hover-opacity:hover {
            opacity: 1 !important;
        }

        /* ──────────────────────  GLASS FOOTER ────────────────────── */
        .glass-footer {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.875rem;
        }

        .download-button {
            animation: pulse-glow 2s infinite;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 10px 30px rgba(1, 165, 78, 0.3);
            }

            50% {
                box-shadow: 0 10px 40px rgba(1, 165, 78, 0.5);
            }
        }
    </style>
</head>

<body>

    <!-- ==================== DEMO BANNER ==================== -->
    <div class="demo-banner text-center bg-gray bg-cover">
        <div class="demo-item">
            <img src="{{ asset('assets/comming-soon/2.jpg') }}" alt="Demo">
            <img src="{{ asset('assets/comming-soon/1.jpg') }}" alt="Demo">
        </div>

        <div class="box-table">
            <div class="box-cell">
                <div class="container">
                    <div class="content">
                        <div class="row">
                            <div class="col-lg-12">

                                <!-- ───── LOGO ───── -->
                                <div class="logo-wrapper wow fadeInDown" data-wow-delay="100ms">
                                    <img src="{{ asset('assets/comming-soon/logo.png') }}" alt="Greenleaf Logo">
                                </div>

                                <!-- ───── HEADLINE ───── -->
                                <h2 class="wow fadeInUp">
                                    Revolutionize Your Farm with <br>
                                    <span class="element" id="typewriter"></span>
                                    <span class="typed-cursor">|</span>
                                </h2>
                                <!-- ───── DESCRIPTION ───── -->
                                <p class="wow fadeInUp" data-wow-delay="200ms">
                                    Premium equipment, smart solutions, and sustainable farming — all in one place.
                                    Download our app and get early access to exclusive deals!
                                </p>

                                <!-- ───── FEATURES: GLASSMORPHISM CARDS (Apple-style) ───── -->
                                <div class="features py-5">
                                    <div class="container">
                                        <div class="row g-4 g-lg-5 justify-content-center">

                                            <!-- Card 1 -->
                                            <div class="col-6 col-md-3">
                                                <div class="glass-card d-flex flex-column align-items-center justify-content-center text-center p-4 rounded-4 h-100 wow fadeInUp"
                                                    style="visibility:visible; animation-name:fadeInUp;">
                                                    <i class="fas fa-tractor fa-2x text-white mb-3"></i>
                                                    <h5 class="text-white fw-semibold mb-1">Quality Equipment</h5>
                                                    <p class="text-white text-opacity-75 small mb-0">Premium agriculture
                                                        machinery</p>
                                                </div>
                                            </div>

                                            <!-- Card 2 -->
                                            <div class="col-6 col-md-3">
                                                <div class="glass-card d-flex flex-column align-items-center justify-content-center text-center p-4 rounded-4 h-100 wow fadeInUp"
                                                    data-wow-delay="150ms"
                                                    style="visibility:visible; animation-delay:150ms; animation-name:fadeInUp;">
                                                    <i class="fa-solid fa-seedling fa-2x text-white mb-3"></i>
                                                    <h5 class="text-white fw-semibold mb-1">Organic Products</h5>
                                                    <p class="text-white text-opacity-75 small mb-0">100% natural
                                                        farming solutions</p>
                                                </div>
                                            </div>

                                            <!-- Card 3 -->
                                            <div class="col-6 col-md-3">
                                                <div class="glass-card d-flex flex-column align-items-center justify-content-center text-center p-4 rounded-4 h-100 wow fadeInUp"
                                                    data-wow-delay="300ms"
                                                    style="visibility:visible; animation-delay:300ms; animation-name:fadeInUp;">
                                                    <i class="fa-solid fa-truck fa-2x text-white mb-3"></i>
                                                    <h5 class="text-white fw-semibold mb-1">Fast Delivery</h5>
                                                    <p class="text-white text-opacity-75 small mb-0">Nationwide shipping
                                                        available</p>
                                                </div>
                                            </div>

                                            <!-- Card 4 -->
                                            <div class="col-6 col-md-3">
                                                <div class="glass-card d-flex flex-column align-items-center justify-content-center text-center p-4 rounded-4 h-100 wow fadeInUp"
                                                    data-wow-delay="450ms"
                                                    style="visibility:visible; animation-delay:450ms; animation-name:fadeInUp;">
                                                    <i class="fa-solid fa-coins fa-2x text-white mb-3"></i>
                                                    <h5 class="text-white fw-semibold mb-1">Secure Payment</h5>
                                                    <p class="text-white text-opacity-75 small mb-0">Safe and encrypted
                                                        transactions</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="notification-section">
                                    <p class="notification-text">
                                        Stay tuned! We'll be launching soon with exciting features and products.
                                    </p>
                                </div>


                                @if(session('error'))
                                <div class="alert alert-warning"
                                    style="max-width: 600px; margin: 0 auto 30px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); color: white;">
                                    {{ session('error') }}
                                </div>
                                @endif


                                <!-- ───── DOWNLOAD BUTTON ───── -->
                                <a href="{{ route('app.download') }}" class="download-button wow fadeInUp w-50 mx-auto"
                                    data-wow-delay="600ms" id="downloadBtn" download>
                                    <span>Download App Now</span>
                                </a>

                                <!-- ───── SCREENSHOTS ───── -->
                                <p class="mx-auto text-center mt-5 py-0 my-0 text-muted">Preview</p>
                                <div class="demo-images wow fadeInUp" data-wow-delay="800ms">
                                    <img src="{{ asset('assets/comming-soon/1.jpg') }}" alt="App Preview">
                                    <img src="{{ asset('assets/comming-soon/2.jpg') }}" alt="Equipment">
                                    <img src="{{ asset('assets/comming-soon/3.jpg') }}" alt="Farm Tech">
                                </div>

                                <!-- ───── SOCIAL LINKS ───── -->
                                <!-- SOCIAL LINKS: GLASS ICONS WITH HOVER -->
                                <div class="social-links mt-5 wow fadeInUp" data-wow-delay="1000ms">
                                    <a href="https://www.facebook.com/greenekart" class="social-icon" target="_blank"
                                        aria-label="Facebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="#" class="social-icon" target="_blank" aria-label="Twitter">
                                        <i class="fab fa-x-twitter"></i>
                                    </a>
                                    <a href="https://www.instagram.com/greekekart?igsh=YW1kcWRubmNrYXNm"
                                        class="social-icon" target="_blank" aria-label="Instagram">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                    <a href="https://youtube.com/@greenekart2626?si=PM9kj_cYg8Y08l6T"
                                        class="social-icon" target="_blank" aria-label="YouTube">
                                        <i class="fab fa-youtube"></i>
                                    </a>
                                    <a href="https://wa.me/+918295282656" class="social-icon" target="_blank"
                                        aria-label="WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </div>

                                <!-- SOCIAL ICON STYLES (Apple-style Glass + Hover) -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ==================== GLASS FOOTER ==================== -->
    <footer class="glass-footer py-4 mt-5">
        <div class="container">
            <div class="row align-items-center text-center text-md-start">
                <div class="col-md-6">
                    <p class="mb-0 text-white text-opacity-75 small">
                        &copy; 2025 <strong>Greenleaf</strong>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <a href="#" class="text-white text-opacity-75 small mx-2 text-decoration-none hover-opacity">Privacy
                        Policy</a>
                    <a href="#" class="text-white text-opacity-75 small mx-2 text-decoration-none hover-opacity">Terms
                        of Service</a>
                    <a href="mailto:support@nexusagri.com"
                        class="text-white text-opacity-75 small mx-2 text-decoration-none hover-opacity">support@nexusagri.com</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ==================== SCRIPTS ==================== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script>
        new WOW().init();

        /* ──────────────────────  TYPEWRITER EFFECT  ────────────────────── */
        const phrases = [
            "Greenleaf",
            "AI‑Powered Farming",
            "Smart Crop Insights",
            "Sustainable Harvest",
            "Data‑Driven Fields"
        ];

        let phraseIdx = 0;
        let charIdx = 0;
        let deleting = false;

        const TYPE_SPEED = 80;   // ms per character while typing
        const ERASE_SPEED = 50;   // ms per character while erasing
        const PAUSE_TIME = 1500; // ms to wait after a phrase is fully typed

        const $typewriter = document.getElementById('typewriter');

        function type() {
            const cur = phrases[phraseIdx];

            if (deleting) {
                $typewriter.textContent = cur.substring(0, charIdx - 1);
                charIdx--;
            } else {
                $typewriter.textContent = cur.substring(0, charIdx + 1);
                charIdx++;
            }

            // ── finished typing a phrase → pause → start erasing ──
            if (!deleting && charIdx === cur.length) {
                setTimeout(() => deleting = true, PAUSE_TIME);
            }
            // ── finished erasing → move to next phrase ──
            else if (deleting && charIdx === 0) {
                deleting = false;
                phraseIdx = (phraseIdx + 1) % phrases.length;
            }

            const speed = deleting ? ERASE_SPEED : TYPE_SPEED;
            setTimeout(type, speed);
        }

        // start after the page is ready
        document.addEventListener('DOMContentLoaded', () => setTimeout(type, 800));

        /* ──────────────────────  CURSOR BLINK  ────────────────────── */
        const $cursor = document.querySelector('.typed-cursor');
        setInterval(() => {
            $cursor.style.opacity = ($cursor.style.opacity === '0') ? '1' : '0';
        }, 500);

        /* ──────────────────────  DOWNLOAD BUTTON  ────────────────────── */
        // Download button will directly download the APK file
        // The link is already set to the download route, so clicking will trigger the download
        // If file doesn't exist, backend will return JSON error which browser will handle
    </script>
</body>

</html>