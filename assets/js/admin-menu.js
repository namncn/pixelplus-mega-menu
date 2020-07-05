(function($) {
    "use strict";

    function initImageField() {
        var media_frame = null;
        $('.menu-img').on('click', function(e) {
            e.preventDefault();

            if (e.target !== this) {
                return;
            }
            var button = $(this),
                field = button.parent().find('input');
            if (null !== media_frame) {
                media_frame.open();
                return false;
            }

            // Create new wp.media instance.
            media_frame = wp.media({
                title: pamm.media_title,
                library: {
                    type: 'image'
                },
                multiple: false,
                button: {
                    text: pamm.media_button
                }
            });

            // Capture the media pop section event.
            media_frame.on('select', function() {
                var image = media_frame.state().get('selection').first().toJSON();
                button.css('background-image', 'url(' + image.url + ')');
                button.addClass('has-img');
                field.val(image.url);
            });

            media_frame.open();
            return false;
        });

        $('.menu-img span').on('click', function(e) {
            e.preventDefault();

            var button = $(this).parent(),
                field = button.parent().find('input');

            button.removeClass('has-img');
            button.css('background-image', 'url()');
            field.val('');
        });
    }

    $(document).ready(function() {
        initImageField();
    });

    if ('undefined' != typeof wpNavMenu && wpNavMenu.menusChanged) {
        initImageField();
    }

    $(document).ajaxSuccess(function(event, xhr, settings) {
        let data = new URLSearchParams(settings.data)
        if ('add-menu-item' === data.get('action')) {
            initImageField();
        }
    });
})(jQuery);