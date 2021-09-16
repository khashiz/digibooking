if (typeof jQuery !== 'undefined') {
    window.DJNotificationsOptions = 'undefined';

} else {
    alert('jQuery is required. You can enable it in plugin settings');
}

// Custom debounce
(function ($, sr) {
    // debouncing function from John Hann
    // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
    var debounce = function (func, threshold, execAsap) {
        var timeout;
        return function debounced() {
            var obj = this, args = arguments;

            function delayed() {
                if (!execAsap)
                    func.apply(obj, args);
                timeout = null;
            };
            if (timeout) {
                clearTimeout(timeout);
            } else if (execAsap) {
                func.apply(obj, args);
            }
            timeout = setTimeout(delayed, threshold || 100);
        };
    }
    jQuery.fn[sr] = function (fn) {
        return fn ? this.on('DOMNodeInserted', debounce(fn)) : this.trigger(sr);
    };
})(jQuery, 'debouncedDNI');


jQuery.fn.DJNotifications = function (config) {
    var options = {}
    Object.assign(options, config);

    window.DJNotificationsOptions = options;

    jQuery('#system-message-container').css({
        'opacity' : '0',
        'height' : '0px',
        'overflow' : 'hidden'
    });

    // Observe for dom mutation
    jQuery('#system-message-container').debouncedDNI(function () {
        var container = jQuery(this).find('.alert');
        toastr.clear()


        var type = null;
        if (container.hasClass('alert-error')) {
            type = 'error';
        } else if (container.hasClass('alert-warning')) {
            type = 'success';
        } else if (container.hasClass('alert-success')) {
            type = 'success';
        } else if (container.hasClass('alert-info')) {
            type = 'info';
        }


        jQuery.map(container.find('div'), function (element, index) {
            var msg = jQuery(element).html();
            jQuery(document).trigger('djtoastr:renderMessage', [msg, type]);
        });
        container.remove();
    });
    // API
    jQuery(document).on('djtoastr:renderMessages', function (e, messages) {

        jQuery.each(messages, function (index, message) {
            jQuery(document).trigger('djtoastr:renderMessage', [message.message, message.type])
        });
    });

    jQuery(document).on('djtoastr:renderMessage', function (e, message, type) {
        e.preventDefault();

        switch (type.toLowerCase()) {
            case 'success' :
            case 'message' :
                toastr.success(message, Joomla.JText._('PLG_SYSTEM_DJNOTIFICATIONS_MESSAGE_HEADER_SUCCESS'), window.DJNotificationsOptions);
                break;
            case 'warning' :
            case 'notice' :
                toastr.warning(message, Joomla.JText._('PLG_SYSTEM_DJNOTIFICATIONS_MESSAGE_HEADER_WARNING'), window.DJNotificationsOptions);
                break;

            case 'error' :
                toastr.error(message, Joomla.JText._('PLG_SYSTEM_DJNOTIFICATIONS_MESSAGE_HEADER_ERROR'), window.DJNotificationsOptions);
                break;
            case 'info' :
            default :
                toastr.info(message, Joomla.JText._('PLG_SYSTEM_DJNOTIFICATIONS_MESSAGE_HEADER_INFO'), window.DJNotificationsOptions);
                break;
        }
    });
};


