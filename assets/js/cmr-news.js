jQuery(document).ready(function($) {
    $('.cmr-news-tab-btn').on('click', function() {
        // Remove active class from all buttons
        $(this).siblings().removeClass('active');
        // Add active class to clicked button
        $(this).addClass('active');

        // Hide all tab panes
        var container = $(this).closest('.cmr-news-container');
        container.find('.cmr-news-tab-pane').removeClass('active');

        // Show target tab pane
        var targetId = $(this).data('target');
        container.find('#' + targetId).addClass('active');
        
        // Update "Explore All" text
        var tabText = $(this).text().trim();
        container.find('.cmr-explore-all').text('Explore All ' + tabText + ' ↗');
    });
    
    // Initialize the first tab's button text
    var firstTabText = $('.cmr-news-tab-btn.active').text().trim();
    if(firstTabText) {
        $('.cmr-explore-all').text('Explore All ' + firstTabText + ' ↗');
    }
});
