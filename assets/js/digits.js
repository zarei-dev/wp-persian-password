String.prototype.toEnDigit = function() {
    return this.replace(/[\u06F0-\u06F9]+/g, function(digit) {
        var ret = '';
        for (var i = 0, len = digit.length; i < len; i++) {
            ret += String.fromCharCode(digit.charCodeAt(i) - 1728);
        }

        return ret;
    });
};

jQuery(document).on( 'input', '#dig_wc_log_otp, input#username', function() {
    var value = jQuery(this).val();
    jQuery(this).val(value.toEnDigit());
});