jQuery.fn.acritExpTabs = function (conf) {
	
	var config = jQuery.extend({
		selected:	1, 				// Which tab is initially selected (hash overrides this)
		selectedClass: 'selected',
		show:		'fadeIn', 		// Show animation
		hide:		'fadeOut', 		// Hide animation
		duration:	0,				// Animation duration
		before:	function () {},	// Callback before tab has been clicked,
		after:	function () {},	// Callback after tab has been clicked,
		use_hash: true
	}, conf);

	return this.each(function () {
	
		var ul = jQuery(this);
		ul.animating = false;
		var ipl	= 'a[href^="#"]';

		// Go through all the in-page links in the ul
		// and hide all but the selected's contents
		ul.find(ipl).each(function (i) {
			var link = jQuery(this);
			if ((i + 1) === config.selected) {
				link.addClass(config.selectedClass);
			} else {
				jQuery(link.attr('href')).hide();
			}
		});

		// When clicking the UL (or anything within)
		ul.click(function (e,x) {
			var clicked	= x ? x : jQuery(e.target);
			var link	= false;

			if (clicked.is(ipl)) {
				link = clicked;
			} else {
				var parent = clicked.parents(ipl);
				if (parent.length) {
					link = parent;
				}
			}
			
			if (!link || (link.hasClass(config.selectedClass) && $(link.attr('href')).is(':visible') )) return false;

			// Only continue if the clicked element was an in page link
			if (link) {
				var selected = ul.find('a.'+config.selectedClass);

				if (selected.length) {
					// Remove currently .selected, hide the element it was pointing to
					if (ul.animating!==true) {
						ul.animating = true;
						config.before(link, link.attr('href'), ul.find("a").index(link), false);
						jQuery(selected.removeClass(config.selectedClass).attr('href'))[config.hide](config.duration, function () {
							// Then show the element the clicked link was pointing to
							jQuery(link.attr('href'))[config.show](config.duration, function () {
								config.after(link, link.attr('href'), ul.find("a").index(link), false);
								ul.animating = false;
							});
						});
						link.addClass(config.selectedClass);
					}
				} else {
					jQuery(link.addClass(config.selectedClass).attr('href'))[config.show](config.duration, function () {
						config.after(link, link.attr('href'), ul.find("a").index(link), false);
					});
				}

				// Update the hash
				if (config.use_hash) {
					acritExpTabsUpdateHash(link.attr('href'));
				}

				return false;
			}
		});

		if (config.use_hash) {
			// If a hash is set, click that tab
			var hash = window.location.hash;
		}

		if (config.use_hash && hash) {
			if (jQuery(ul.find('a[href="' + hash + '"]')).size()>=1) {
				// Hide add
				ul.find('a').each(function(){
					jQuery($(this).attr("href")).hide();
				});
				// We can't simply .click() the link since that will run the show/hide animation
				jQuery(ul.find('a').removeClass(config.selectedClass).attr('href')).hide();
				jQuery(ul.find('a[href="' + hash + '"]').addClass(config.selectedClass).attr('href')).show();
				config.after(false, hash, config.selected, true);
			}
		}
	});
};

// http://stackoverflow.com/questions/1489624/modifying-document-location-hash-without-page-scrolling#answer-1489802
function acritExpTabsUpdateHash (hash) {
	hash = hash.replace( /^#/, '' );
	var fx, node = $( '#' + hash );
	if ( node.length ) {
		fx = $( '<div></div>' )
					.css({
							position:'fixed',
							left:0,
							top:0,
							visibility:'hidden'
					})
					.attr( 'id', hash )
					.appendTo( document.body );
		node.attr( 'id', '' );
	}
	document.location.hash = hash;
	if ( node.length ) {
		fx.remove();
		node.attr( 'id', hash );
	}
}
