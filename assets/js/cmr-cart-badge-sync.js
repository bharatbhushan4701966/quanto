/**
 * CMR Cart Badge Sync
 * Automatically updates the nav badge count when cart changes.
 * Works by intercepting the fetch() call made by the custom cart.
 */
(function() {
    // Patch fetch to intercept cart AJAX responses
    var originalFetch = window.fetch;
    window.fetch = function() {
        var args = arguments;
        return originalFetch.apply(this, args).then(function(response) {
            // Clone the response so we can read it without consuming it
            var clone = response.clone();
            
            // Check if this is our cart AJAX call
            var url = (typeof args[0] === 'string') ? args[0] : (args[0] && args[0].url ? args[0].url : '');
            if (url.indexOf('admin-ajax.php') !== -1) {
                clone.json().then(function(data) {
                    if (data && data.success && typeof data.data === 'object') {
                        // If cart_count is in the response, use it directly
                        if (typeof data.data.cart_count !== 'undefined') {
                            document.querySelectorAll('.cmr-nav-cart-badge-count').forEach(function(b) {
                                b.textContent = data.data.cart_count;
                            });
                        }
                        // Also try to extract count from the HTML response
                        if (data.data.html && typeof data.data.html === 'string') {
                            var match = data.data.html.match(/Shopping\s+Cart\s*\((\d+)\s*items?\)/i);
                            if (match) {
                                document.querySelectorAll('.cmr-nav-cart-badge-count').forEach(function(b) {
                                    b.textContent = match[1];
                                });
                            }
                            // Check for empty cart
                            if (data.data.html.indexOf('currently empty') !== -1) {
                                document.querySelectorAll('.cmr-nav-cart-badge-count').forEach(function(b) {
                                    b.textContent = '0';
                                });
                            }
                        }
                    }
                }).catch(function() { /* ignore parse errors */ });
            }
            return response;
        });
    };
})();
