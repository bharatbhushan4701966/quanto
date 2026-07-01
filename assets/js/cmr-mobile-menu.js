/* Custom Zendesk-style Drill-down Mobile Menu JS */

document.addEventListener('DOMContentLoaded', function() {
    var overlay = document.getElementById('cmrMobileNav');
    if (!overlay) return;

    var closeBtn = overlay.querySelector('.cmr-mobile-nav-close');
    var mainPanel = document.getElementById('cmrPanelMain');
    var subPanels = overlay.querySelectorAll('.cmr-mobile-nav-panel-sub');
    var navItems = overlay.querySelectorAll('.cmr-mobile-nav-item');
    var backBtns = overlay.querySelectorAll('.cmr-mobile-nav-back');
    
    // Function to close the overlay
    function closeOverlay() {
        overlay.classList.remove('cmr-nav-open');
        document.body.style.overflow = ''; // Restore scrolling
        
        // Reset panels after animation
        setTimeout(function() {
            mainPanel.classList.remove('cmr-slide-left');
            subPanels.forEach(function(panel) {
                panel.classList.remove('cmr-active');
            });
        }, 300);
    }

    // Intercept clicks on the original theme mobile menu toggle
    // Assuming it's '.quanto-menu-toggle' or '.menuBar-toggle'
    var themeToggles = document.querySelectorAll('.menuBar-toggle, .quanto-menu-toggle');
    themeToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Open our custom overlay
            overlay.classList.add('cmr-nav-open');
            document.body.style.overflow = 'hidden'; // Prevent body scrolling
        });
    });

    // Close button logic
    closeBtn.addEventListener('click', closeOverlay);

    // Forward Navigation (Drill down)
    navItems.forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            var targetId = this.getAttribute('data-target');
            var targetPanel = document.getElementById(targetId);
            
            if (targetPanel) {
                // Slide main panel left
                mainPanel.classList.add('cmr-slide-left');
                // Slide target panel in
                targetPanel.classList.add('cmr-active');
            }
        });
    });

    // Backward Navigation
    backBtns.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var targetId = this.getAttribute('data-target');
            var parentPanel = this.closest('.cmr-mobile-nav-panel-sub');
            
            if (parentPanel) {
                // Slide current panel right (out)
                parentPanel.classList.remove('cmr-active');
                // Slide main panel right (in)
                mainPanel.classList.remove('cmr-slide-left');
            }
        });
    });
});
