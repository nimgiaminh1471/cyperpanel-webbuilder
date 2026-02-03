var nh_registration = {
    theme_id:'',
    sub_domain: '',
    checkDomainExist: function (domain) {
        $.ajax({
            url: '/contact/ContactPlugin/checkDomainExist',
            type: 'POST',
            async: false,
            data: {
                domain: domain
            },
            dataType: 'json',
            success: function (response) {
                if (response.code != 'success') {
                    nh_functions.showTooltipError('#domain', response.messages);
                    $('#check_domain').val('0');
                    $('#error_domain').val(response.messages)
                } else {
                    nh_functions.removeTooltipError('#domain');
                    $('#check_domain').val('1');
                }

            },
            error: function (response, json, errorThrown) {
            }
        });
    },
    loadInfoTheme: function (theme_id, callback) {
        var self = this;
        if (typeof(callback) != 'function') callback = function () {
        };

        if (theme_id > 0) {
            $('select[name="template"]').val(theme_id)
            $('select[name="template"]').trigger("chosen:updated");
        };

        $.ajax({
            url: '/contact/ContactPlugin/loadInfoTheme',
            type: 'POST',
            async: false,
            data: {
                theme_id: theme_id
            },
            dataType: 'json',
            success: function (response) {
                if (response.code == 'success') {
                    if (typeof(response.data.url_img) != "undefined" && response.data.url_img.length > 0) {
                        $('#info-theme').find('img.img-fluid').attr('src', '/' + response.data.url_img);
                        $('#info-theme').find('a').attr('href', 'http://' + response.data.code + self.sub_domain);
                        $('#info-theme').find('a').attr('target', '_blank');
                        $('#info-theme').removeClass('hidden');

                        callback(response);
                    }
                } else {
                    $('#info-theme').addClass('hidden');
                }
            },
            error: function (response, json, errorThrown) {
            }
        });
    }
}