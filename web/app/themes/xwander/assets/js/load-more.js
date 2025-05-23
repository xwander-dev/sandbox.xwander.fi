jQuery(document).ready(function($) {
    var container = $('.posts-wrapper'),
        loadButton = $('.load-more'),
        page = 2;

    $.ajax({
        type: 'POST',
        url: load_more_params.ajaxurl,
        data: {
            'action': 'check_posts_remaining',
            'tag': loadButton.data('tag'),
            'security': load_more_params.security
        },
        success: function(response) {
            try {
                var res = JSON.parse(response);
                loadButton.data('posts-remaining', res.posts_remaining);
                if (loadButton.data('posts-remaining') > 0) {
                    loadButton.show();
                }
            } catch (e) {
                console.error("Error parsing response as JSON:", e, response);
            }
        }
    });

    loadButton.click(function(e) {
        e.preventDefault();
        var data = {
            'action': 'load_posts_by_ajax',
            'page': page,
            'tag': loadButton.data('tag'),
            'security': load_more_params.security
        };

        $.post(load_more_params.ajaxurl, data)
            .done(function(response) {
                try {
                    var res = typeof response === "object" ? response : JSON.parse(response);

                    container.append(res.html);

                    loadButton.data('posts-remaining', res.posts_remaining);

                    if (res.show_load_more) {
                        page++;
                    } else {
                        loadButton.hide();
                    }
                } catch (e) {
                    console.error("Error parsing response as JSON:", e, response);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX request failed:", textStatus, errorThrown);
            });
    });
});