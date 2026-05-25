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
    });
});
