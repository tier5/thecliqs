var originalVal = this.originalVal = $.fn.val;
$.fn.val = function(value) {
	if (typeof value == 'undefined') {
		if ($(this).attr('isColorPicker')) {
			return $(widgetElementId).val();
		}
		return originalVal.call(this);
	} else {
		if ($(this).attr('isColorPicker')) {
			$(widgetElementId).val(value);
		}

		return originalVal.call(this, value);
	}
};