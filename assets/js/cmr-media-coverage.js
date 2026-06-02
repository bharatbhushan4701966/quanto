jQuery(document).ready(function($) {
    var currentPage = 1;
    var currentPublisher = '';
    var currentSearch = '';
    var isLoading = false;

    function loadMediaCoverage(append = false) {
        if (isLoading) return;
        isLoading = true;

        var grid = $('#cmr-mc-grid-container');
        var loadMoreBtn = $('#cmr-mc-load-more');

        if (!append) {
            grid.html('<div class="cmr-mc-loading">Loading...</div>');
            loadMoreBtn.hide();
        } else {
            loadMoreBtn.text('Loading...').prop('disabled', true);
        }

        $.ajax({
            url: cmr_mc_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'cmr_filter_media_coverage',
                page: currentPage,
                publisher: currentPublisher,
                search: currentSearch
            },
            success: function(response) {
                if (response.success) {
                    if (!append) {
                        grid.html(response.data.html);
                    } else {
                        grid.append(response.data.html);
                    }

                    if (response.data.has_more) {
                        loadMoreBtn.show().text('Load More').prop('disabled', false);
                    } else {
                        loadMoreBtn.hide();
                    }
                }
                isLoading = false;
            },
            error: function() {
                if (!append) {
                    grid.html('<p class="cmr-mc-no-results">An error occurred while loading.</p>');
                }
                isLoading = false;
                loadMoreBtn.text('Load More').prop('disabled', false);
            }
        });
    }

    // Initial load
    loadMediaCoverage();

    // Publisher Pill Click
    $('.cmr-mc-pill').on('click', function(e) {
        // Ignore if it's the 'More' button itself
        if ($(this).hasClass('cmr-mc-more-btn')) return;

        $('.cmr-mc-pill').removeClass('active');
        $(this).addClass('active');

        // Also if it was inside dropdown, mark the dropdown trigger active too
        if ($(this).hasClass('cmr-mc-dropdown-item')) {
            $('.cmr-mc-more-btn').addClass('active');
        }

        currentPublisher = $(this).data('publisher');
        currentPage = 1;
        loadMediaCoverage(false);
    });

    // Search Input with Debounce
    var searchTimeout;
    $('#cmr-mc-search-input').on('input', function() {
        clearTimeout(searchTimeout);
        var val = $(this).val();
        searchTimeout = setTimeout(function() {
            currentSearch = val;
            currentPage = 1;
            loadMediaCoverage(false);
        }, 500); // 500ms delay
    });

    // Load More Click
    $('#cmr-mc-load-more').on('click', function() {
        currentPage++;
        loadMediaCoverage(true);
    });

    // Sticky Header Banner with Shadow
    var banner = $('.cmr-mc-top-banner');
    if (banner.length) {
        var bannerOffset = banner.offset().top;
        
        // Recalculate on resize in case layout changes
        $(window).on('resize', function() {
            // Only recalculate if it's not currently sticky to get true original position
            if (!banner.hasClass('is-sticky')) {
                bannerOffset = banner.offset().top;
            }
        });

        $(window).on('scroll', function() {
            var scrollPos = $(window).scrollTop();
            // Trigger when the scroll position passes the banner's original position minus the header offset
            // Assuming ~90px main header height (122px with admin bar)
            var offset = $('body').hasClass('admin-bar') ? 122 : 90;
            if (scrollPos >= bannerOffset - offset) {
                banner.addClass('is-sticky');
            } else {
                banner.removeClass('is-sticky');
            }
        });
    }
});
