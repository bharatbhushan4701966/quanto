<?php
/* Custom Zendesk-style Drill-down Mobile Menu */

function cmr_inject_mobile_mega_menu() {
    ?>
    <div class="cmr-mobile-nav-overlay" id="cmrMobileNav">
        <div class="cmr-mobile-nav-header">
            <a href="/" class="cmr-mobile-nav-logo">
                <img src="/wp-content/uploads/2026/06/cmr-logo-white.svg" alt="CMR" style="height: 30px; filter: invert(1);">
            </a>
            <button class="cmr-mobile-nav-close" aria-label="Close mobile menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div class="cmr-mobile-nav-viewport">
            
            <!-- Main Menu List -->
            <div class="cmr-mobile-nav-panel cmr-mobile-nav-panel-main cmr-active" id="cmrPanelMain">
                <a href="javascript:void(0)" class="cmr-mobile-nav-item" data-target="panel-who-we-are">
                    Who we are
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
                <a href="javascript:void(0)" class="cmr-mobile-nav-item" data-target="panel-what-we-do">
                    What we do
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
                <a href="javascript:void(0)" class="cmr-mobile-nav-item" data-target="panel-who-we-serve">
                    Who we serve
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
                <a href="javascript:void(0)" class="cmr-mobile-nav-item" data-target="panel-what-we-think">
                    What we think
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
                <a href="javascript:void(0)" class="cmr-mobile-nav-item" data-target="panel-connect">
                    Connect
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
                <a href="javascript:void(0)" class="cmr-mobile-nav-item" data-target="panel-newsroom">
                    Newsroom
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
            </div>

            <!-- Sub Panel: Who We Are -->
            <div class="cmr-mobile-nav-panel cmr-mobile-nav-panel-sub" id="panel-who-we-are">
                <button class="cmr-mobile-nav-back" data-target="cmrPanelMain">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    Who we are
                </button>
                <div class="cmr-mobile-nav-content">
                    <div class="cmr-mobile-nav-label">ABOUT CMR</div>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Company Overview</div>
                        <div class="cmr-mobile-nav-link-desc">Learn about our mission and history</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Leadership</div>
                        <div class="cmr-mobile-nav-link-desc">Meet the team driving our vision</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Careers</div>
                        <div class="cmr-mobile-nav-link-desc">Join our growing global team</div>
                    </a>
                </div>
            </div>

            <!-- Sub Panel: What We Do -->
            <div class="cmr-mobile-nav-panel cmr-mobile-nav-panel-sub" id="panel-what-we-do">
                <button class="cmr-mobile-nav-back" data-target="cmrPanelMain">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    What we do
                </button>
                <div class="cmr-mobile-nav-content">
                    <div class="cmr-mobile-nav-label">OUR SERVICES</div>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Industry Intelligence</div>
                        <div class="cmr-mobile-nav-link-desc">Comprehensive market tracking and analysis</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Custom Research</div>
                        <div class="cmr-mobile-nav-link-desc">Tailored insights for your specific business needs</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Consulting</div>
                        <div class="cmr-mobile-nav-link-desc">Strategic advisory for technology companies</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Go-to-Market Services</div>
                        <div class="cmr-mobile-nav-link-desc">Accelerate your product launch and growth</div>
                    </a>
                </div>
            </div>

            <!-- Sub Panel: Who We Serve -->
            <div class="cmr-mobile-nav-panel cmr-mobile-nav-panel-sub" id="panel-who-we-serve">
                <button class="cmr-mobile-nav-back" data-target="cmrPanelMain">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    Who we serve
                </button>
                <div class="cmr-mobile-nav-content">
                    <div class="cmr-mobile-nav-label">INDUSTRIES</div>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Technology & Telecom</div>
                        <div class="cmr-mobile-nav-link-desc">Hardware, software, and connectivity solutions</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Enterprise IT</div>
                        <div class="cmr-mobile-nav-link-desc">Digital transformation and enterprise architecture</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Consumer Tech</div>
                        <div class="cmr-mobile-nav-link-desc">Smartphones, wearables, and smart home</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Semiconductors</div>
                        <div class="cmr-mobile-nav-link-desc">Foundry, design, and component tracking</div>
                    </a>
                </div>
            </div>

            <!-- Sub Panel: What We Think -->
            <div class="cmr-mobile-nav-panel cmr-mobile-nav-panel-sub" id="panel-what-we-think">
                <button class="cmr-mobile-nav-back" data-target="cmrPanelMain">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    What we think
                </button>
                <div class="cmr-mobile-nav-content">
                    <div class="cmr-mobile-nav-label">RESEARCH & INSIGHTS</div>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Market Tracker Reports</div>
                        <div class="cmr-mobile-nav-link-desc">Quarterly market share and shipment analysis</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Whitepapers</div>
                        <div class="cmr-mobile-nav-link-desc">In-depth analysis of emerging technology trends</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Analyst Perspectives</div>
                        <div class="cmr-mobile-nav-link-desc">Expert opinions on industry developments</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Infographics</div>
                        <div class="cmr-mobile-nav-link-desc">Visual summaries of complex data</div>
                    </a>
                </div>
            </div>

            <!-- Sub Panel: Connect -->
            <div class="cmr-mobile-nav-panel cmr-mobile-nav-panel-sub" id="panel-connect">
                <button class="cmr-mobile-nav-back" data-target="cmrPanelMain">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    Connect
                </button>
                <div class="cmr-mobile-nav-content">
                    <div class="cmr-mobile-nav-label">CONNECT</div>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Enterprise Connect</div>
                        <div class="cmr-mobile-nav-link-desc">Insights for enterprise leaders</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">SMB Connect</div>
                        <div class="cmr-mobile-nav-link-desc">Growth strategies for SMBs</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Channel Connect</div>
                        <div class="cmr-mobile-nav-link-desc">Opportunities for channel partners</div>
                    </a>
                </div>
            </div>

            <!-- Sub Panel: Newsroom -->
            <div class="cmr-mobile-nav-panel cmr-mobile-nav-panel-sub" id="panel-newsroom">
                <button class="cmr-mobile-nav-back" data-target="cmrPanelMain">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    Newsroom
                </button>
                <div class="cmr-mobile-nav-content">
                    <div class="cmr-mobile-nav-label">MEDIA & EVENTS</div>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Press Releases</div>
                        <div class="cmr-mobile-nav-link-desc">Official announcements from CMR</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">In the News</div>
                        <div class="cmr-mobile-nav-link-desc">CMR analysts quoted in the media</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Events & Webinars</div>
                        <div class="cmr-mobile-nav-link-desc">Upcoming and past industry events</div>
                    </a>
                    <a href="#" class="cmr-mobile-nav-link">
                        <div class="cmr-mobile-nav-link-title">Media Kit</div>
                        <div class="cmr-mobile-nav-link-desc">Resources for journalists and partners</div>
                    </a>
                </div>
            </div>

        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var overlay = document.getElementById('cmrMobileNav');
        var closeBtn = overlay ? overlay.querySelector('.cmr-mobile-nav-close') : null;
        var mainPanel = document.getElementById('cmrPanelMain');
        var subPanels = overlay ? overlay.querySelectorAll('.cmr-mobile-nav-panel-sub') : [];
        var navItems = overlay ? overlay.querySelectorAll('.cmr-mobile-nav-item') : [];
        var backBtns = overlay ? overlay.querySelectorAll('.cmr-mobile-nav-back') : [];
        
        function closeOverlay() {
            if (!overlay) return;
            overlay.classList.remove('cmr-nav-open');
            document.body.style.overflow = '';
            setTimeout(function() {
                if (mainPanel) mainPanel.classList.remove('cmr-slide-left');
                subPanels.forEach(function(panel) { panel.classList.remove('cmr-active'); });
            }, 300);
        }

        document.addEventListener('click', function(e) {
            var toggle = e.target.closest('.menuBar-toggle, .quanto-menu-toggle');
            if (toggle) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                if (!overlay) {
                    alert("Mobile menu HTML missing. Purge cache!");
                    return;
                }
                overlay.classList.add('cmr-nav-open');
                document.body.style.overflow = 'hidden';
            }
        }, true);

        if (!overlay) return;
        if (closeBtn) closeBtn.addEventListener('click', closeOverlay);

        navItems.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var targetId = this.getAttribute('data-target');
                var targetPanel = document.getElementById(targetId);
                if (targetPanel && mainPanel) {
                    mainPanel.classList.add('cmr-slide-left');
                    targetPanel.classList.add('cmr-active');
                }
            });
        });

        backBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var parentPanel = this.closest('.cmr-mobile-nav-panel-sub');
                if (parentPanel && mainPanel) {
                    parentPanel.classList.remove('cmr-active');
                    mainPanel.classList.remove('cmr-slide-left');
                }
            });
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'cmr_inject_mobile_mega_menu', 100);
