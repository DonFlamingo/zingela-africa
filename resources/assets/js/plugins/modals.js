$(document).ready(function() {
    $(document).on('hidden', '.modal', function(){
        // Todo
    });

    // Open on click
    $(document).on('click', '[data-modal]', function(){
        var data = $(this).data(),
            method = (typeof data.method == 'undefined' ? 'GET' : data.method),
            modal = $modal.initModal(data.modal);

        $modal.getModalContent(data, method, modal);
    });

    $(document).on('click', '[data-submit="modal"],.modal .update:visible, .modal .update_hidden', function(){
        var element = $(this);
        var modal = element.closest('.modal');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = form.serializeArray();

        method = (typeof method != 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data);
    });

    $(document).on('click', '.modal .update_with_files:visible', function(){
        var element = $(this);
        var modal = element.closest('.modal');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = new FormData(form['0']);

        method = (typeof method != 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data, true);
    });
});

var $modal = {
    initModal: function(modal) {
        var element = $('#' + modal);
        if (!element.length) {
            $('body').append('<div class="modal fade" id="' + modal + '"><div class="contents"></div></div>');
            element = $('#' + modal);
        }

        return element;
    },
    getModalContent: function(data, method, modal) {
        $.ajax({
            type: method,
            dataType: "html",
            url: data.url,
            data: {
                id: data.id
            },
            beforeSend: function() {
                loader.add( $('body') );
            },
            success: function(res){
                modal.find('.contents').html(res);
                modal.modal('show');

                initComponents( modal );
            },
            complete: function() {
                loader.remove( $('body') );
            }
        });
    },
    postData: function(url, method, modal, data, with_files) {
        if (method == 'PUT' || method == 'DELETE')
            method = 'POST';

        var modal_content = modal.find('.modal-content');

        var ajax = {
            type: method,
            dataType: "json",
            url: url + '?_=' + $.now(),
            data: data,
            beforeSend: function() {
                modal.find('.help-block.error').remove();
                modal.find('.has-error').removeClass('has-error');

                loader.add( modal_content );
            },
            success: function(res){
                if (res.status != 0) {
                    if (res.status == 1)
                        modal.modal('hide');

                    $modal.initCallback(res, modal.attr('id'));
                }

                loader.remove( modal_content );

                if (res.trigger) {
                    $(document).trigger(res.trigger, res);
                }
                if (typeof res.errors != 'undefined') {
                    $modal.parseErrors(res.errors, modal);
                }
            },
            complete: function() {
                loader.remove( modal_content );
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                handlerFail(jqXHR, textStatus, errorThrown);
            }
        };

        if (typeof with_files != 'undefined') {
            $.extend(ajax, {
                processData: false,
                contentType: false
            });
        }

        $.ajax(ajax);
    },
    parseErrors: function(errors, modal) {
        $modal.defaultParseErrors(errors, modal);
    },
    defaultParseErrors: function(errors, modal) {
        $.each( errors, function( key, value ) {
            var el = modal.find('input[name="' + key + '"]:not([type="radio"]), select[name="' + key + '"], textarea[name="' + key + '"], input[name="' + key + '_fake"]:not([type="radio"]), select[name="' + key + '_fake"], textarea[name="' + key + '_fake"]');
            el.after('<span class="help-block error input_error_' + key + '">' + value + '</span>');
            el.closest('.form-group').addClass('has-error');

            var tab = el.closest('.tab-pane').attr('id');
            if (typeof tab != 'undefined') {
                modal.find('a[href="#' + tab + '"]').addClass('has-error');
            }
        });
        if (modal.find('.help-block.error').length > 0) {
            var fromTop = modal.find('.help-block.error').first().position().top;
            if (fromTop == 0)
                fromTop = modal.find('.help-block.error').first().offsetParent().position().top;
        }
    },
    initCallback: function(res, id) {
        var fnc = id.replace('-','_') + '_modal_callback';
        var callback_fnc = window[fnc];

        if (typeof callback_fnc == 'function') {
            callback_fnc(res);
        }
    }
}
