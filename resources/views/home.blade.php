<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.theme-assets')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LAVEA - Laundry Made Easy</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #ffffff;
            color: #111d38;
        }

        /* ================= NAVBAR ================= */

        .navbar {
            width: 100%;
            height: 88px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 7%;
            border-bottom: 1px solid #eef2f8;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            text-decoration: none;
            display: flex;
            flex-direction: column;
        }

        .logo h1 {
            color: #111d38;
            font-size: 34px;
            letter-spacing: 2px;
            font-weight: 800;
            line-height: 1;
        }

        .logo span {
            color: #7182a5;
            font-size: 12px;
            margin-top: 5px;
            letter-spacing: .3px;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .nav-menu a {
            text-decoration: none;
            color: #34415c;
            font-size: 14px;
            font-weight: 500;
            transition: .2s;
        }

        .nav-menu a:hover {
            color: #111d38;
        }

        .nav-menu .active {
            color: #425274;
            font-weight: 500;
        }

        /* SIGN UP */

        .signup-btn {
            padding: 11px 17px;
            border: 1px solid #111d38;
            border-radius: 8px;
            color: #111d38 !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            transition: .2s;
        }

        .signup-btn:hover {
            background: #111d38;
            color: #ffffff !important;
            border-color: #111d38;
            box-shadow: 0 4px 12px rgba(17, 29, 56, 0.20);
            transform: translateY(-2px);
        }

        /* SIGN IN */

        .signin-btn {
            padding: 11px 17px;
            border-radius: 8px;
            background: #111d38;
            color: white !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            transition: .2s;
        }

        .signin-btn:hover {
            background: #1b2d52;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(17, 29, 56, 0.25);
            transform: translateY(-2px);
        }


        /* ================= HERO ================= */

        .hero {
            min-height: 650px;
            display: flex;
            align-items: center;
            padding: 60px 7%;
            position: relative;
            overflow: hidden;
            background: linear-gradient(
                90deg,
                #ffffff 0%,
                #ffffff 43%,
                #f4f6fa 100%
            );
        }

        .hero-content {
            width: 47%;
            z-index: 2;
        }

        .small-title {
            color: #111d38;
            font-size: 14px;
            letter-spacing: 4px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .hero h2 {
            font-size: 68px;
            line-height: .98;
            margin-bottom: 25px;
            color: #111d38;
        }

        .hero h2 span {
            color: #111d38;
        }

        .hero-description {
            color: #61708f;
            font-size: 17px;
            line-height: 1.7;
            max-width: 520px;
            margin-bottom: 32px;
        }

        .hero-buttons {
            display: flex;
            gap: 18px;
            margin-bottom: 45px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 15px 28px;
            background: #111d38;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            transition: .2s;
        }

        .btn-primary:hover {
            background: #1b2d52;
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(17, 29, 56, 0.25);
            transform: translateY(-2px);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            padding: 15px 28px;
            border: 1.5px solid #111d38;
            color: #111d38;
            text-decoration: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            background: white;
        }

        .btn-secondary:hover {
            background: #111d38;
            color: #ffffff;
            border-color: #111d38;
            transform: translateY(-2px);
        }


        /* ================= FEATURES ================= */

        .features {
            display: flex;
            gap: 35px;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .feature-icon {
            width: 35px;
            height: 35px;
            border: 2px solid #111d38;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #111d38;
            font-size: 16px;
        }

        .feature-text strong {
            display: block;
            color: #344567;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .feature-text small {
            color: #8995ab;
            font-size: 10px;
        }


        /* ================= HERO IMAGE ================= */

        .hero-image {
            width: 53%;
            height: 560px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-image::before {
            content: "";
            position: absolute;
            width: 600px;
            height: 600px;
            background: #edf0f5;
            border-radius: 50%;
            right: -100px;
            top: -30px;
        }

        .hero-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            position: relative;
            z-index: 2;
            box-shadow: 0 20px 50px rgba(17, 29, 56, .12);
        }


        /* ================= ABOUT ================= */

        .about {
            padding: 90px 7%;
            background: #f7faff;
            text-align: center;
        }

        .section-label {
            color: #111d38;
            font-size: 13px;
            letter-spacing: 3px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .section-title {
            font-size: 38px;
            color: #111d38;
            margin-bottom: 18px;
        }

        .section-description {
            max-width: 700px;
            margin: auto;
            color: #687792;
            line-height: 1.7;
            font-size: 15px;
        }


        /* ================= SERVICES ================= */

        .services {
            padding: 90px 7%;
            background: white;
            text-align: center;
        }

        .service-grid {
            margin-top: 45px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .service-card {
            padding: 35px 25px;
            border: 1px solid #e7edf6;
            border-radius: 15px;
            background: white;
            transition: .2s;
        }

        .service-card:hover {
            transform: translateY(-6px);
            border-color: #111d38;
            box-shadow: 0 12px 28px rgba(17, 29, 56, 0.12);
        }

        .service-icon {
            width: 55px;
            height: 55px;
            margin: 0 auto 20px;
            border-radius: 12px;
            background: #edf0f5;
            color: #111d38;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
        }

        .service-card h3 {
            margin-bottom: 10px;
            color: #172442;
        }

        .service-card p {
            color: #71809a;
            font-size: 14px;
            line-height: 1.6;
        }


        /* ================= CONTACT ================= */

        .contact {
            padding: 80px 7%;
            background: #111d38;
            color: white;
            text-align: center;
        }

        .contact h2 {
            font-size: 36px;
            margin-bottom: 15px;
        }

        .contact p {
            color: #b9c5dc;
            margin-bottom: 25px;
        }

        .contact-btn {
            display: inline-block;
            padding: 13px 28px;
            background: #111d38;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.25s ease;
        }

        .contact-btn:hover {
            background: #1b2d52;
            color: #ffffff;
            box-shadow: 0 5px 14px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }


        /* ================= FOOTER ================= */

        footer {
            background: #0b152b;
            color: #8795af;
            text-align: center;
            padding: 20px;
            font-size: 12px;
        }


        /* ================= RESPONSIVE ================= */

        @media (max-width: 1000px) {

            .nav-menu {
                gap: 15px;
            }

            .hero {
                flex-direction: column;
                text-align: center;
            }

            .hero-content,
            .hero-image {
                width: 100%;
            }

            .hero-content {
                margin-bottom: 40px;
            }

            .hero-description {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-buttons,
            .features {
                justify-content: center;
            }

            .hero-image {
                height: 450px;
            }

            .service-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {

            .navbar {
                padding: 0 20px;
            }

            .nav-menu {
                display: none;
            }

            .hero {
                padding: 50px 25px;
            }

            .hero h2 {
                font-size: 48px;
            }

            .features {
                flex-direction: column;
                align-items: center;
            }

        }
    </style>
</head>

<body>


    <!-- ================= NAVIGATION ================= -->

    <nav class="navbar">

        <a href="{{ route('home') }}" class="logo">

            <h1>LAVEA</h1>

            <span>
                Laundry Made Easy
            </span>

        </a>


        <div class="nav-menu">

            <a href="{{ route('home') }}" class="active">
                Home
            </a>

            <a href="#about">
                About
            </a>

            <a href="#services">
                Service
            </a>

            <a href="#contact">
                Contact
            </a>

            <a href="{{ route('register') }}" class="signup-btn">
                Register
            </a>

            <a href="{{ route('login') }}" class="signin-btn">
                Login
            </a>

        </div>

    </nav>



    <!-- ================= HERO ================= -->

    <section class="hero">

        <div class="hero-content">

            <div class="small-title">
                CLEANER CLOTHES. BRIGHTER DAYS.
            </div>


            <h2>
                Laundry
                <br>
                <span>Made Easy</span>
            </h2>


            <p class="hero-description">

                We provide fast, reliable, and affordable laundry
                services to keep your clothes fresh, clean, and
                ready for whatever comes next.

            </p>


            <div class="hero-buttons">

                <a href="{{ route('register') }}" class="btn-primary">
                    Get Started →
                </a>

                <a href="#about" class="btn-secondary">
                    Learn More
                </a>

            </div>


            <div class="features">

                <div class="feature">

                    <div class="feature-icon">
                        ✓
                    </div>

                    <div class="feature-text">

                        <strong>
                            Clean & Safe
                        </strong>

                        <small>
                            Your clothes, our priority
                        </small>

                    </div>

                </div>


                <div class="feature">

                    <div class="feature-icon">
                        ◷
                    </div>

                    <div class="feature-text">

                        <strong>
                            On-Time Delivery
                        </strong>

                        <small>
                            When you need it
                        </small>

                    </div>

                </div>


                <div class="feature">

                    <div class="feature-icon">
                        ₱
                    </div>

                    <div class="feature-text">

                        <strong>
                            Affordable Rates
                        </strong>

                        <small>
                            Quality at a fair price
                        </small>

                    </div>

                </div>

            </div>

        </div>



        <!-- REAL PHOTO -->

        <div class="hero-image">

            <img
                src="{{ asset('images/laundry-home.jpg') }}?v={{ filemtime(public_path('images/laundry-home.jpg')) }}"
                alt="LAVEA Laundry Service"
            >

        </div>

    </section>



    <!-- ================= ABOUT ================= -->

    <section class="about" id="about">

        <div class="section-label">
            ABOUT LAVEA
        </div>

        <h2 class="section-title">
            Laundry Made Simple
        </h2>

        <p class="section-description">

            LAVEA is a modern laundry management and service system
            designed to make laundry services easier, faster, and
            more convenient for customers and staff.

        </p>

    </section>



    <!-- ================= SERVICES ================= -->

    <section class="services" id="services">

        <div class="section-label">
            OUR SERVICES
        </div>

        <h2 class="section-title">
            What We Offer
        </h2>

        <p class="section-description">

            Choose from our reliable laundry services designed
            for your everyday needs.

        </p>


        <div class="service-grid">


            <div class="service-card">

                <div class="service-icon">
                    ◉
                </div>

                <h3>
                    Wash & Fold
                </h3>

                <p>
                    Professional washing and folding
                    for your everyday clothes.
                </p>

            </div>



            <div class="service-card">

                <div class="service-icon">
                    ◈
                </div>

                <h3>
                    Dry Cleaning
                </h3>

                <p>
                    Careful cleaning for clothes that
                    require special treatment.
                </p>

            </div>



            <div class="service-card">

                <div class="service-icon">
                    ✓
                </div>

                <h3>
                    Ironing
                </h3>

                <p>
                    Neatly pressed clothes ready
                    for work, school, or special occasions.
                </p>

            </div>

        </div>

    </section>



    <!-- ================= CONTACT ================= -->

    <section class="contact" id="contact">

        <h2>
            Need Clean Clothes?
        </h2>

        <p>
            Let LAVEA take care of your laundry.
        </p>

        <a href="{{ route('register') }}" class="contact-btn">
            Get Started
        </a>

    </section>



    <!-- ================= FOOTER ================= -->
l    <footer>

        © {{ date('Y') }} LAVEA Laundry Management System.
        All rights reserved.

    </footer>


</body>
</html>
