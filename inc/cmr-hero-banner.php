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
      gap: 16px;
      align-items: center;
    }

    .btn-primary {
        min-width: 200px;
        height: 58px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: #FFFFFF;
        color: #0F0F0F !important;
        border: 1px solid #FFFFFF;
        border-radius: 50px;
        font-family: "Instrument Sans", sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: 1;
        letter-spacing: -0.18px;
        text-decoration: none;
        padding: 0 32px;
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
        width: 15px;
        height: 15px;
        object-fit: contain;
        flex-shrink: 0;
    }

    /* ===== TALK TO ANALYST BUTTON ===== */
    .btn-outline {
        min-width: 200px;
        height: 58px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: transparent;
        color: #FFFFFF !important;
        border: 1px solid rgba(255,255,255,0.75);
        border-radius: 50px;
        font-family: "Instrument Sans", sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: 1;
        letter-spacing: -0.14px;
        text-decoration: none;
        padding: 0 32px;
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
        width: 15px;
        height: 15px;
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
    @-webkit-keyframes dotProgressFill {
      0% {
        width: 0%;
      }
      100% {
        width: 100%;
      }
    }
    @keyframes dotProgressFill {
      0% {
        width: 0%;
      }
      100% {
        width: 100%;
      }
    }

    .hero .hero-indicators,
    .hero-indicators {
      position: absolute !important;
      bottom: 40px !important;
      right: 80px !important;
      z-index: 10 !important;
      display: flex !important;
      gap: 12px !important;
      align-items: center !important;
    }

    .hero .hero-indicators .dot,
    .hero-indicators .dot,
    .hero .dot {
      width: 55px !important;
      height: 6px !important;
      background: rgba(255, 255, 255, 0.35) !important;
      border-radius: 10px !important;
      position: relative !important;
      overflow: hidden !important;
      cursor: pointer !important;
      display: block !important;
      box-shadow: none !important;
      border: none !important;
      padding: 0 !important;
      margin: 0 !important;
      outline: none !important;
    }

    /* Keep container background semi-transparent when active so the white fill inside is visible */
    .hero .hero-indicators .dot.active,
    .hero-indicators .dot.active,
    .hero .dot.active {
      background: rgba(255, 255, 255, 0.35) !important;
      width: 55px !important;
    }

    .hero .hero-indicators .dot .dot-fill,
    .hero-indicators .dot .dot-fill,
    .hero .dot .dot-fill {
      position: absolute !important;
      top: 0 !important;
      left: 0 !important;
      height: 100% !important;
      width: 0%;
      background: #ffffff !important;
      border-radius: 10px !important;
      display: block !important;
      pointer-events: none !important;
      z-index: 2 !important;
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
    (function() {
      function initHeroBannerSlider() {
        const hero = document.querySelector('.hero');
        if (!hero) return;
        if (hero.dataset.cmrHeroInit === 'true') return;
        hero.dataset.cmrHeroInit = 'true';

        // 1. Replace title & indicators with fresh nodes to detach any legacy external scripts holding references
        const rawTitle = hero.querySelector('.hero-title');
        const rawIndicators = hero.querySelector('.hero-indicators');
        if (!rawTitle || !rawIndicators) return;

        const title = rawTitle.cloneNode(true);
        rawTitle.parentNode.replaceChild(title, rawTitle);

        const indicators = rawIndicators.cloneNode(true);
        rawIndicators.parentNode.replaceChild(indicators, rawIndicators);

        const dots = indicators.querySelectorAll('.dot');
        if (!dots.length) return;

        const texts = [
          `Shaping the future <br>
          through insights powered <br>
          by <span class="inline-wrap"><span>intelligence</span>
            <img class="hero-arrow-inline"
            src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/hero5-arrow.svg.svg">
          </span>`,

          `Driving the future <br>
          with intelligence-led<br>
          <span class="inline-wrap"><span>insights</span>
            <img class="hero-arrow-inline"
            src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/04/hero5-arrow.svg.svg">
          </span>`
        ];

        const DURATION = 6000; // 6 seconds per slide
        let activeIdx = 0;
        let slideTimer = null;

        function setSlide(index, isFirstRun) {
          if (slideTimer) {
            clearTimeout(slideTimer);
            slideTimer = null;
          }

          activeIdx = index;

          // 1. Reset all indicators
          dots.forEach(function(dot) {
            dot.classList.remove('active');
            let fill = dot.querySelector('.dot-fill');
            if (!fill) {
              fill = document.createElement('span');
              fill.className = 'dot-fill';
              dot.appendChild(fill);
            }
            fill.style.transition = 'none';
            fill.style.webkitTransition = 'none';
            fill.style.width = '0%';
          });

          // 2. Activate target indicator & animate progress fill from 0 to 100%
          if (dots[activeIdx]) {
            const activeDot = dots[activeIdx];
            activeDot.classList.add('active');
            let fill = activeDot.querySelector('.dot-fill');
            if (!fill) {
              fill = document.createElement('span');
              fill.className = 'dot-fill';
              activeDot.appendChild(fill);
            }
            fill.style.transition = 'none';
            fill.style.webkitTransition = 'none';
            fill.style.width = '0%';
            void fill.offsetWidth; // Force layout reflow

            requestAnimationFrame(function() {
              fill.style.transition = 'width ' + DURATION + 'ms linear';
              fill.style.webkitTransition = 'width ' + DURATION + 'ms linear';
              fill.style.width = '100%';
            });
          }

          // 3. Smooth Text Transition
          if (!isFirstRun) {
            title.classList.add('fade');
            setTimeout(function() {
              title.innerHTML = texts[activeIdx];
              title.classList.remove('fade');
            }, 250);
          } else {
            title.innerHTML = texts[activeIdx];
          }

          // 4. Automatically switch to next slide the instant the fill completes
          slideTimer = setTimeout(function() {
            const nextIdx = (activeIdx + 1) % texts.length;
            setSlide(nextIdx, false);
          }, DURATION);
        }

        // Click handlers on indicators
        dots.forEach(function(dot, i) {
          dot.addEventListener('click', function(e) {
            e.preventDefault();
            if (activeIdx === i) return;
            setSlide(i, false);
          });
        });

        // Initialize first slide
        setSlide(0, true);

        // Popups
        const popupTrigger = hero.querySelector('.open-popup');
        if (popupTrigger) {
          popupTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            if (typeof elementorProFrontend !== 'undefined') {
              elementorProFrontend.modules.popup.showPopup({ id: 7637 });
            }
          });
        }

        const reportTrigger = hero.querySelector('.open-report-popup');
        if (reportTrigger) {
          reportTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            if (typeof elementorProFrontend !== 'undefined') {
              elementorProFrontend.modules.popup.showPopup({ id: 7758 });
            }
          });
        }
      }

      if (document.readyState === 'interactive' || document.readyState === 'complete') {
        initHeroBannerSlider();
      } else {
        document.addEventListener('DOMContentLoaded', initHeroBannerSlider);
      }
      window.addEventListener('load', initHeroBannerSlider);
    })();
    </script>
    <?php
    return ob_get_clean();
}
