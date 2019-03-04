/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of this code is crm-now.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Modified by crm-now GmbH, www.crm-now.com
 ************************************************************************************/
(function ($) {
	$.fn.scrollPagination = function (options) {
		var settings = {
			nop     : options.nop, // The number of posts per scroll to be loaded
			offset  : options.offset, // Initial offset, begins at 10 like in config
			error   : mobiscroll_arr.ALERT_POSTS, // When the user reaches the end this is the message that is displayed.
			delay   : 100, // When you scroll down the posts will load after a delayed amount of time. This is is mainly for usability concerns.
			scroll  : true // The main bit, if set to false posts will not load as the user scrolls. but will still load if the user clicks.
		};
		// Extend the options so they work with the plugin
		if (options) {
			$.extend(settings, options);
		}

		// For each so that we keep chainability.
		return this.each(function () {
			// Some variables
			var $this = $(this);
			var $settings = settings;
			var offset = $settings.offset;
			var module = $settings.module;
			var view = $settings.view;
			var viewName = $settings.viewName;
			var search = $settings.search;
			var busy = false; // Checks if the scroll action is happening so we don't run it multiple times

			// Custom messages based on settings
			var $initmessage = '';
			if ($settings.scroll == true) {
				$initmessage = mobiscroll_arr.ALERT_SCROLL;
			} else {
				$initmessage = mobiscroll_arr.ALERT_CLICK;
			}
			// Append custom messages and extra UI
			$this.append('<div class="content"></div><div class="loading-bar">'+$initmessage+'</div>');
			$this.find('.loading-bar').addClass('ui-bar ui-btn-active');

			function getData() {
				busy = true;
				// Post data to getScrollcontent.php
				$.post('?_operation=getScrollcontent', {
					action        : 'scrollpagination',
					number        : $settings.nop,
					offset        : offset,
					module        : module,
					view		  : view,
					viewName	  : viewName,
					src_str		  : search,
				}, function (data) {
					// Change loading bar content (it may have been altered)
					$this.find('.loading-bar').html($initmessage);

					// If there is no data returned, there are no more posts to be shown. Show error
					if (data == '') {
						$this.find('.loading-bar').html($settings.error);
					} else {
						// Offset increases
						offset = parseInt(offset)+parseInt($settings.nop);
						// Append the data to the content div
						$this.find('.content').append(data);
						// No longer busy!
						busy = false;
					}
				});
			}
			getData(); // Run function initially
			// If scrolling is enabled
			if ($settings.scroll == true) {
				// .. and the user is scrolling
				$(window).scroll(function () {
					// Check the user is at the bottom of the element
					if ($(window).scrollTop() + $(window).height() > $this.height() && !busy) {
						// Now we are working, so busy is true
						busy = true;
						// Tell the user we're loading posts
						$this.find('.loading-bar').html(mobiscroll_arr.ALERT_LOADING);
						// Run the function to fetch the data inside a delay
						// This is useful if you have content in a footer you
						// want the user to see.
						setTimeout(function () {
							getData();
						}, $settings.delay);
					}
				});
			}
			// Also content can be loaded by clicking the loading bar
			$this.find('.loading-bar').click(function () {
				if (busy == false) {
					busy = true;
					getData();
				}
			});
		});
	};
})(jQuery);