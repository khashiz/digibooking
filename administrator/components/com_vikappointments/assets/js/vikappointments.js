/*
 * LEFTBOARD MENU
 */

jQuery(document).ready(function() {

	if (typeof VIKAPPOINTMENTS_MENU_INIT === 'undefined') {
		// avoid to re-init menu again
		VIKAPPOINTMENTS_MENU_INIT = true;

		if (isLeftBoardMenuCompressed()) {
			jQuery('.vap-leftboard-menu.compressed .parent .title.selected').removeClass('collapsed');
			jQuery('.vap-leftboard-menu.compressed .parent .wrapper.collapsed').removeClass('collapsed');
		}

		jQuery('#vap-main-menu .parent .title').disableSelection();

		jQuery('#vap-main-menu .parent .title').on('click', function() {
			leftBoardMenuItemClicked(this, 'click');
		});

		jQuery('#vap-main-menu .parent .title').hover(function() {
			if (isLeftBoardMenuCompressed() && !jQuery(this).hasClass('collapsed')) {
				leftBoardMenuItemClicked(this, 'hover');

				jQuery('#vap-main-menu.compressed .parent .title').removeClass('collapsed');
				jQuery(this).addClass('collapsed');
			}

			if (jQuery(this).hasClass('has-href') && jQuery(this).find('.wrapper').length) {
				leftBoardMenuItemClicked(this, 'hover');

				jQuery('#vap-main-menu .parent .title').removeClass('collapsed');
				jQuery(this).addClass('collapsed');
			}
		}, function() {
			if (jQuery(this).hasClass('has-href') && jQuery(this).find('.wrapper').length) {
				leftBoardMenuItemClicked(this, 'out');

				jQuery('#vap-main-menu .parent .title').removeClass('collapsed');
			}
		});
		
		jQuery('.vap-leftboard-menu').hover(function() {
			
		}, function() {
			jQuery('.vap-leftboard-menu.compressed .parent .title').removeClass('collapsed');
			jQuery('.vap-leftboard-menu.compressed .parent .wrapper').removeClass('collapsed');
		});

		jQuery('.vap-leftboard-menu .custom').hover(function() {
			jQuery('.vap-leftboard-menu.compressed .parent .title').removeClass('collapsed');
			jQuery('.vap-leftboard-menu.compressed .parent .wrapper').removeClass('collapsed');
		}, function() {

		});
	}

});

function leftBoardMenuItemClicked(elem, callee) {
	var wrapper = jQuery(elem).next('.wrapper');

	if (!wrapper.length) {
		// find wrapper within the container
		wrapper = jQuery(elem).find('.wrapper');
	}

	var has = !wrapper.hasClass('collapsed');

	if (has && callee == 'out')
	{
		// do not proceed as we are facing a loading delay,
		// because the 'hover' event wasn't yet ready
		return;
	}

	jQuery('#vap-main-menu .parent .wrapper').removeClass('collapsed');

	jQuery('.vap-angle-dir').removeClass('fa-angle-up');
	jQuery('.vap-angle-dir').addClass('fa-angle-down');
	
	if (has) {
		wrapper.addClass('collapsed');
		var angle = jQuery(elem).find('.vap-angle-dir');
		angle.addClass('fa-angle-up');
		angle.removeClass('fa-angle-down');
	}
}

function leftBoardMenuToggle() {

	// restore arrows
	jQuery('.vap-angle-dir').removeClass('fa-angle-up');
	jQuery('.vap-angle-dir').addClass('fa-angle-down');

	var status;

	if (isLeftBoardMenuCompressed()) {
		jQuery('.vap-leftboard-menu').removeClass('compressed');
		jQuery('.vap-task-wrapper').removeClass('extended');
		status = 1;
	} else {
		jQuery('.vap-leftboard-menu').addClass('compressed');
		jQuery('.vap-task-wrapper').addClass('extended');

		jQuery('.vap-leftboard-menu.compressed .parent .title.selected').removeClass('collapsed');
		jQuery('.vap-leftboard-menu.compressed .parent .wrapper.collapsed').removeClass('collapsed');

		status = 2;
	}

	leftBoardMenuRegisterStatus(status);
	jQuery(window).trigger('resize');

}

function leftBoardMenuRegisterStatus(status) {

	jQuery.noConflict();
		
	var jqxhr = jQuery.ajax({
		type: "POST",
		url: "index.php?option=com_vikappointments&task=store_mainmenu_status&tmpl=component",
		data: {status: status}
	}).done(function(resp){
		
	}).fail(function(resp){
		
	});

}

function isLeftBoardMenuCompressed() {
	return jQuery('.vap-leftboard-menu').hasClass('compressed');
}

/*
 * DOCUMENT CONTENT RESIZE
 */

jQuery(document).ready(function() {
	// statement to quickly disable doc resizing
	if (true) {
		var task     = jQuery('.vap-task-wrapper');
		var lfb_menu = jQuery('.vap-leftboard-menu');
		var _margin  = 20;

		jQuery(window).resize(function() {
			var p = (lfb_menu.width() + _margin) * 100 / jQuery(document).width();
			task.css('width', (100 - Math.ceil(p + 1)) + '%');
		});
	}

	jQuery(window).trigger('resize');
});

/*
 * OVERLAYS
 */

function openLoadingOverlay(lock, message) {
	var _html = '';

	if (message !== undefined) {
		_html += '<div class="vap-loading-box-message">' + message + '</div>';
	}

	jQuery('#content').append('<div class="vap-loading-overlay' + (lock ? ' lock' : '') + '">' + _html + '<div class="vap-loading-box"></div></div>');
}

function closeLoadingOverlay() {
	jQuery('.vap-loading-overlay').remove();
}

/*
 * SYSTEM UTILS
 */

jQuery.fn.updateChosen = function(value, active) {
	jQuery(this).val(value).trigger('chosen:updated').trigger('liszt:updated');

	if (active) {
		jQuery(this).next().addClass('active');
	} else {
		jQuery(this).next().removeClass('active');
	}
};

function debounce(func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) {
				func.apply(context, args);
			}
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) {
			func.apply(context, args);
		}
	};
};

function callbackOn(on, callback) {

	setTimeout(function() {

		if (on()) {
			callback();
		} else {
			callbackOn(on, callback);
		}

	}, 128 + Math.random() * 128);

}

function vapToggleSearchToolsButton(btn, suffix) {
	if (suffix === undefined) {
		suffix = '';
	} else {
		suffix = '-' + suffix;
	}

	if (jQuery(btn).hasClass('btn-primary')) {
		jQuery('#vap-search-tools' + suffix).slideUp();

		jQuery(btn).removeClass('btn-primary');
		
		jQuery('#vap-tools-caret' + suffix).removeClass('fa-caret-up').addClass('fa-caret-down');
	} else {
		jQuery('#vap-search-tools' + suffix).slideDown();

		jQuery(btn).addClass('btn-primary');

		jQuery('#vap-tools-caret' + suffix).removeClass('fa-caret-down').addClass('fa-caret-up');
	}
}

/*
 * SEARCH BAR - editconfig
 */

function SearchBar(matches) {
	this.setMatches(matches);
}

SearchBar.prototype.setMatches = function(matches) {
	this.matches = matches;
	this.currIndex = 0;
};

SearchBar.prototype.clear = function() {
	this.setMatches(false);
};

SearchBar.prototype.isNull = function() {
	return this.matches === false;
};

SearchBar.prototype.isEmpty = function() {
	return !this.isNull() && this.matches.length == 0;
};

SearchBar.prototype.getElement = function() {
	if (this.matches === false) {
		return null;
	}
	return this.matches[this.currIndex];
};

SearchBar.prototype.getCurrentIndex = function() {
	return this.currIndex;
};

SearchBar.prototype.size = function() {
	if (this.matches === false) {
		return 0;
	}
	return this.matches.length;
};

SearchBar.prototype.next = function() {
	if (this.matches === false) {
		return null;
	}
	this.currIndex++;
	if (this.currIndex >= this.matches.length) {
		this.currIndex = 0;
	}
	return this.matches[this.currIndex];
};

SearchBar.prototype.previous = function() {
	if (this.matches === false) {
		return null;
	}
	this.currIndex--;
	if (this.currIndex < 0) {
		this.currIndex = this.matches.length-1;
	}
	return this.matches[this.currIndex];
};
