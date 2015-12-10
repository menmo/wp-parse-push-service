jQuery(document).ready( function($) {
    var box = $('#pps_box');
    var spinner = box.find('.spinner');
    var info =  box.find('.info');
    box.find('button').click(function(e) {
        var button = $(this);
        e.preventDefault();
        info.html('');
        button.attr('disabled', 'disabled');
        spinner.css('visibility', 'visible');
        var data = {
            action: 'pps_push_notification',
            post_id: $('#post_ID').val(),
            message: $('#pps_alert').val(),
            channel: $('#pps_channel').val()
        };
        $.post(ajaxurl, data, function(response) {
            if(response == "reload") {
                document.location.reload(true);
            } else {
                response = JSON.parse(response);
                if(response.error) {
                    alert(response.error);
                } else {
                    info.html('Push sent.');
                }
                button.removeAttr('disabled');
                spinner.css('visibility', '');
            }
        });
    });
});