(function($) {
    $(document).ready(function() {
        $('.color-label-link').on('click', function(e) {
            e.preventDefault();
            var postId = $(this).data('post-id');

            // Define predefined colors
            var colors = [
                { name: 'Light Red', value: '#FFCDD2' },
                { name: 'Light Orange', value: '#FFE0B2' },
                { name: 'Light Yellow', value: '#FFF9C4' },
                { name: 'Light Green', value: '#C8E6C9' },
                { name: 'Light Blue', value: '#BBDEFB' },
                { name: 'Light Purple', value: '#E1BEE7' }
            ];

            // Create a color selection interface
            var colorPicker = $('<div class="color-picker"><div><label for="custom-color">Enter a color (e.g., #FF0000):</label><input type="text" id="custom-color" placeholder="#FF0000"></div><div class="swatches"></div></div>');

            colors.forEach(function(color) {
                var colorOption = $('<div class="color-option" title="' + color.name + '"></div>').css('background-color', color.value);
                colorOption.on('click', function() {
                    saveColorLabel(postId, color.value);
                    colorPicker.remove();
                });
                colorPicker.find('.swatches').append(colorOption);
            });

            var customColorInput = colorPicker.find('#custom-color');
            customColorInput.on('change', function() {
                var color = $(this).val();
                if (color) {
                    saveColorLabel(postId, color);
                    colorPicker.remove();
                }
            });

            $('body').append(colorPicker);
            colorPicker.css({
                position: 'fixed',
                top: e.pageY + 'px',
                left: e.pageX + 'px',
                background: '#fff',
                padding: '10px',
                border: '1px solid #ccc',
                zIndex: 10000
            });
        });
    });

    function saveColorLabel(postId, color) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'save_color_label',
                post_id: postId,
                color: color
            },
            success: function(response) {
                if (response.success) {
                    // Apply the color to the row
                    $('tr#post-' + postId).css('background-color', color);
                } else {
                    alert('Failed to save color label');
                }
            }
        });
    }
})(jQuery);
