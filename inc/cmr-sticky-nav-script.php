<?php
/**
 * Sticky Navigation Script for intel-nav-bar
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action('wp_footer', function() {
    ?>
    <style>
    .intel-nav-fixed-js {
        position: fixed !important;
        left: 50% !important;
        transform: translateX(-50%);
        width: 100%;
        max-width: 1280px;
        z-index: 999990;
        background: transparent !important;
        padding-left: 20px !important;
        padding-right: 20px !important;
        margin-bottom: 0 !important;
        font-family: 'Instrument Sans', sans-serif !important;
        box-sizing: border-box !important;
    }
    .intel-nav-bar .intel-nav-title {
        font-size: 14px;
    }
    .intel-nav-fixed-js::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100vw;
        height: 100%;
        background: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        z-index: -1;
    }
    .intel-nav-fixed-js .cmr-nav-btn-subscribe {
        display: inline-flex !important;
    }
    .intel-nav-mobile-wrap {
        display: none;
    }
    @media (max-width: 1320px) {
        .intel-nav-fixed-js {
            padding-left: 20px !important;
            padding-right: 20px !important;
        }
    }
    @media (max-width: 768px) {
        /* Mobile Dropdown Format matching reference */
        /* Parent elementor container padding reset for 16px total screen edge space */
        .elementor-element-b632ab3,
        .elementor-element-b632ab3 > .e-con-inner,
        .elementor-element:has(.cmr-industry-intel-section),
        .e-con:has(.cmr-industry-intel-section),
        .e-con-inner:has(.cmr-industry-intel-section),
        .elementor-widget:has(.cmr-industry-intel-section),
        .elementor-widget-container:has(.cmr-industry-intel-section),
        .elementor-shortcode:has(.cmr-industry-intel-section) {
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            --padding-left: 0px !important;
            --padding-right: 0px !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        .cmr-industry-intel-section,
        .cmr-industry-intelligence,
        .cmr-marketing-services-section,
        .cmr-consulting-advisory-section {
            padding-left: 16px !important;
            padding-right: 16px !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            margin-top: 15px !important;
            margin-bottom: 40px !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        /* Mobile Dropdown Format matching reference */
        .intel-nav-bar {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            position: relative !important;
            width: 100% !important;
            min-height: 52px !important;
            padding: 0 16px !important;
            box-sizing: border-box !important;
            background: #ffffff !important;
            border-bottom: 1px solid #eeeeee !important;
            margin-bottom: 25px !important;
        }

        .cmr-industry-intel-section .intel-nav-bar:not(.intel-nav-fixed-js),
        .cmr-industry-intelligence .intel-nav-bar:not(.intel-nav-fixed-js),
        .cmr-marketing-services-section .intel-nav-bar:not(.intel-nav-fixed-js),
        .cmr-consulting-advisory-section .intel-nav-bar:not(.intel-nav-fixed-js) {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .cmr-intel-header-wrapper {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            width: 100% !important;
            gap: 16px !important;
            margin-top: 15px !important;
            margin-bottom: 25px !important;
        }

        .cmr-intel-header h2,
        .intel-header h2 {
            font-size: 26px !important;
            line-height: 1.25 !important;
            letter-spacing: -0.5px !important;
            margin: 0 0 8px 0 !important;
        }

        .cmr-intel-header p,
        .intel-header p {
            font-size: 14px !important;
            line-height: 1.5 !important;
            color: #475569 !important;
            margin: 0 !important;
        }

        .intel-controls {
            width: 100% !important;
            max-width: 100% !important;
            display: flex !important;
            justify-content: stretch !important;
            margin: 0 !important;
        }

        .intel-search {
            width: 100% !important;
            max-width: 100% !important;
            display: block !important;
        }

        .intel-controls #intel-search-form,
        #intel-search-form {
            width: 100% !important;
            max-width: 100% !important;
            height: 48px !important;
            box-sizing: border-box !important;
            display: flex !important;
            align-items: center !important;
            position: relative !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 50px !important;
            padding: 4px 6px 4px 18px !important;
            margin: 0 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
        }

        #intel-search-input {
            flex: 1 1 auto !important;
            width: 100% !important;
            min-width: 0 !important;
            border: none !important;
            background: transparent !important;
            outline: none !important;
            font-size: 14px !important;
            color: #0f172a !important;
            padding: 0 !important;
            font-family: 'Instrument Sans', sans-serif !important;
        }

        #intel-search-input::placeholder {
            color: #94a3b8 !important;
            font-size: 14px !important;
        }

        .intel-search-btn {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            border-radius: 50% !important;
            background: #5c24d3 !important;
            color: #ffffff !important;
            border: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            margin-left: 8px !important;
            cursor: pointer !important;
            padding: 0 !important;
        }

        .intel-search-btn i,
        .intel-search-btn svg {
            font-size: 13px !important;
            width: 13px !important;
            height: 13px !important;
            color: #ffffff !important;
        }

        .intel-grid {
            grid-template-columns: 1fr !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 0 35px 0 !important;
            gap: 24px !important;
            box-sizing: border-box !important;
        }

        .intel-card {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        .intel-card-img {
            width: 100% !important;
            border-radius: 8px !important;
            overflow: hidden !important;
            margin-bottom: 14px !important;
        }

        .intel-card-img img {
            width: 100% !important;
            height: auto !important;
            display: block !important;
        }

        #overview::before,
        #cmr-intel-trends-section::before,
        #cmr-market-updates::before,
        #reports::before,
        #cmr-in-news::before,
        #newsroom::before {
            display: none !important;
            height: 0 !important;
            margin-top: 0 !important;
        }

        #overview {
            scroll-margin-top: 0 !important;
        }

        .intel-nav-bar .intel-nav-title {
            display: block !important;
            font-family: 'Instrument Sans', sans-serif !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            color: #0f172a !important;
            letter-spacing: -0.3px !important;
            line-height: 1.25 !important;
            margin: 0 !important;
            flex: 1 1 auto !important;
            padding-right: 8px !important;
            word-break: break-word !important;
        }

        .intel-nav-mobile-wrap {
            display: flex !important;
            align-items: center !important;
            flex-shrink: 0 !important;
        }

        .intel-nav-dropdown-btn {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            background: transparent !important;
            border: none !important;
            padding: 8px 0 !important;
            cursor: pointer !important;
            font-family: 'Instrument Sans', sans-serif !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            color: #0f172a !important;
            line-height: 1 !important;
            outline: none !important;
        }

        .intel-nav-current-label {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            max-width: 140px !important;
        }

        .intel-nav-chevron {
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            flex-shrink: 0 !important;
            color: #0f172a !important;
        }

        .intel-nav-bar.is-dropdown-open .intel-nav-chevron {
            transform: rotate(180deg) !important;
        }

        /* Dropdown links container on mobile */
        .intel-nav-bar .intel-nav-links {
            display: none !important;
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            background: #ffffff !important;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12) !important;
            border-top: 1px solid #f0f0f0 !important;
            border-bottom: 2px solid #5c24d3 !important;
            flex-direction: column !important;
            align-items: stretch !important;
            padding: 0 !important;
            margin: 0 !important;
            gap: 0 !important;
            z-index: 10000000 !important;
            max-height: 380px !important;
            overflow-y: auto !important;
            box-sizing: border-box !important;
            -webkit-overflow-scrolling: touch !important;
        }

        .intel-nav-bar.is-dropdown-open .intel-nav-links {
            display: flex !important;
        }

        .intel-nav-bar .intel-nav-links a {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            width: 100% !important;
            box-sizing: border-box !important;
            padding: 9px 16px !important;
            font-family: 'Instrument Sans', sans-serif !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            line-height: 1.3 !important;
            color: #374151 !important;
            text-decoration: none !important;
            text-align: left !important;
            border-bottom: 1px solid #f0f0f0 !important;
            margin: 0 !important;
            transition: background 0.15s ease, color 0.15s ease !important;
        }

        .intel-nav-bar .intel-nav-links a:hover,
        .intel-nav-bar .intel-nav-links a.active {
            background-color: #f8f6ff !important;
            color: #5c24d3 !important;
            font-weight: 600 !important;
        }

        .intel-nav-bar .intel-nav-links a:last-child,
        .intel-nav-bar .intel-nav-links a:nth-last-child(2) {
            border-bottom: none !important;
        }

        .intel-nav-bar .intel-nav-links a.cmr-nav-btn-subscribe {
            display: none !important;
        }

        /* Mobile Sticky / Fixed State */
        .intel-nav-bar.intel-nav-fixed-js {
            position: fixed !important;
            left: 0 !important;
            right: 0 !important;
            transform: none !important;
            width: 100% !important;
            max-width: 100% !important;
            border-radius: 0 !important;
            background: #ffffff !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08) !important;
            border-bottom: 1px solid #e5e7eb !important;
            padding-left: 16px !important;
            padding-right: 16px !important;
            margin: 0 !important;
            z-index: 999990 !important;
        }

        .intel-nav-bar.intel-nav-fixed-js::before {
            display: none !important;
        }
    }
    </style>
    <script>
    if (!window.cmrStickyNavInitialized) {
        window.cmrStickyNavInitialized = true;
        
        function initStickyNav() {
            // Global click to close dropdown when clicking outside
            if (!window.cmrStickyNavClickOutsideAttached) {
                window.cmrStickyNavClickOutsideAttached = true;
                document.addEventListener('click', function(e) {
                    document.querySelectorAll('.intel-nav-bar.is-dropdown-open').forEach(function(bar) {
                        if (!bar.contains(e.target)) {
                            bar.classList.remove('is-dropdown-open');
                            var btn = bar.querySelector('.intel-nav-dropdown-btn');
                            if (btn) btn.setAttribute('aria-expanded', 'false');
                        }
                    });
                });
            }

            // Find all intel nav bars across sections
            const navBars = document.querySelectorAll('.cmr-industry-nav-bar, .cmr-latest-nav-bar, .intel-nav-bar');
            navBars.forEach(navBar => {
                if (navBar.dataset.stickyInitialized) return;
                navBar.dataset.stickyInitialized = 'true';

                const section = navBar.closest('.cmr-industry-intelligence, .cmr-latest-insights-section, .cmr-industry-intel-section, .cmr-marketing-services-section, .cmr-consulting-advisory-section, .cmr-enterprisecgd-wrapper, .cmr-channelcgd-wrapper, .cmr-smbcgd-wrapper, .cmr-mrg-wrapper, .elementor-section, .e-con, section') || navBar.parentElement;

                // Setup Mobile Dropdown Toggle Button
                let toggleWrap = navBar.querySelector('.intel-nav-mobile-wrap');
                if (!toggleWrap) {
                    toggleWrap = document.createElement('div');
                    toggleWrap.className = 'intel-nav-mobile-wrap';
                    
                    const navLinks = navBar.querySelectorAll('.intel-nav-links a:not(.cmr-nav-btn-subscribe)');
                    const defaultText = navLinks.length > 0 ? navLinks[0].textContent.trim() : 'Overview';
                    
                    toggleWrap.innerHTML = `
                        <button type="button" class="intel-nav-dropdown-btn" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="intel-nav-current-label">${defaultText}</span>
                            <svg class="intel-nav-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                    `;
                    navBar.appendChild(toggleWrap);
                    
                    const dropdownBtn = toggleWrap.querySelector('.intel-nav-dropdown-btn');
                    dropdownBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const isOpen = navBar.classList.toggle('is-dropdown-open');
                        dropdownBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    });
                }
                
                const placeholder = document.createElement('div');
                placeholder.className = 'cmr-nav-placeholder';
                placeholder.style.height = '0px';
                placeholder.style.marginBottom = '0px';
                navBar.parentNode.insertBefore(placeholder, navBar);
                
                function getStickyHeaderOffset() {
                    let offset = 0;
                    const wpAdminBar = document.getElementById('wpadminbar');
                    if (wpAdminBar && window.getComputedStyle(wpAdminBar).position === 'fixed') {
                        offset = wpAdminBar.offsetHeight;
                    }
                    
                    const headerSelectors = [
                        'header',
                        '.header',
                        '[data-elementor-type="header"]',
                        '.elementor-location-header',
                        '.elementor-sticky',
                        '.elementor-sticky--active',
                        '.elementor-sticky--effects',
                        'header .elementor-element',
                        '.header .elementor-element',
                        '.main-header-wrapper',
                        '.blog-header-wrapper'
                    ];
                    
                    const candidates = document.querySelectorAll(headerSelectors.join(', '));
                    candidates.forEach(h => {
                        if (!h || h === navBar || h.contains(navBar) || navBar.contains(h)) return;
                        const hStyle = window.getComputedStyle(h);
                        const isSticky = hStyle.position === 'fixed' || 
                                         hStyle.position === 'sticky' || 
                                         h.classList.contains('elementor-sticky--active') || 
                                         h.classList.contains('elementor-sticky--effects');
                        if (isSticky) {
                            const hRect = h.getBoundingClientRect();
                            if (hRect.top <= offset + 25 && hRect.bottom > offset && hRect.bottom < (window.innerHeight * 0.45)) {
                                if (hRect.bottom > offset) {
                                    offset = hRect.bottom;
                                }
                            }
                        }
                    });

                    // Fallback for mobile fixed header
                    if (offset === 0) {
                        const topHeader = document.querySelector('header, .header, .elementor-location-header');
                        if (topHeader) {
                            const rect = topHeader.getBoundingClientRect();
                            const hStyle = window.getComputedStyle(topHeader);
                            if ((hStyle.position === 'fixed' || hStyle.position === 'sticky') && rect.bottom > 0 && rect.bottom < 140) {
                                offset = rect.bottom;
                            }
                        }
                    }
                    
                    return Math.round(offset);
                }

                function updateSticky() {
                    const sectionRect = section.getBoundingClientRect();
                    const stickyOffset = getStickyHeaderOffset();

                    let boundaryBottom = sectionRect.bottom;
                    
                    let testimonialsSection = document.getElementById('cmr-testimonials-section') || 
                                              document.getElementById('testimonials') || 
                                              document.querySelector('.elementor-element-82ef444') ||
                                              document.querySelector('.elementor-widget-testimonial-carousel') ||
                                              document.querySelector('.elementor-widget-testimonial');
                                              
                    if (!testimonialsSection) {
                        const headings = Array.from(document.querySelectorAll('h1, h2, h3, h4, h5, h6')).filter(h => h.textContent.toLowerCase().includes('testimonial'));
                        if (headings.length > 0) {
                            testimonialsSection = headings[0].closest('.elementor-section') || headings[0].closest('section') || headings[0].parentElement;
                        }
                    }

                    if (testimonialsSection) {
                        boundaryBottom = testimonialsSection.getBoundingClientRect().top;
                    } else {
                        const footer = document.querySelector('footer, .elementor-location-footer');
                        if (footer) {
                            boundaryBottom = footer.getBoundingClientRect().top;
                        }
                    }

                    const triggerTop = placeholder.getBoundingClientRect().top;

                    if (triggerTop <= stickyOffset && boundaryBottom > (navBar.offsetHeight + stickyOffset)) {
                        if (!navBar.classList.contains('intel-nav-fixed-js')) {
                            placeholder.style.height = navBar.offsetHeight + 'px';
                            placeholder.style.marginBottom = window.innerWidth <= 768 ? '25px' : '30px';
                            navBar.classList.add('intel-nav-fixed-js');
                            document.body.appendChild(navBar); 
                            const subscribeBtn = navBar.querySelector('.cmr-nav-btn-subscribe');
                            if (subscribeBtn && window.innerWidth > 768) {
                                subscribeBtn.style.setProperty('display', 'inline-flex', 'important');
                            }
                        }
                        
                        if (boundaryBottom <= (navBar.offsetHeight + stickyOffset)) {
                            navBar.style.top = (boundaryBottom - navBar.offsetHeight) + 'px';
                        } else {
                            navBar.style.top = stickyOffset + 'px';
                        }
                    } else {
                        if (navBar.classList.contains('intel-nav-fixed-js')) {
                            navBar.classList.remove('intel-nav-fixed-js');
                            navBar.style.top = '';
                            placeholder.parentNode.insertBefore(navBar, placeholder.nextSibling);
                            placeholder.style.height = '0px';
                            placeholder.style.marginBottom = '0px';
                            const subscribeBtn = navBar.querySelector('.cmr-nav-btn-subscribe');
                            if (subscribeBtn && window.innerWidth > 768) {
                                subscribeBtn.style.setProperty('display', 'none', 'important');
                            }
                        }
                    }
                }
                
                window.addEventListener('scroll', updateSticky, { passive: true });
                window.addEventListener('resize', updateSticky, { passive: true });
                setTimeout(updateSticky, 100);
                setTimeout(updateSticky, 1000); // Failsafe for late render
                
                // Active section spy for mobile dropdown label
                function updateActiveSpy() {
                    if (window.innerWidth > 768) return;
                    const navLinks = navBar.querySelectorAll('.intel-nav-links a:not(.cmr-nav-btn-subscribe)');
                    if (!navLinks.length) return;
                    
                    const currentTop = navBar.classList.contains('intel-nav-fixed-js') ? (parseFloat(navBar.style.top) || 0) + (navBar.offsetHeight || 50) : 0;
                    const scrollPos = window.scrollY + currentTop + 70;
                    let activeLink = null;
                    
                    navLinks.forEach(link => {
                        const href = link.getAttribute('href');
                        if (!href || href === '#top') return;
                        const hashIdx = href.indexOf('#');
                        if (hashIdx === -1) return;
                        const targetId = href.substring(hashIdx + 1);
                        if (!targetId) return;
                        const target = document.getElementById(targetId);
                        if (target) {
                            const top = target.getBoundingClientRect().top + window.scrollY;
                            if (top <= scrollPos) {
                                activeLink = link;
                            }
                        }
                    });
                    
                    const labelSpan = navBar.querySelector('.intel-nav-current-label');
                    if (activeLink && labelSpan) {
                        if (labelSpan.textContent !== activeLink.textContent.trim()) {
                            labelSpan.textContent = activeLink.textContent.trim();
                        }
                        navLinks.forEach(l => l.classList.remove('active'));
                        activeLink.classList.add('active');
                    } else if (window.scrollY < 400 && labelSpan && navLinks.length > 0) {
                        labelSpan.textContent = navLinks[0].textContent.trim();
                        navLinks.forEach(l => l.classList.remove('active'));
                        navLinks[0].classList.add('active');
                    }
                }
                window.addEventListener('scroll', updateActiveSpy, { passive: true });

                // Add smooth scrolling for anchor links inside this navBar
                const links = navBar.querySelectorAll('.intel-nav-links a');
                links.forEach(link => {
                    link.addEventListener('click', function(e) {
                        // Close dropdown on mobile and update label
                        navBar.classList.remove('is-dropdown-open');
                        const dropdownBtn = navBar.querySelector('.intel-nav-dropdown-btn');
                        if (dropdownBtn) dropdownBtn.setAttribute('aria-expanded', 'false');
                        const labelSpan = navBar.querySelector('.intel-nav-current-label');
                        if (labelSpan && !this.classList.contains('cmr-nav-btn-subscribe')) {
                            labelSpan.textContent = this.textContent.trim();
                        }
                        const allLinks = navBar.querySelectorAll('.intel-nav-links a');
                        allLinks.forEach(l => l.classList.remove('active'));
                        if (!this.classList.contains('cmr-nav-btn-subscribe')) {
                            this.classList.add('active');
                        }

                        const href = this.getAttribute('href');
                        const linkText = this.innerText.toLowerCase().trim();
                        
                        // Special handling for Overview to scroll to top
                        if (linkText === 'overview' || href === '#top') {
                            e.preventDefault();
                            e.stopPropagation(); // Prevent Elementor from hijacking
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                            return;
                        }
                        
                        // For other links, check if they have a hash and point to the current page
                        if (!href) return;
                        
                        const hashIndex = href.indexOf('#');
                        if (hashIndex === -1) return;
                        
                        // If it's a full URL, check if the path matches the current page
                        if (hashIndex > 0) {
                            const linkUrl = new URL(href, window.location.href);
                            if (linkUrl.pathname !== window.location.pathname) {
                                return; // Different page, let the browser handle it
                            }
                        }
                        
                        const targetId = href.substring(hashIndex + 1);
                        if (!targetId) return;
                        
                        let targetElement = document.getElementById(targetId);
                        if (targetId === 'overview' || targetId === 'insights') {
                            const headingWrap = section.querySelector('.cmr-intel-header-wrapper, .cmr-intel-header');
                            if (headingWrap) {
                                targetElement = headingWrap;
                            }
                        }
                        
                        // Fallback 1: Try known shortcode wrapper selectors directly
                        if (!targetElement) {
                            const selectorMap = {
                                'reports': '.cmr-latest-section, .cmr-featured-reports-section, [id^="reports-"]',
                                'cmr-in-news': '.cmr-media-coverage-wrapper, .cmr-mc-wrapper, [class*="cmr-dmc-"]',
                                'cmr-market-updates': '.cmr-mui-section, .cmr-market-updates-section',
                                'featured': '.cmr-cancg-section, .cmr-enterprisecg-section, .cmr-smbcg-section, .cmr-featured-insight-section',
                                'latest': '.cmr-channelcgd-wrapper, .cmr-enterprisecgd-wrapper, .cmr-smbcgd-wrapper'
                            };

                            // Try exact match first, then partial match
                            if (selectorMap[targetId]) {
                                targetElement = document.querySelector(selectorMap[targetId]);
                            }
                            if (!targetElement) {
                                for (const [key, selector] of Object.entries(selectorMap)) {
                                    if (targetId.includes(key) || key.includes(targetId)) {
                                        targetElement = document.querySelector(selector);
                                        if (targetElement) break;
                                    }
                                }
                            }
                        }

                        // Fallback 2: Search by heading text for Elementor sections missing IDs
                        if (!targetElement) {
                            const headings = Array.from(document.querySelectorAll('h1, h2, h3, h4, h5, h6, .elementor-heading-title'));
                            let matchingHeading = null;
                            
                            if (targetId.includes('report')) {
                                matchingHeading = headings.find(h => {
                                    const txt = h.textContent.toLowerCase().trim();
                                    return (txt.includes('reports') || txt.includes('featured reports') || txt.includes('latest reports'))
                                        && !h.closest('.intel-nav-bar') && !h.closest('[class*="-nav"]');
                                });
                            } else if (targetId.includes('insight')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('insight'));
                            } else if (targetId.includes('market-update') || targetId.includes('market')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('market updates') || h.textContent.toLowerCase().includes('updates'));
                            } else if (targetId.includes('featured')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('featured'));
                            } else if (targetId.includes('latest')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('latest'));
                            } else if (targetId.includes('media-resource')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('media resource'));
                            } else if (targetId.includes('media-contact')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('media contact') || h.textContent.toLowerCase().includes('contact us'));
                            } else if (targetId.includes('newsroom') || targetId.includes('news')) {
                                matchingHeading = headings.find(h => {
                                    const txt = h.textContent.toLowerCase().trim();
                                    return (txt.includes('media coverage') || txt.includes('cmr in news') || txt.includes('cmr media coverage') || txt.includes('newsroom') || txt.includes('cmr live'))
                                        && !h.closest('.intel-nav-bar') && !h.closest('[class*="-nav"]');
                                });
                            } else if (targetId.includes('explore')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('explore industry intelligence') || h.textContent.toLowerCase().includes('intelligence'));
                            } else if (targetId === 'cmr-footer-card-section' || targetId.includes('subscribe')) {
                                // Search for headings or widgets containing "CMR Connect" or "Subscribe Now"
                                const possibleCards = Array.from(document.querySelectorAll('h1, h2, h3, h4, h5, h6, .elementor-heading-title, .elementor-button-text'));
                                matchingHeading = possibleCards.find(el => {
                                    const txt = el.textContent.toLowerCase();
                                    return txt.includes('cmr connect') || txt.includes('monthly digest') || (txt.includes('subscribe now') && !el.closest('.intel-nav-bar'));
                                });
                            }
                            
                            if (matchingHeading) {
                                targetElement = matchingHeading.closest('.elementor-section') || matchingHeading.closest('.e-con-parent') || matchingHeading.closest('.e-con-full') || matchingHeading.closest('.cmr-latest-section') || matchingHeading.closest('.cmr-media-coverage-wrapper') || matchingHeading.closest('.cmr-mc-wrapper') || matchingHeading.closest('[class*="cmr-"]') || matchingHeading.parentElement;
                            }
                        }

                        if (targetElement) {
                            e.preventDefault();
                            e.stopPropagation(); // Prevent Elementor from hijacking
                            
                            const stickyOffset = getStickyHeaderOffset();
                            const navHeight = navBar.offsetHeight || 52;
                            const finalOffset = stickyOffset + navHeight + 25; // 25px breathing room
                            
                            const targetPosition = targetElement.getBoundingClientRect().top + window.scrollY;
                            
                            window.scrollTo({
                                top: targetPosition - finalOffset,
                                behavior: 'smooth'
                            });
                        }
                    }, true); // Use capture phase to beat Elementor's native scroll
                });
            });
        }

        // Dynamic Anchor Script: assign IDs to Elementor sections based on headings
        // so sticky navbar anchor links work regardless of page builder setup
        function assignDynamicAnchors() {
            var allHeadings = document.querySelectorAll('h1, h2, h3, h4, h5, h6, .elementor-heading-title');
            
            allHeadings.forEach(function(h) {
                // Skip headings inside nav bars
                if (h.closest('.intel-nav-bar') || h.closest('[class*="-nav-bar"]')) return;
                
                var text = h.innerText.toLowerCase().trim();
                var section = h.closest('.elementor-top-section') || h.closest('section') || h.closest('.elementor-section') || h.closest('.e-con') || h.closest('.e-con-parent') || h.closest('.e-con-full');
                
                if (!section) return;
                // Don't overwrite existing IDs
                if (section.id) return;
                
                // "CMR in news" section
                if (text.includes("recognition of cmr in news") || text.includes("featured media coverage") || text === "cmr in news" || text.includes("cmr media coverage") || text === "media coverage") {
                    section.id = 'cmr-in-news';
                }
                
                // "Reports" section
                if (text.includes("similar reports") || text.includes("browse latest reports") || text.includes("featured reports") || text === "reports" || text.includes("latest reports")) {
                    if (!section.id) section.id = 'reports';
                }
                
                // "Market Updates" section
                if (text.includes("market intelligence &") || text.includes("market updates")) {
                    if (!section.id) section.id = 'cmr-market-updates';
                }
                
                // "Explore Industry Intelligence" section
                if (text.includes("explore industry intelligence") || text.includes("explore our industry intelligence")) {
                    if (!section.id) section.id = 'explore-industry-intelligence';
                }
                
                // "Featured Intelligence" / "Newsroom" section
                if (text.includes("featured intelligence") || (text.includes("media releases") && !text.includes("featured media")) || text === "newsroom") {
                    if (!section.id || (section.id !== 'cmr-in-news')) section.id = 'newsroom';
                }
                
                // "Insights" / "Latest Insights" section
                if (text.includes("latest insights") && !section.id) {
                    section.id = 'overview';
                }
                
                // "Trends" section
                if (text.includes("industry intelligence trends") && !section.id) {
                    section.id = 'trends';
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                assignDynamicAnchors();
                initStickyNav();
            });
        } else {
            assignDynamicAnchors();
            initStickyNav();
        }
    }
    </script>
    <?php
});
