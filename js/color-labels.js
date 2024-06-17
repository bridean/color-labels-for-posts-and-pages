jQuery(document).ready(function($) {
    $('.color-label-link').on('click', function(e) {
        e.preventDefault();

        var post_id = $(this).data('post-id');
        var nonce = $(this).data('nonce');  // Get the nonce from the data attribute

        // Hide all other color swatch containers
        $('.color-swatch-container').not('#color-swatch-' + post_id).hide();

        // Toggle the current color swatch container
        $('#color-swatch-' + post_id).toggle();
    });

    $('.color-swatch').on('click', function(e) {
        e.preventDefault();

        var post_id = $(this).closest('.color-swatch-container').attr('id').split('-')[2];
        var color = $(this).data('color');
        var nonce = $('.color-label-link[data-post-id="' + post_id + '"]').data('nonce');  // Get the nonce for this post

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'save_color_label',
                post_id: post_id,
                color: color,
                nonce: nonce  // Include the nonce in the request
            },
            success: function(response) {
                if (response.success) {
                    $('tr#post-' + post_id).css('background-color', color);
                } else {
                    alert('Failed to save color label.');
                }
            }
        });
    });

    // Hide the color swatch menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.color-label-link').length && !$(e.target).closest('.color-swatch-container').length) {
            $('.color-swatch-container').each(function() {
                if ($(this).is(':visible')) {
                    $(this).toggle();  // Use toggle to hide the visible swatch container
                }
            });
        }
    });

    // Hide the color swatch menu when clicking within another PPMI
    $('.wp-list-table .type-post, .wp-list-table .type-page').on('click', function(e) {
        if (!$(e.target).closest('.color-label-link').length && !$(e.target).closest('.color-swatch-container').length) {
            $('.color-swatch-container').hide();
        }
    });
});