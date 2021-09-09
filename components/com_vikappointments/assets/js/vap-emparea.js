/* list */

function EmployeeArea(form) {
	if (form === undefined) {
		form = '#adminForm';
	}

	this.form = form;
}

EmployeeArea.prototype.checkAll = function(checkbox) {
	// Use :visible selector in order to skip hidden checkboxes.
	// This is helpful to ignore hidden inputs while filtering the list.
	jQuery(this.form).find('input[name="cid[]"]:visible').prop('checked', checkbox.checked);
}

EmployeeArea.prototype.isChecked = function(checked) {
	// get toggle-all checkbox
	var allBox = jQuery(this.form).find('input[type="checkbox"].checkall-toggle');

	if (!checked || jQuery(this.form).find('input[name="cid[]"]').length != this.hasChecked()) {
		allBox.prop('checked', false);
	} else {
		allBox.prop('checked', true);
	}
}

EmployeeArea.prototype.hasChecked = function() {
	return jQuery(this.form).find('input[name="cid[]"]:checked').length;
}
