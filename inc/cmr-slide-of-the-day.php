<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function cmr_slide_of_the_day_shortcode( $atts ) {
    ob_start();
    ?>
    <style>
        :root {
            --primary-blue: #36ebd6;
            --bg-dark: #000000;
            --text-white: #ffffff;
            --transition-smooth: all 0.8s cubic-bezier(0.33, 1, 0.68, 1);
        }
        /* Target the parent container and all wrappers to make them seamless black */
        #slide,
        .brain,
        .elementor-element-12ee4ee,
        .elementor-element-12ee4ee > .e-con-inner,
        .elementor-element-5a5e00d,
        .elementor-element-0cb331f,
        .brain-component-wrapper {
            background-color: #000000 !important;
            background: #000000 !important;
        }
        
        /* ===== SLIDE OF DAY ICON ===== */
        .slide-day-icon{
            width:18px;
            height:18px;
            object-fit:contain;
            display:block;
            flex-shrink:0;
        }
        .brain-component-wrapper {
            background-color: var(--bg-dark);
            color: var(--text-white);
            font-family: 'Outfit', sans-serif;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scrollbar-width: none;
        }
        .brain-component-wrapper * {
            box-sizing: border-box;
        }
        /* Initial spacer */
        .slide-spacer {
            height: 8vh;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0.3;
            letter-spacing: 5px;
        }
        .slide-scroll-container {
            height: 100%;
            position: relative;
            padding: 0 5%;
        }
        .slide-main-layout {
            display: flex;
            align-items: flex-start;
            gap: 80px;
            max-width: 1140px;
            margin: 0 auto;
        }
        .slide-left-column {
            flex: 1;
            position: sticky;
            top: 5vh;
        }
        .slide-right-column {
            flex: 1;
            padding-top: 10vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .slide-sticky-box {
            width: 100%;
            aspect-ratio: 1 / 1.1;
            position: relative;
        }
        /* 1st Div: The Blue Background/Frame */
        .slide-blue-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: calc(100% + 100px);
            background-color: var(--primary-blue);
            z-index: 1;
            transition: var(--transition-smooth);
            transform: translateY(-60px); 
        }
        /* 2nd Div: The Image (Overlapping) */
        .slide-image-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #111;
            z-index: 2;
            overflow: hidden;
            transition: var(--transition-smooth);
            transform: translate(-40px, 60px);
            box-shadow: 20px 20px 50px rgba(0,0,0,0.5);
        }
        .slide-image-layer img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: grayscale(1);
            transition: var(--transition-smooth);
        }
        /* Overlay text ON the image */
        .slide-image-text-overlay {
            position: absolute;
            bottom: 40px;
            right: 40px;
            z-index: 3;
            text-align: right;
        }
        .slide-value {
            font-size: 54px;
            font-weight: 800;
            line-height: 64px;
            margin: 0;
            color:#fff;
            letter-spacing: 0px;
        }
        .slide-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0px;
            color: #fff;
            opacity: 0.7;
            margin: 0;
        }
        /* RIGHT SIDE CONTENT STYLES */
        .slide-content-wrapper {
            max-width: 100%;
        }
        .slide-badge {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: 100%;
        }
        .slide-main-heading {
            font-size: 48px;
            font-weight: 600;
            line-height: 58px;
            margin-bottom: 30px;
            letter-spacing: -1.5px;
            color: #fff;
        }
        .slide-description {
            font-size: 14px;
            line-height: 24px;
            color: #ffffff;
            margin-bottom: 40px;
        }
        .slide-cta-button{
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background-color: #fff;
            color: #000;
            padding: 16px 28px; 
            border-radius: 50px;
            text-decoration: none;
            font-size: 16px !important;   
            font-weight: 500;
            line-height: 1;               
            transition: all 0.3s ease;
        }
        /* ICON SIZE FIX */
        .slide-cta-button .button-icon{
            width: 16px;
            height: 16px;
            object-fit: contain;
        }
        /* HOVER (optional smooth lift like Figma) */
        .slide-cta-button:hover{
            transform: translateY(-2px);
        }
        /* SCROLL TRANSFORM STATE (Handled by GSAP now) */

        /* Mobile Responsiveness */
        @media (max-width: 1024px) {
            .slide-main-layout {
                flex-direction: column-reverse;
                gap: 50px;
            }
            .slide-right-column { padding-top: 50px; }
            .slide-main-heading { font-size: 40px; line-height: 48px; }
            .slide-value { font-size: 64px; }
        }

        @media (max-width: 768px) {
            #slide,
            .brain,
            .elementor-element-12ee4ee,
            .elementor-element-12ee4ee > .e-con-inner,
            .elementor-element-5a5e00d,
            .elementor-element-0cb331f,
            .brain-component-wrapper {
                background-color: #000000 !important;
                background: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
                min-height: 0 !important;
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
            }

            .brain-component-wrapper {
                padding: 40px 16px 50px 16px !important;
            }

            .slide-spacer {
                display: none !important;
            }

            .slide-scroll-container {
                padding: 0 !important;
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
                position: relative !important;
            }

            .slide-main-layout {
                flex-direction: column-reverse !important;
                gap: 32px !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 auto !important;
            }

            .slide-right-column {
                padding: 0 !important;
                width: 100% !important;
                text-align: center !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
            }

            .slide-content-wrapper {
                width: 100% !important;
                max-width: 340px !important;
                margin: 0 auto !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
            }

            .slide-badge {
                font-family: 'Outfit', 'Instrument Sans', sans-serif !important;
                font-size: 13px !important;
                font-weight: 600 !important;
                letter-spacing: 1px !important;
                text-transform: uppercase !important;
                color: #ffffff !important;
                margin-bottom: 16px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 8px !important;
                opacity: 1 !important;
            }

            .slide-day-icon {
                width: 16px !important;
                height: 16px !important;
            }

            .slide-main-heading { 
                font-family: 'Outfit', 'Instrument Sans', sans-serif !important;
                font-size: 30px !important; 
                line-height: 38px !important; 
                font-weight: 600 !important;
                margin-bottom: 16px !important;
                letter-spacing: -0.8px !important;
                color: #ffffff !important;
                text-align: center !important;
            }

            .slide-description {
                font-family: 'Outfit', 'Instrument Sans', sans-serif !important;
                font-size: 14.5px !important;
                line-height: 22px !important;
                color: rgba(255, 255, 255, 0.9) !important;
                margin-bottom: 24px !important;
                text-align: center !important;
            }

            .slide-cta-button {
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 8px !important;
                background-color: #ffffff !important;
                color: #000000 !important;
                width: 100% !important;
                max-width: 320px !important;
                height: 48px !important;
                padding: 0 24px !important;
                border-radius: 50px !important;
                font-family: 'Outfit', 'Instrument Sans', sans-serif !important;
                font-size: 15px !important;
                font-weight: 600 !important;
                text-decoration: none !important;
                margin: 0 auto !important;
                box-shadow: none !important;
            }

            .slide-cta-button .button-icon {
                width: 14px !important;
                height: 14px !important;
            }

            .slide-left-column {
                position: relative !important;
                top: auto !important;
                width: 100% !important;
                display: flex !important;
                justify-content: center !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .slide-sticky-box {
                width: 100% !important;
                max-width: 340px !important;
                aspect-ratio: 1 / 1.15 !important;
                margin: 0 auto !important;
                position: relative !important;
                background-color: #00ede9 !important;
                padding: 10px 10px 12px 10px !important;
                border-radius: 0 !important;
                box-sizing: border-box !important;
            }

            .slide-blue-layer {
                display: none !important;
            }

            .slide-image-layer {
                position: relative !important;
                top: auto !important;
                left: auto !important;
                width: 100% !important;
                height: 100% !important;
                transform: none !important;
                box-shadow: none !important;
                overflow: hidden !important;
                background: #000000 !important;
            }

            .slide-image-layer img {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                filter: grayscale(0) !important;
            }

            .slide-image-text-overlay {
                position: absolute !important;
                bottom: 20px !important;
                right: 20px !important;
                z-index: 5 !important;
                text-align: right !important;
            }

            .slide-value { 
                font-family: 'Outfit', 'Instrument Sans', sans-serif !important;
                font-size: 42px !important; 
                font-weight: 800 !important;
                line-height: 46px !important; 
                color: #ffffff !important;
                margin: 0 !important;
            }

            .slide-label {
                font-family: 'Outfit', 'Instrument Sans', sans-serif !important;
                font-size: 11px !important;
                font-weight: 500 !important;
                letter-spacing: 0px !important;
                color: #ffffff !important;
                opacity: 0.9 !important;
                margin: 2px 0 0 0 !important;
                text-transform: capitalize !important;
            }
        }
    </style>

    <div class="brain-component-wrapper">
        <div class="slide-spacer"></div>
        <div class="slide-scroll-container" id="slide-trigger-zone">
            <div class="slide-main-layout">
                <!-- Left Side: Interactive Image -->
                <div class="slide-left-column">
                    <div class="slide-sticky-box" id="slide-main-box">
                        <!-- 1st Div: Blue -->
                        <div class="slide-blue-layer"></div>
                        <!-- 2nd Div: Image (Overlaps blue with gap) -->
                        <div class="slide-image-layer">
                            <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/Rectangle-25.png" alt="Market Insight">
                        </div>
                        <!-- Overlay text on image -->
                        <div class="slide-image-text-overlay">
                            <h1 class="slide-value">$14.2B</h1>
                            <p class="slide-label">Projected Market Value (2028)</p>
                        </div>
                    </div>
                </div>
                <!-- Right Side: Text Content -->
                <div class="slide-right-column">
                    <div class="slide-content-wrapper">
                        <div class="slide-badge">
                            <img 
                            src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/05/slide-of-day.svg" 
                            class="slide-day-icon" 
                            alt="Slide of the Day"
                            > SLIDE OF THE DAY
                        </div>
                        <h2 class="slide-main-heading">India AI market growing at 18% YoY</h2>
                        <p class="slide-description">
                            Our latest study indicates that generative AI adoption among Indian SMEs surpass large enterprises by 2026, driven by localised language models.
                        </p>
                        <a href="#" class="slide-cta-button open-report-popup">
                            Download Free Report 
                            <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/Symbol-1.svg" class="button-icon" alt="arrow">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const mainBox = document.getElementById('slide-main-box');
            const triggerZone = document.getElementById('slide-trigger-zone');
            
            if (mainBox && triggerZone) {
                const initGSAP = () => {
                    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
                        setTimeout(initGSAP, 50);
                        return;
                    }
                    gsap.registerPlugin(ScrollTrigger);
                    
                    const blueLayer = mainBox.querySelector('.slide-blue-layer');
                    const imageLayer = mainBox.querySelector('.slide-image-layer');
                    const img = imageLayer.querySelector('img');
                    
                    // Remove CSS transitions so GSAP can scrub smoothly
                    blueLayer.style.transition = 'none';
                    imageLayer.style.transition = 'none';
                    img.style.transition = 'none';
                    
                    let mm = gsap.matchMedia();
                    
                    // Desktop & Tablet Landscape
                    mm.add("(min-width: 769px)", () => {
                        let tl = gsap.timeline({
                            scrollTrigger: {
                                trigger: triggerZone,
                                start: "top 75%",
                                end: "top 25%",
                                scrub: 1
                            }
                        });
                        
                        tl.to(blueLayer, { opacity: 0, y: -150, ease: "none" }, 0)
                          .to(imageLayer, { x: 0, y: 0, boxShadow: "0px 0px 0px rgba(0,0,0,0)", ease: "none" }, 0)
                          .to(img, { filter: "grayscale(0)", ease: "none" }, 0);
                    });
                    
                    // Mobile & Tablet Portrait
                    mm.add("(max-width: 768px)", () => {
                        let tl = gsap.timeline({
                            scrollTrigger: {
                                trigger: mainBox, // Trigger when the image itself enters viewport
                                start: "top 85%",
                                end: "center center",
                                scrub: 1
                            }
                        });
                        // On mobile, blue box and image remain static as a frame.
                        // Only the grayscale effect animates!
                        tl.to(img, { filter: "grayscale(0)", ease: "none" }, 0);
                    });
                };
                initGSAP();
            }

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

