<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function cmr_slide_of_the_day_shortcode( $atts ) {
    ob_start();
    ?>
    <style>
        :root {
            --primary-blue: #00baa8;
            --bg-dark: #000000;
            --text-white: #ffffff;
            --transition-smooth: all 0.8s cubic-bezier(0.33, 1, 0.68, 1);
        }
        /* Target the parent container and all wrappers to make them seamless full-width black and allow overflow */
        #slide,
        .brain,
        .elementor-element-12ee4ee,
        .elementor-element-12ee4ee > .e-con-inner,
        .elementor-element-5a5e00d,
        .elementor-element-0cb331f,
        .brain-component-wrapper {
            background-color: #000000 !important;
            background: #000000 !important;
            overflow: visible !important;
        }

        .elementor-element-12ee4ee,
        .elementor-element-12ee4ee > .e-con-inner {
            width: 100% !important;
            max-width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        /* Allow overflow across all component containers so teal layer and image layer pop out above and below */
        #slide,
        .brain,
        .elementor-element-12ee4ee,
        .elementor-element-12ee4ee > .e-con-inner,
        .elementor-element-5a5e00d,
        .elementor-element-0cb331f,
        .brain-component-wrapper,
        .slide-scroll-container,
        .slide-main-layout,
        .slide-left-column,
        .slide-sticky-box {
            overflow: visible !important;
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
            background-color: #000000 !important;
            background: #000000 !important;
            color: var(--text-white);
            font-family: 'Outfit', sans-serif;
            overflow: visible !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 100px 0 120px 0 !important;
            padding: 60px 0 !important;
            box-sizing: border-box;
            position: relative;
            z-index: 2;
        }
        .brain-component-wrapper * {
            box-sizing: border-box;
        }
        /* Initial spacer */
        .slide-spacer {
            display: none !important;
        }
        .slide-scroll-container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 40px;
            position: relative;
            overflow: visible !important;
        }
        .slide-main-layout {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 70px;
            width: 100%;
            margin: 0 auto;
            overflow: visible !important;
        }
        .slide-left-column {
            flex: 0 0 480px;
            max-width: 480px;
            position: relative;
            top: auto;
            overflow: visible !important;
        }
        .slide-right-column {
            flex: 1;
            padding-left: 20px;
            max-width: 620px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .slide-sticky-box {
            width: 100%;
            max-width: 480px;
            aspect-ratio: 1 / 1.15;
            position: relative;
            overflow: visible !important;
            margin: 0;
        }
        /* 1st Div: The Blue/Teal Background/Frame */
        .slide-blue-layer {
            position: absolute;
            top: -95px;
            left: 45px;
            width: calc(100% - 45px);
            height: 100%;
            background-color: var(--primary-blue);
            z-index: 1;
            transition: var(--transition-smooth);
            transform: none; 
        }
        /* 2nd Div: The Image (Overlapping) */
        .slide-image-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: calc(100% - 45px);
            height: 100%;
            background-color: #000000;
            z-index: 2;
            overflow: hidden;
            transition: var(--transition-smooth);
            transform: translateY(95px);
            box-shadow: none;
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
            font-size: 56px;
            font-weight: 800;
            line-height: 1;
            margin: 0;
            color: #fff;
            letter-spacing: 0px;
        }
        .slide-label {
            font-size: 12px;
            text-transform: capitalize;
            letter-spacing: 0px;
            color: rgba(255, 255, 255, 0.8);
            margin: 6px 0 0 0;
        }
        /* RIGHT SIDE CONTENT STYLES */
        .slide-content-wrapper {
            max-width: 100%;
        }
        .slide-badge {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1.5px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: 100%;
            text-transform: uppercase;
        }
        .slide-main-heading {
            font-size: 52px;
            font-weight: 600;
            line-height: 1.15;
            margin-bottom: 24px;
            letter-spacing: -1.5px;
            color: #fff;
        }
        .slide-description {
            font-size: 15px;
            line-height: 25px;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 36px;
            max-width: 520px;
        }
        .slide-cta-button{
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background-color: #fff;
            color: #000;
            padding: 16px 30px; 
            border-radius: 50px;
            text-decoration: none;
            font-size: 15px !important;   
            font-weight: 600;
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
                    if (blueLayer) blueLayer.style.transition = 'none';
                    if (imageLayer) imageLayer.style.transition = 'none';
                    if (img) img.style.transition = 'none';
                    
                    let mm = gsap.matchMedia();
                    
                    // Desktop & Tablet Landscape
                    mm.add("(min-width: 769px)", () => {
                        let tl = gsap.timeline({
                            scrollTrigger: {
                                trigger: triggerZone,
                                start: "top 80%",
                                end: "center center",
                                scrub: 1
                            }
                        });
                        
                        // Keep teal layer & image offset framing intact as per Figma design,
                        // smoothly scrub grayscale to full contrast color on scroll
                        tl.to(img, { filter: "grayscale(0)", ease: "none" }, 0);
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

