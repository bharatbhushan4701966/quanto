<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function cmr_slide_of_the_day_shortcode( $atts ) {
    ob_start();
    ?>
    <style>
        :root {
            --bg-dark: #0a0a0a;
            --teal-accent: #00bfa5;
            --text-gray: #9ca3af;
        }

        /* Top White Space / Section */
        .top-spacer {
            background-color: #ffffff;
            height: 80px;
            width: 100%;
        }

        /* Hero Dark Section */
        .hero-section {
            background-color: var(--bg-dark);
            position: relative;
            padding-top: 50px;
            padding-bottom: 50px;
            /* Allow elements to overflow top and bottom into white sections */
            overflow: visible;
            z-index: 10;
        }

        /* Relative container for the layered image and teal block */
        .image-container-wrapper {
            position: relative;
            max-width: 440px;
            margin: 0 auto;
        }

        /* Teal block protruding ABOVE the dark section and to the RIGHT */
        .teal-backdrop {
            position: absolute;
            top: -95px; /* Protrudes into the top white section */
            left: 0;
            width: calc(100% + 55px); /* Protrudes to the right */
            height: 480px;
            background-color: var(--teal-accent);
            z-index: 1;
            border-radius: 0px;
        }

        /* Main Image Card protruding BELOW the dark section into the bottom white section */
        .hero-image-card {
            position: relative;
            z-index: 2;
            background-color: #000000;
            width: 100%;
            height: 520px;
            margin-bottom: -110px; /* Hangs down into the bottom white section */
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            border-radius: 0px;
        }

        .hero-image-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center bottom;
            display: block;
        }

        /* Stat text overlay in the bottom right of the image */
        .stat-overlay {
            position: absolute;
            bottom: 24px;
            right: 24px;
            z-index: 3;
            text-align: right;
            color: #ffffff;
            pointer-events: none;
        }

        .stat-overlay .stat-number {
            font-size: 2.85rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.03em;
            margin-bottom: 4px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.8);
        }

        .stat-overlay .stat-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            letter-spacing: 0.02em;
            margin: 0;
            text-shadow: 0 1px 4px rgba(0,0,0,0.8);
        }

        /* Right Content Column */
        .content-col {
            padding-left: 3rem;
            color: #ffffff;
        }

        @media (max-width: 991.98px) {
            .content-col {
                padding-left: 0.75rem;
                margin-top: 7rem;
            }
            .teal-backdrop {
                top: -50px;
                width: 100%;
                height: 420px;
            }
            .hero-image-card {
                height: 440px;
                margin-bottom: -60px;
            }
        }

        /* Slide of the Day Badge */
        .badge-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #ffffff;
            margin-bottom: 1.5rem;
        }

        .badge-tag i {
            font-size: 0.95rem;
        }

        /* Title */
        .hero-title {
            font-size: clamp(2.4rem, 4vw, 3.25rem);
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: -0.03em;
            color: #ffffff;
            margin-bottom: 1.75rem;
        }

        /* Description Paragraph */
        .hero-desc {
            font-size: 0.95rem;
            line-height: 1.65;
            color: var(--text-gray);
            max-width: 440px;
            margin-bottom: 2.25rem;
            font-weight: 400;
        }

        /* Download Button */
        .btn-download {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #ffffff;
            color: #000000;
            font-weight: 700;
            font-size: 0.92rem;
            padding: 0.75rem 1.6rem;
            border-radius: 9999px;
            border: none;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .btn-download:hover {
            background-color: #f3f4f6;
            color: #000000;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.25);
        }

        .btn-download i {
            font-size: 0.85rem;
            font-weight: 800;
        }

        /* Bottom White Newsroom Section */
        .newsroom-section {
            background-color: #ffffff;
            padding-top: 130px; /* Space for overlapping image card */
            padding-bottom: 80px;
            position: relative;
            z-index: 1;
        }

        .newsroom-tag {
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #000000;
            margin-bottom: 1rem;
        }

        .newsroom-title {
            font-size: clamp(2rem, 3.5vw, 2.75rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #000000;
            margin: 0;
        }
    </style>

    <!-- Top White Area -->
    <div class="top-spacer"></div>

    <!-- Hero Dark Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                
                <!-- Left Column: Layered Teal Backdrop + Image Card -->
                <div class="col-lg-6 col-md-10 mx-auto">
                    <div class="image-container-wrapper">
                        <!-- Teal Block (overlaps top boundary and right side) -->
                        <div class="teal-backdrop"></div>
                        
                        <!-- Image Card (overlaps bottom boundary) -->
                        <div class="hero-image-card">
                            <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/Rectangle-25.png" alt="India AI Market">
                            
                            <!-- Stat Overlay -->
                            <div class="stat-overlay">
                                <div class="stat-number">$14.2B</div>
                                <div class="stat-label">Projected Market Value (2028)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Text & CTA -->
                <div class="col-lg-6">
                    <div class="content-col">
                        <!-- Slide Tag -->
                        <div class="badge-tag">
                            <i class="bi bi-calendar4-event"></i>
                            <span>SLIDE OF THE DAY</span>
                        </div>

                        <!-- Title -->
                        <h1 class="hero-title">
                            India AI market<br>growing at 18% YoY
                        </h1>

                        <!-- Paragraph -->
                        <p class="hero-desc">
                            Our latest study indicates that generative AI adoption among Indian SMEs surpass large enterprises by 2026, driven by localised language models.
                        </p>

                        <!-- CTA Button -->
                        <div>
                            <a href="#download" class="btn-download">
                                Download Free Report <i class="bi bi-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const reportBtn = document.querySelector('.slide-cta-button');
            if(reportBtn){
                reportBtn.addEventListener('click', function(e){
                    e.preventDefault();
                    if (typeof elementorProFrontend !== 'undefined' && elementorProFrontend.modules && elementorProFrontend.modules.popup) {
                        elementorProFrontend.modules.popup.showPopup({ id: 7758 });
                    } else {
                        console.log('Elementor Popup JS not loaded');
                    }
                });
            }
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'cmr_slide_of_the_day', 'cmr_slide_of_the_day_shortcode' );

