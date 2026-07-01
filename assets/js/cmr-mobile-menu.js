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
    document.addEventListener('click', function(e) {
        var toggle = e.target.closest('.menuBar-toggle, .quanto-menu-toggle');
        if (toggle) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Open our custom overlay
            overlay.classList.add('cmr-nav-open');
            document.body.style.overflow = 'hidden'; // Prevent body scrolling
        }
    }, true); // Use capture phase to intercept before jQuery!

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
