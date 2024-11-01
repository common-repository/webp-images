jQuery(document).ready( function($) {
    var webp_images_conversion_btn = $("button.wepb-images-start-conversion");
    var webp_images_delete_btn = $("button.wepb-images-bulk-delete");

    var webp_images_infos_wrap = $(".webp-images-bulk-infos");
    var totals_wrap = $(webp_images_infos_wrap).find(".total > div:last-child");
    var not_webp_wrap = $(webp_images_infos_wrap).find(".nowebp > div:last-child");
    var webp_wrap = $(webp_images_infos_wrap).find(".webp > div:last-child");

    function webp_images_is_loading(action = 'conversion', loading = true){
        var current_label_1 = $(webp_images_conversion_btn).text();
        var new_label_1 = $(webp_images_conversion_btn).attr('data-webp-images-label');

        var current_label_2 = $(webp_images_delete_btn).text();
        var new_label_2 = $(webp_images_delete_btn).attr('data-webp-images-label');

        if(action === 'conversion'){
            $(webp_images_conversion_btn).attr('data-webp-images-label', current_label_1);
            $(webp_images_conversion_btn).html(new_label_1);
            $(webp_images_conversion_btn).toggleClass('webp-images-loading', loading);
        } else {
            $(webp_images_delete_btn).attr('data-webp-images-label', current_label_2);
            $(webp_images_delete_btn).html(new_label_2);
            $(webp_images_delete_btn).toggleClass('webp-images-loading', loading);
        }
        $(webp_images_conversion_btn).attr('disabled', loading);
        $(webp_images_delete_btn).attr('disabled', loading);
    }

    function webp_images_error(message = WebpImages.error){
        alert(message);
    }

    function webp_images_ajax(action, success_callback, bis = false){
        if(!bis){
            webp_images_is_loading(action);
            $(window).on("beforeunload", function() {
                return WebpImages.beforeunload;
            });
        }
        $.ajax({
            type : "post",
            dataType : "json",
            url : WebpImages.ajaxurl,
            data : {
                action: "webp_images_ajax",
                type: action,
                token: WebpImages.nonce,
            },
            error: function(){
                webp_images_is_loading(action, false);
                webp_images_error();
            },
            success: function(response) {
                if(response.success !== true){
                    webp_images_is_loading(action, false);
                    webp_images_error();
                    return;
                }

                var webp_count = parseInt($(webp_wrap).text());
                var not_webp_count = parseInt($(not_webp_wrap).text());
                var new_count = parseInt(response.data);

                var result = success_callback(new_count, webp_count, not_webp_count);

                if(result === -1){ // Error
                    webp_images_is_loading(action, false);
                    webp_images_error();
                    return;
                }

                if(result === 1){ // One more run
                    webp_images_ajax(action, success_callback, true);
                    return;
                }

                if(result === 2){ // End
                    webp_images_is_loading(action, false);
                    $(webp_images_conversion_btn).attr('disabled', action === 'conversion');
                    $(webp_images_delete_btn).attr('disabled', action !== 'conversion');
                }
            }
        });
    }

    $(webp_images_conversion_btn).click(function(e){
        e.preventDefault();
        webp_images_ajax('conversion', function(new_count, webp_count, not_webp_count){
            if(new_count === not_webp_count){
                return -1;
            }

            $(not_webp_wrap).text(new_count);
            $(webp_wrap).text(webp_count + (not_webp_count - new_count));

            if(new_count > 0){
                return 1;
            }

            return 2;
        });
    });

    $(webp_images_delete_btn).click(function(e){
        e.preventDefault();
        webp_images_ajax('delete', function(new_count, webp_count, not_webp_count){
            if(new_count === webp_count){
                return -1;
            }

            $(webp_wrap).text(new_count);
            $(not_webp_wrap).text(not_webp_count + (webp_count - new_count));

            if(new_count > 0){
                return 1;
            }

            return 2;
        });
    });
});