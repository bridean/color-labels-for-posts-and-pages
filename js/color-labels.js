jQuery(function($) {

    // Delegated: handle click on the "Color Label" link
    $(document).on('click', '.color-label-link', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var $link = $(this);
        var post_id = $link.data('post-id');
        var nonce = $link.data('nonce');

        // Ensure a palette exists; if not, inject one next to row actions
        var $container = $('#color-swatch-' + post_id);
        if (!$container.length) {
            var colors = ['#FFCDD2', '#FFE0B2', '#FFF9C4', '#C8E6C9', '#BBDEFB', '#E1BEE7', '#f6f7f7', '#ffffff'];
            var html = '<span class="color-swatch-container" id="color-swatch-' + post_id + '">';
            for (var i=0; i<colors.length; i++) {
                html += '<span class="color-swatch" data-color="' + colors[i] + '" style="background-color: ' + colors[i] + ';"></span>';
            }
            html += '</span>';
            var $rowActions = $link.closest('.row-actions');
            if ($rowActions.length) {
                $rowActions.append(html);
                $container = $('#color-swatch-' + post_id);
            }
        }

        // Hide all other palettes, then toggle this one
        $('.color-swatch-container').not('#color-swatch-' + post_id).hide();
        if ($container && $container.length) {
            $container.toggle();
        }
    });

    // Delegated: clicking a swatch saves and applies the color
    $(document).on('click', '.color-swatch', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var $swatch = $(this);
        var post_id = $swatch.closest('.color-swatch-container').attr('id').split('-')[2];
        var color = $swatch.data('color');
        var nonce = $('.color-label-link[data-post-id="' + post_id + '"]').data('nonce');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'clpp_save_color_label',
                post_id: post_id,
                color: color,
                nonce: nonce
            },
            success: function(response) {
                if (response && response.success) {
                    var row = document.getElementById('post-' + post_id);
                    if (row) { row.style.setProperty('background-color', color, 'important'); }
                } else {
                    alert('Failed to save color label.');
                }
            }
        });
    });

    // Hide the palette when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.color-label-link').length && !$(e.target).closest('.color-swatch-container').length) {
            $('.color-swatch-container:visible').toggle();
        }
    });

    // Hide the palette when clicking elsewhere in the row
    $(document).on('click', '.wp-list-table .type-post, .wp-list-table .type-page', function(e) {
        if (!$(e.target).closest('.color-label-link').length && !$(e.target).closest('.color-swatch-container').length) {
            $('.color-swatch-container').hide();
        }
    });
});

