//Copy to Clipboard
function copyToClipboard(element) {
    var jQuerytemp = jQuery("<input>");
    jQuery("body").append(jQuerytemp);
    jQuerytemp.val(jQuery(element).text()).select();
    document.execCommand("copy");
    jQuerytemp.remove();
}

//Get Specific Cookie
function getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

// Set Plate Value
function plateInserter() {
    let plateValue = getCookie("plate_threeDigit")+" "+getCookie("plate_alphabetDigit")+" "+getCookie("plate_twoDigit")+" | ایران "+getCookie("plate_sideDigit");
    jQuery("#vapcfinput5").val(plateValue);
}

// Set Split Plate Value
function plateSpliteInserter() {
    if (getCookie("plate_sideDigit") != "undefined") {
        jQuery("#sideDigit").val(getCookie("plate_sideDigit"));
    }
    if (getCookie("plate_threeDigit") != "undefined") {
        jQuery("#threeDigit").val(getCookie("plate_threeDigit"));
    }
    if (getCookie("plate_alphabetDigit") != "undefined") {
        jQuery("#alphabet").val(getCookie("plate_alphabetDigit"));
    }
    if (getCookie("plate_twoDigit") != "undefined") {
        jQuery("#twoDigit").val(getCookie("plate_twoDigit"));
    }
}

jQuery(document).ready(function () {
    // Negahban Data Tables
    jQuery('#dataTables').fancyTable({
        sortable: false,
        pagination:true,
        perPage:10,
        searchable:true,
        globalSearch:true,
        paginationClass:"uk-button uk-button-default",
        paginationClassActive:"uk-button-success active",
        inputPlaceholder: 'جستجوی موقعیت ، تاریخ ، پلاک ...'
    });

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

    // Persian Numbers
    jQuery('.fnum').persiaNumber('fa');
});