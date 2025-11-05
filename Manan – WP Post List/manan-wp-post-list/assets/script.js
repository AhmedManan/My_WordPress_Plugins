jQuery(document).ready(function($) {
    $('#manan-load-more').on('click', function() {
        let button = $(this);
        let page = button.data('page');

        $.ajax({
            url: manan_ajax_obj.ajaxurl,
            type: 'POST',
            data: {
                action: 'manan_load_more',
                page: page,
                nonce: manan_ajax_obj.nonce
            },
            beforeSend: function() {
                button.text('Loading...');
            },
            success: function(response) {
                if (response.trim() !== '') {
                    $('#manan-post-list').append(response);
                    button.data('page', page + 1);
                    button.text('Load More');
                } else {
                    button.text('No More Posts');
                    button.prop('disabled', true);
                }
            }
        });
    });
});
