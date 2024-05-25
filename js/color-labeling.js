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
                { name: 'Light Purple', value: '#E1BEE7' },
				{ name: 'Default 1: Light Grey', value: '#f6f7f7' },
                { name: 'Default 2: White', value: '#ffffff' }
            ];

            // Create a color selection interface
            var colorPicker = $('<div class="color-picker"><div><label for="custom-color">Enter a color (e.g., #FF0000):</label><br /><input type="text" id="custom-color" placeholder="#FF0000"></div><div class="swatches"></div><button class="reset-color">Reset</button><button class="close-picker">Close</button></div>');

            colors.forEach(function(color) {
                var colorOption = $('<div class="color-option" title="' + color.name + '"></div>').css({
                    'background-color': color.value,
                    'width': '30px',
                    'height': '30px',
                    'display': 'inline-block',
                    'margin': '2px',
                    'cursor': 'pointer',
                    'border': '1px solid #333'  // Add border style here
                });
                colorOption.on('click', function() {
                    saveColorLabel(postId, color.value);
                    colorPicker.remove();
                });
                colorPicker.find('.swatches').append(colorOption);
            });

            // Add reset functionality
            colorPicker.find('.reset-color').on('click', function() {
                console.log("Reset button clicked for post ID:", postId);
                resetColorLabel(postId);
                colorPicker.remove();
            });

            var customColorInput = colorPicker.find('#custom-color');
            customColorInput.on('change', function() {
                var color = $(this).val();
                if (color) {
                    saveColorLabel(postId, color);
                    colorPicker.remove();
                }
            });

            colorPicker.find('.close-picker').on('click', function() {
                colorPicker.remove();
            });

            // Append the color picker to the body and position it correctly
            $('body').append(colorPicker);
            colorPicker.css({
                position: 'absolute',
                top: e.pageY + 'px',
                left: e.pageX + 'px',
                background: '#fff',
                padding: '10px',
                border: '1px solid #ccc',
                zIndex: 10000
            });

            // Close the color picker when clicking outside of it
            $(document).on('click', function(event) {
                if (!$(event.target).closest('.color-picker, .color-label-link').length) {
                    colorPicker.remove();
                }
            });

            // Prevent the click event from propagating to the document
            colorPicker.on('click', function(event) {
                event.stopPropagation();
            });
        });
    });

    function saveColorLabel(postId, color) {
        console.log("Saving color label for post ID:", postId, "with color:", color);
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
                    console.log("Color applied to post ID:", postId);
                } else {
                    alert('Failed to save color label');
                }
            }
        });
    }

    function resetColorLabel(postId) {
        console.log("Resetting color label for post ID:", postId);
        var $postRow = $('tr#post-' + postId);
        // Remove the inline style for the background color completely
        $postRow.css('background-color', '');
        // Remove the color label from the post meta
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'save_color_label',
                post_id: postId,
                color: ''
            },
            success: function(response) {
                if (response.success) {
                    console.log("Color reset for post ID:", postId);
                    // Refresh the page to ensure the color reset is applied
                    location.reload();
                } else {
                    alert('Failed to reset color label');
                }
            }
        });
    }
})(jQuery);
