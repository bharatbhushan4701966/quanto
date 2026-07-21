<?php
// Shortcode for Nav Search Popup
add_shortcode('cmr_nav_search', 'cmr_nav_search_shortcode');
function cmr_nav_search_shortcode() {
    ob_start();
    ?>
    <style>
    .cmr-nav-search-container {
        position: relative;
        display: inline-block;
    }
    .cmr-nav-search-trigger {
        background: transparent;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        color: #333;
        transition: color 0.3s ease;
    }
    .cmr-nav-search-trigger:hover {
        color: #7c3aed;
    }
    .cmr-nav-search-trigger svg {
        width: 24px;
        height: 24px;
    }
    
    .cmr-search-overlay-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background: rgba(255, 255, 255, 0.95);
        z-index: 999999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .cmr-search-overlay-wrapper.active {
        opacity: 1;
        visibility: visible;
    }
    
    .cmr-search-overlay-close {
        position: absolute;
        top: 40px;
        right: 40px;
        background: #fff;
        border: 1px solid #e2e8f0;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 24px;
        color: #333;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .cmr-search-overlay-close:hover {
        border-color: #7c3aed;
        color: #7c3aed;
    }
    
    .cmr-search-overlay-content {
        width: 100%;
        max-width: 800px;
        padding: 0 20px;
    }
    </style>
    
    <div class="cmr-nav-search-container">
        <!-- The trigger icon -->
        <button type="button" class="cmr-nav-search-trigger" onclick="document.getElementById('cmr-search-overlay').classList.add('active');">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                <path d="M20 20L17 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
    </div>
    
    <!-- The Overlay -->
    <div id="cmr-search-overlay" class="cmr-search-overlay-wrapper">
        <button class="cmr-search-overlay-close" onclick="document.getElementById('cmr-search-overlay').classList.remove('active');">
            <i class="ri-close-line"></i>
        </button>
        
        <div class="cmr-search-overlay-content">
            <?php get_search_form(); ?>
        </div>
    </div>
    
    <?php
    return ob_get_clean();
}
