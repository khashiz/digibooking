/*
 * CURRENCY
 */

function Currency(symbol, options) {
	if (!arguments.callee.instance) {

		if (options === undefined) {
			options = {};
		}

		this.symbol 	= symbol;
		this.position 	= (options.hasOwnProperty('position') && options.position == 2 ? 2 : 1);
		this.decimals 	= (options.hasOwnProperty('separator') ? options.separator : '.');
		this.thousands 	= (this.decimals == '.' ? ',' : '.');
		this.digits 	= (options.hasOwnProperty('digits') ? options.digits : 2);

		arguments.callee.instance = this;
	}
	
	return arguments.callee.instance;
}

Currency.getInstance = function(symbol, options) {
	return new Currency(symbol, options);
};

Currency.prototype.format = function(price, dig) {
	if (dig === undefined) {
		dig = this.digits;
	}

	price = parseFloat(price).toFixed(dig);

	var _d = this.decimals;
	var _t = this.thousands;

	price = price.split('.');

	price[0] = price[0].replace(/./g, function(c, i, a) {
		return i > 0 && (a.length - i) % 3 === 0 ? _t + c : c;
	});

	if (price.length > 1) {
		price = price[0] + _d + price[1];
	} else {
		price = price[0];
	}
	

	if (this.position == 1) {
		return price + ' ' + this.symbol;
	}

	return this.symbol + ' ' + price;
}

/*
 * TIME
 */

function getFormattedTime(hour, min, format) {
	if (format == 'H:i') {
		return ((hour < 10) ? '0' : '') + hour + ':' + ((min < 10) ? '0' : '') + min;
	}
	
	var _th = (hour > 12 ? hour - 12 : hour);

	if (_th < 10) {
		_th = '0' + _th;
	}

	return _th + ':' + (min < 10 ? '0' : '') + min + (hour >= 12 ? ' PM' : ' AM');
}

/*
 * EMAIL
 */

function isEmailCompliant(email) {	
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}

/*
 * FORM VALIDATION
 */

function VikFormValidator(form, clazz) {
	this.form = form;

	if (typeof clazz === 'undefined') {
		clazz = 'invalid';
	}

	this.clazz  = clazz;
	this.labels = {};

	// prevent the form submission on enter keydown

	jQuery(this.form).on('keyup', function(e) {
		var keyCode = e.keyCode || e.which;
		
		if (keyCode === 13) { 
			e.preventDefault();
			return false;
		}
	});

	this.registerFields('.required');
}

VikFormValidator.prototype.isValid = function(input) {
	var val = jQuery(input).val();

	return val !== null && val.length > 0;
}

VikFormValidator.prototype.registerFields = function(selector) {

	var _this = this;

	jQuery(this.form).find(selector).on('blur', function() {
		if (_this.isValid(this)) {
			_this.unsetInvalid(this);
		} else {
			_this.setInvalid(this);
		}
	});

	return this;
}

VikFormValidator.prototype.unregisterFields = function(selector) {

	jQuery(this.form).find(selector).off('blur')

	return this;
}

VikFormValidator.prototype.validate = function(callback) {
	var ok = true;

	var _this = this;

	this.clearInvalidTabPane();

	jQuery(this.form).find('.required:input').each(function() {
		if (_this.isValid(this)) {
			_this.unsetInvalid(this);
		} else {
			_this.setInvalid(this);
			ok = false;

			if (!jQuery(this).is(':visible')) {
				// the input is probably hidden behind
				// an unactive tab pane
				_this.setInvalidTabPane(this);
			}
		}
	});

	if (typeof callback !== 'undefined') {
		ok = callback() && ok;
	}

	return ok;
}

VikFormValidator.prototype.setLabel = function(input, label) {
	this.labels[jQuery(input).attr('name')] = label;

	return this;
}

VikFormValidator.prototype.getLabel = function(input) {
	var name = jQuery(input).attr('name');	

	if (this.labels.hasOwnProperty(name)) {
		return jQuery(this.labels[name]);
	}

	return jQuery(input).closest('.controls').prev().find('b,label');
}

VikFormValidator.prototype.setInvalid = function(input) {
	jQuery(input).addClass(this.clazz);
	this.getLabel(input).addClass(this.clazz);

	return this;
}

VikFormValidator.prototype.unsetInvalid = function(input) {
	jQuery(input).removeClass(this.clazz);
	this.getLabel(input).removeClass(this.clazz);

	return this;
}

VikFormValidator.prototype.isInvalid = function(input) {
	return jQuery(input).hasClass(this.clazz);
}

VikFormValidator.prototype.clearInvalidTabPane = function() {
	jQuery('ul.nav-tabs li a').removeClass(this.clazz);

	return this;
}

VikFormValidator.prototype.setInvalidTabPane = function(input) {
	var pane = jQuery(input).closest('.tab-pane');

	if (pane.length) {
		var id 	 = jQuery(pane).attr('id');
		var link = jQuery('ul.nav-tabs li a[href="#' + id + '"]');

		if (link.length) {
			link.addClass(this.clazz);
		}
	}

	return this;
}

/*
 * FORM OBSERVER
 */

function VikFormObserver(form, types, skip) {

	if (typeof types === 'undefined') {
		types = ['hidden', 'input', 'textarea', 'select', 'file'];
	}

	if (typeof skip === 'undefined') {
		skip = [];
	}

	this.form  		= form;
	this.types 		= types;
	this.skipList 	= skip;
	this.custom		= {};
	this.cache 		= {};
	this.force 		= false;
}

VikFormObserver.prototype.freeze = function() {
	this.cache = this.map();

	return this;
}

VikFormObserver.prototype.isChanged = function() {
	if (this.force) {
		return true;
	}

	var map = this.map();

	var keys1 = Object.keys(this.cache);
	var keys2 = Object.keys(map);

	if (keys1.length != keys2.length) {
		return true;
	}

	for (var i = 0; i < keys1.length; i++) {
		
		if (!map.hasOwnProperty(keys1[i]) || map[keys1[i]] != this.cache[keys1[i]]) {
			return true;
		}

	}

	return false;
}

VikFormObserver.prototype.changed = function() {
	this.force = true;

	return this;
}

VikFormObserver.prototype.map = function() {

	var map = {};

	var _this = this;

	jQuery(this.form)
		.find(this.types.join(', '))
		.not(this.skipList.join(', '))
		.each(function() {

		var key = jQuery(this).attr('name');

		if (_this.custom.hasOwnProperty(key))
		{
			map[key] = _this.custom[key]();
		}
		else if (jQuery(this).is(':checkbox'))
		{
			map[key] = jQuery(this).is(':checked');
		}
		else
		{
			map[key] = jQuery(this).val();
		}

	});

	return map;
}

VikFormObserver.prototype.exclude = function(selector) {
	this.skipList.push(selector);

	return this;
}

VikFormObserver.prototype.push = function(selector) {
	this.types.push(selector);

	return this;
}

VikFormObserver.prototype.setCustom = function(selector, handler) {
	this.custom[jQuery(selector).attr('name')] = handler;

	return this;
}

/*
 * RENDERER
 */

function VikRenderer() {
	return this;
}

/**
 * To be rendered correctly, the select MUST own a unique ID.
 */
VikRenderer.chosen = function(selector) {

	// render select with chosen plugin
	jQuery(selector).find('select').chosen();

	// copy select classes into the chosen wrapper
	jQuery(selector).find('div[id$="_chzn"]').each(function() {
		// auto set default width
		jQuery(this).css('width', '200px');
		
		var select = jQuery(this).prev();

		jQuery(this).addClass(select.attr('class'));
	});

}

/**
 * Checks if the given box is currently visible within the monitor.
 *
 * @param 	object 	 box 	 The element to check.
 * @param 	integer  margin  An additional margin to use in order to ignore fixed elements.
 *
 * @return 	integer  The pixels to scroll if the box is not visible, otherwise false.
 */
function isBoxOutOfMonitor(box, margin) {
	var timeline_y 		 = box.offset().top;
	var scroll 			 = jQuery(window).scrollTop();
	var screen_height 	 = jQuery(window).height();
	var min_height_const = 150;

	if (margin === undefined) {
		margin = 0;
	}

	// check if we should scroll down
	if (timeline_y - scroll + min_height_const > screen_height) {
		return timeline_y - scroll + min_height_const - screen_height;
	}

	// check if we should scroll up
	if (scroll + margin > timeline_y + min_height_const) {
		return scroll + margin - timeline_y + min_height_const;
	}
	
	// the box is visible
	return false;
}

/*
 * CUSTOM CONFIRMATION DIALOG
 */

function VikConfirmDialog(message, id, clazz) {
	if (id === undefined) {
		id = 'vik-confirm-dialog';

		var cont = 1;
		while (jQuery('#' + id).length) {
			id += '-' + cont;
			cont++;
		}
	}

	if (clazz === undefined) {
		clazz = '';
	}

	this.message = message;
	this.buttons = [];
	this.id 	 = id;
	this.clazz   = 'vik-confirm-dialog' + (clazz.length ? ' ' + clazz : '');
	this.built   = false;
	this.args 	 = null;

	return this;
}

VikConfirmDialog.prototype.addButton = function(text, callback, dispose) {
	if (dispose === undefined) {
		dispose = true;
	}

	this.buttons.push({
		text: text,
		callback: callback,
		dispose: dispose
	});

	return this;
}

VikConfirmDialog.prototype.build = function() {
	if (!this.built) {

		var buttons = '';

		for (var i = 0; i < this.buttons.length; i++) {
			buttons += '<a data-index="'+i+'">'+this.buttons[i].text+'</a>';
		}

		var html = '<div id="'+this.id+'" class="'+this.clazz+'">\n'+
			'<div class="vik-confirm-message">'+this.message+'</div>\n'+
			'<div class="vik-confirm-buttons">'+buttons+'</div>\n'+
		'</div>';

		jQuery('body').append('<div class="vik-confirm-overlay">' + html + '</div>');

		var _this = this;

		jQuery('.vik-confirm-buttons a').on('click', function(event) {
			_this.triggerEvent(this, event);
		});

		this.built = true;
	}

	return this;
}

VikConfirmDialog.prototype.triggerEvent = function(btn, event) {
	var button = this.buttons[parseInt(jQuery(btn).data('index'))];

	if (button.callback) {
		button.callback(this.args, event);
	}

	if (button.dispose) {
		this.dispose();
	}
}

VikConfirmDialog.prototype.show = function(args) {
	this.build();

	this.args = args;

	jQuery('#' + this.id).parent().show();

	return this;
}

VikConfirmDialog.prototype.dispose = function() {
	jQuery('#' + this.id).parent().hide();

	return this;
}

/**
 * AJAX
 */

function doAjaxWithRetries(action, data, success, failure, attempt) {

	if (attempt === undefined) {
		attempt = 1;
	}

	return jQuery.ajax({
		type: 'post',
		url: action,
		data: data
	}).done(function(resp) {

		if (success !== undefined) {
			success(resp);
		}

	}).fail(function(err) {

		var AJAX_MAX_ATTEMPTS = 5;

		// If the error has been raised by a connection failure, 
		// retry automatically the same connection made. Do not retry
		// If the number of attempts is higher than the maximum number allowed.
		if (attempt < AJAX_MAX_ATTEMPTS && isConnectionLostError(err)) {

			// wait 128 milliseconds (plus random amount) before launching the request
			setTimeout(function() {
				// relaunch same action and increase number of attempts by 1
				doAjax(action, data, success, failure, attempt + 1);
			}, 128 + Math.floor(Math.random() * 128));

		} else {

			// otherwise raise the failure method
			if (failure !== undefined) {
				failure(err);
			}

		}

		console.log('failure', err);

		if (err.status == 500) {
			console.log(err.responseText);
		}

	});
}

function isConnectionLostError(err) {
	return (
		err.statusText == 'error'
		&& err.status == 0
		&& err.readyState == 0
		&& err.responseText == ''
	);
}

jQuery.parseJSON = function(data) {
	try {
		return JSON.parse(data);
	} catch (err) {
		console.log(err);
		console.log(data);
	}

	return null;
}
