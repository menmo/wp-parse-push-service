jQuery(document).ready( function($) {
    var box = $('#pps_box');
    var spinner = box.find('.spinner');
    box.find('button').click(function(e) {
        var button = $(this);
        e.preventDefault();
        button.attr('disabled', 'disabled');
        spinner.css('visibility', 'visible');
        var data = {
            action: 'pps_push_notification',
            post_id: $('#post_ID').val(),
            message: $('#pps_alert').val()
        };
        $.post(ajaxurl, data, function(response) {
            button.removeAttr('disabled');
            spinner.css('visibility', '');
        });
    });
});