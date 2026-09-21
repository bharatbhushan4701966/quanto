<?php
/* Hero Banner Shortcode: [cmr_hero_banner] */

add_shortcode('cmr_hero_banner', 'cmr_hero_banner_shortcode');

function cmr_hero_banner_shortcode($atts) {
    ob_start();
    ?>
    <style>
    /* =========================
       HERO BASE
    ========================= */
    .hero {
      position: relative;
      min-height: 100vh;
      overflow: hidden;
    }

    /* =========================
       BACKGROUND
    ========================= */
    .hero-video-wrapper {
      position: absolute;
      inset: 0;
      z-index: 1;
      overflow: hidden;
      
      background:
        linear-gradient(282.61deg, rgba(3, 191, 188, 0.83) -10.41%, rgba(0, 63, 235, 0.83) 32.78%, rgba(72, 32, 176, 0.83) 89.34%),
        url("https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/hero-05-bg.png");
      
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }

    /* =========================
       CONTENT
    ========================= */
    .hero-content {
      position: relative;
      z-index: 3;
      color: white;
      padding: 80px;
      max-width: 1400px;
      padding-top: 287px;
      padding-left: 230px !important;
    }

    /* 1920+ layout */
    @media (min-width: 1920px) {
      .hero-content {
        width: 1440px;
        max-width: none;
        margin-left: clamp(150px, 20vw, 500px);
      }
    }

    /* =========================
       TEXT
    ========================= */
    .hero-title {
      font-size: 60px;
      line-height:70px;
      letter-spacing: -2px;
      color:white;
      font-weight: 500;
      transition: opacity 0.5s ease, transform 0.5s ease;
    }

    .hero-title span {
      color: #00EDE9;
    }

    .hero-title.fade {
      opacity: 0;
      transform: translateY(20px);
    }

    .hero p {
      margin-bottom: 30px;
      font-size:14px;
      line-height: 24px;
    }

    /* =========================
       BUTTONS
    ========================= */
    .buttons {
      display: flex;
      gap: 15px;
      align-items: center;
    }

    .btn-primary {
        min-width: 180px;
        height: 52px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: #FFFFFF;
        color: #0F0F0F !important;
        border: 1px solid #FFFFFF;
        border-radius: 50px;
        font-family: "Instrument Sans", sans-serif;
        font-size: 15px;
        font-weight: 600;
        line-height: 1;
        letter-spacing: -0.18px;
        text-decoration: none;
        padding: 0 28px;
        box-sizing: border-box;
        transition: transform 0.2s ease, box-shadow 0.2s ease !important;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: #FFFFFF !important;
        color: #0F0F0F !important;
        transform: translateY(-2px) !important;
    }

    /* ===== BUTTON ICON ===== */
    .hero-arrow-button-white {
        width: 14px;
        height: 14px;
        object-fit: contain;
        flex-shrink: 0;
    }

    /* ===== TALK TO ANALYST BUTTON ===== */
    .btn-outline {
        min-width: 180px;
        height: 52px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: transparent;
        color: #FFFFFF !important;
        border: 1px solid rgba(255,255,255,0.75);
        border-radius: 50px;
        font-family: "Instrument Sans", sans-serif;
        font-size: 15px;
        font-weight: 600;
        line-height: 1;
        letter-spacing: -0.14px;
        text-decoration: none;
        padding: 0 28px;
        box-sizing: border-box;
        transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease !important;
        cursor: pointer;
    }

    /* ===== HOVER CHANGE ===== */
    .btn-outline:hover {
        background: rgba(255,255,255,0.1) !important;
        color: #FFFFFF !important;
        border-color: #FFFFFF !important;
        transform: translateY(-2px) !important;
    }

    /* ===== ICON ===== */
    .hero-arrow-button {
        width: 14px;
        height: 14px;
        object-fit: contain;
        flex-shrink: 0;
    }

    /* =========================
       ICONS
    ========================= */
    .inline-wrap {
      display: inline-block;
      white-space: nowrap;
    }

    .hero-arrow-inline {
      width: 28px;
      vertical-align: middle;
      margin-left: 10px;
    }

    /* =========================
       INDICATORS WITH PROGRESS FILL
    ========================= */
    .hero-indicators {
      position: absolute;
      bottom: 40px;
      right: 80px;
      z-index: 5;
      display: flex;
      gap: 12px;
      align-items: center;
    }

    .dot {
      width: 55px;
      height: 6px;
      background: rgba(255,255,255,0.35);
      border-radius: 10px;
      position: relative;
      overflow: hidden;
      cursor: pointer;
      display: block;
      transition: background 0.3s ease;
    }

    .dot .dot-fill {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      width: 0%;
      background: #ffffff;
      border-radius: 10px;
      will-change: width;
    }

    /* =========================
       MOBILE HERO FIX
    ========================= */
    @media (max-width: 768px){

      section.hero,
      .hero {
        position: relative;
        min-height: 65vh !important;
        overflow: hidden;
      }

      .hero-content{
        padding: 100px 20px 40px !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
      }

      .hero-title{
        font-size: 38px;
        line-height: 48px;
        letter-spacing: -1px;
        font-weight: 600;
        max-width: 340px;
        margin: 0 auto 20px;
        text-align: center;
      }

      .hero-title br{
        display: none;
      }

      .hero-title .inline-wrap{
        white-space: nowrap;
      }

      .hero p{
        font-size: 16px;
        line-height: 24px;
        color: #ffffff;
        max-width: 340px;
        margin: 0 auto 30px;
        text-align: center;
      }

      .buttons{
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        margin-top: 10px;
        width: 100%;
        max-width: 340px;
      }

      .btn-primary{
        width: 100%;
        max-width: none;
        height: 52px;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 600;
        background: #ffffff;
        color: #000 !important;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .btn-outline{
        width: 100%;
        max-width: none;
        height: 52px;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 600;
        border: 1px solid #ffffff;
        color: #ffffff !important;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
      }
      
      .hero-indicators {
        display: none;
      }
      
      .btn-primary img,
      .btn-outline img{
        width: 14px;
        margin-left: 6px;
      }
    }
    </style>

    <section class="hero">

      <!-- BACKGROUND (No Videos) -->
      <div class="hero-video-wrapper"></div>

      <!-- INDICATORS -->
      <div class="hero-indicators">
        <span class="dot active"><span class="dot-fill"></span></span>
        <span class="dot"><span class="dot-fill"></span></span>
      </div>

      <!-- CONTENT -->
      <div class="hero-content">

      <h1 class="hero-title">
      Shaping the future <br>
      through insights powered <br>
      by <span class="inline-wrap">intelligence
        <img class="hero-arrow-inline"
        src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/hero5-arrow.svg.svg">
      </span>
    </h1>

        <p>
         From emerging trends to strategic opportunity, we translate market intelligence into the<br> clarity organisations need to act decisively.
        </p>

        <div class="buttons">
          <a href="#" class="btn-primary open-report-popup">
            Get Report
            <img class="hero-arrow-button-white"
                 src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/Symbol-1.svg">
          </a>

          <button class="btn-outline open-popup">
            Talk to Analyst
            <img class="hero-arrow-button"
                 src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/Symbol.svg">
          </button>
        </div>

      </div>

    </section>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
      const dots = document.querySelectorAll('.hero-indicators .dot');
      const title = document.querySelector('.hero-title');

      if (!dots.length || !title) return;

      const texts = [
      `Shaping the future <br>
      through insights powered <br>
      by <span class="inline-wrap">intelligence
      <img class="hero-arrow-inline"
      src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/hero5-arrow.svg.svg">
      </span>`,

      `Driving the future <br>
      with intelligence-led<br> insights`
      ];

      const SLIDE_DURATION = 6000; // 6 seconds per slide
      let currentIndex = 0;
      let slideTimer = null;

      function startSlide(index) {
        if (slideTimer) {
          clearTimeout(slideTimer);
          slideTimer = null;
        }

        currentIndex = index;

        // Reset all indicators fill
        dots.forEach((dot, i) => {
          dot.classList.remove('active');
          const fill = dot.querySelector('.dot-fill');
          if (fill) {
            fill.style.transition = 'none';
            fill.style.width = '0%';
          }
        });

        // Activate selected indicator & animate fill progress
        const currentDot = dots[currentIndex];
        if (currentDot) {
          currentDot.classList.add('active');
          const fill = currentDot.querySelector('.dot-fill');
          if (fill) {
            void fill.offsetWidth; // Force reflow
            fill.style.transition = `width ${SLIDE_DURATION}ms linear`;
            fill.style.width = '100%';
          }
        }

        // Change title text with smooth fade
        title.classList.add('fade');
        setTimeout(() => {
          title.innerHTML = texts[currentIndex];
          title.classList.remove('fade');
        }, 300);

        // Schedule next slide when progress fill completes
        slideTimer = setTimeout(() => {
          const nextIndex = (currentIndex + 1) % texts.length;
          startSlide(nextIndex);
        }, SLIDE_DURATION);
      }

      // Initial start
      startSlide(0);

      // Dot Click handler
      dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
          if (currentIndex === i) return;
          startSlide(i);
        });
      });

      // Popups
      const popupTrigger = document.querySelector('.open-popup');
      if (popupTrigger) {
        popupTrigger.addEventListener('click', function(e) {
          e.preventDefault();
          if (typeof elementorProFrontend !== 'undefined') {
            elementorProFrontend.modules.popup.showPopup({ id: 7637 });
          } else {
            console.log("Elementor Popup not loaded");
          }
        });
      }

      const reportTrigger = document.querySelector('.open-report-popup');
      if (reportTrigger) {
        reportTrigger.addEventListener('click', function(e) {
          e.preventDefault();
          if (typeof elementorProFrontend !== 'undefined') {
            elementorProFrontend.modules.popup.showPopup({ id: 7758 });
          } else {
            console.log("Elementor Popup not loaded");
          }
        });
      }
    });
    </script>
    <?php
    return ob_get_clean();
}
