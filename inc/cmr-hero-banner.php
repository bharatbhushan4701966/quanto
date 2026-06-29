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
    }

    .btn-primary {
        width: 181px; /* MATCH OUTLINE BUTTON SIZE */
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        background: #FFFFFF;
        color: #0F0F0F !important;
        border: 1px solid #FFFFFF;
        border-radius: 40px;
        font-family: "Instrument Sans", sans-serif;
        font-size: 14px;
        font-weight: 600;
        line-height: 24px;
        letter-spacing: -0.18px;
        text-decoration: none;
        padding: 0 20px;
        transition: none !important;
    }

    .btn-primary:hover {
        background: #FFFFFF !important;
        color: #0F0F0F !important;
        transform: none !important;
    }

    /* ===== BUTTON ICON ===== */
    .hero-arrow-button-white {
        width: 16px;
        height: 16px;
        object-fit: contain;
        flex-shrink: 0;
    }

    /* ===== TALK TO ANALYST BUTTON ===== */
    .btn-outline {
        width: 181px; /* SAME SIZE */
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        background: transparent;
        color: #FFFFFF !important;
        border: 1px solid rgba(255,255,255,0.6);
        border-radius: 40px;
        font-family: "Instrument Sans", sans-serif;
        font-size: 14px;
        font-weight: 600;
        line-height: 24px;
        letter-spacing: -0.14px;
        text-decoration: none;
        padding: 0 22px;
        transition: none !important;
        cursor: pointer;
    }

    /* ===== NO HOVER CHANGE ===== */
    .btn-outline:hover {
        background: transparent !important;
        color: #FFFFFF !important;
        border-color: rgba(255,255,255,0.6) !important;
        transform: none !important;
    }

    /* ===== ICON ===== */
    .hero-arrow-button {
        width: 16px;
        height: 16px;
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

    .hero-arrow-button,
    .hero-arrow-button-white {
      width: 12px;
    }

    /* =========================
       INDICATORS
    ========================= */
    .hero-indicators {
      position: absolute;
      bottom: 40px;
      right: 80px;
      z-index: 5;
      display: flex;
      gap: 10px;
    }

    .dot {
      width: 40px;
      height: 6px;
      background: rgba(255,255,255,0.3);
      border-radius: 10px;
      transition: all 0.4s ease;
      cursor: pointer;
    }

    .dot.active {
      background: white;
      width: 60px;
    }

    /* =========================
       MOBILE HERO FIX
    ========================= */
    @media (max-width: 768px){

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
        border-radius: 40px;
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
        border-radius: 40px;
        font-size: 16px;
        font-weight: 600;
        border: 1px solid #ffffff;
        color: #ffffff !important;
        display: flex;
        align-items: center;
        justify-content: center;
        background: transparent;
      }
      
      .dot {
        display: none;
      }

      .dot.active {
        display: none;
      }
      
      .btn-primary img,
      .btn-outline img{
        width: 12px;
        margin-left: 6px;
      }
    }
    </style>

    <section class="hero">

      <!-- BACKGROUND (No Videos) -->
      <div class="hero-video-wrapper"></div>

      <!-- INDICATORS -->
      <div class="hero-indicators">
        <span class="dot active"></span>
        <span class="dot"></span>
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
      const dots = document.querySelectorAll('.dot');
      const title = document.querySelector('.hero-title');

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

      let index = 0;

      // Switch Texts automatically
      setInterval(() => {
        dots[index].classList.remove('active');
        index = (index + 1) % texts.length;
        dots[index].classList.add('active');

        title.classList.add('fade');
        setTimeout(() => {
          title.innerHTML = texts[index];
          title.classList.remove('fade');
        }, 300);
      }, 10000);

      // Dot Click handler
      dots.forEach((dot, i) => {
        dot.addEventListener('click', () => {
          if (index === i) return;

          dots[index].classList.remove('active');
          index = i;
          dots[index].classList.add('active');

          title.classList.add('fade');
          setTimeout(() => {
            title.innerHTML = texts[index];
            title.classList.remove('fade');
          }, 300);
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
