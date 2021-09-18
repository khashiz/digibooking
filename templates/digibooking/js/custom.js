jQuery(document).ready(function () {
    // Avatar Image
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                jQuery('#imagePreview').css('background-image', 'url('+e.target.result +')');
                jQuery('#imagePreview').hide();
                jQuery('#imagePreview').fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    jQuery("#jform_cmavatar_cmavatar").change(function() {
        readURL(this);
    });
});
//Copy to Clipboard
function copyToClipboard(element) {
    var jQuerytemp = jQuery("<input>");
    jQuery("body").append(jQuerytemp);
    jQuerytemp.val(jQuery(element).text()).select();
    document.execCommand("copy");
    jQuerytemp.remove();
}