$.fn.acritPopup = function(options, sender) {
	
	var defaults = {
		animation: 'fadeAndPop', //fade, fadeAndPop, none
		animationspeed: 150, //how fast animtions are
		closeonbackgroundclick: true, //if you click background will modal close?
		dismissmodalclass: 'close', //the class of a button or element that will close an open modal
		callbackshow: null, // callback on each open popup
		callbackclose: null // callback on each close popup
	}; 

	var options = $.extend({}, defaults, options);

	return this.each(function() {
		
		var modal = $(this);
		
		if(this.modalTop == undefined) {
			this.modalTop = !isNaN(parseInt(modal.css('top'))) ? parseInt(modal.css('top')) : 0;
		}

		var top = this.modalTop,
			locked = false,
			modalBG = modal.next('.acrit-modal-bg');
		
		top_real = top + $(document).scrollTop();

		if(modalBG.length == 0) {
			modalBG = $('<div/>').addClass('acrit-modal-bg').insertAfter(modal);
		}

		// Entrance Animations
		modal.bind('acrit:open', function () {
			modalBG.unbind('click.modalAcritEvent');
			$('.' + options.dismissmodalclass).unbind('click.modalAcritEvent');
			if(!locked) {
				lockModal();
				if(options.animation == 'fadeAndPop') {
					modal.css({'top': $(document).scrollTop(), 'opacity' : 0, 'visibility' : 'visible', 'margin-left' : -1 * modal.width() / 2});
					modalBG.fadeIn(options.animationspeed/2);
					modal.delay(options.animationspeed/2).animate({
						'top': top_real,
						'opacity' : 1
					}, options.animationspeed,unlockModal());					
				}
				if(options.animation == 'fade') {
					modal.css({'opacity' : 0, 'visibility' : 'visible', 'top': top_real, 'margin-left' : -1 * modal.width() / 2});
					modalBG.fadeIn(options.animationspeed/2);
					modal.delay(options.animationspeed/2).animate({
						'opacity' : 1
					}, options.animationspeed,unlockModal());					
				} 
				if(options.animation == 'none') {
					modal.css({'visibility' : 'visible', 'top' : top_real});
					modalBG.css({'display':'block'});	
					unlockModal()				
				}
				if(options.callbackshow && typeof window[options.callbackshow] == 'function') {
					window[options.callbackshow](modal, modal.find('.content').first(), modal.find('.buttons').first(), modal.find('.data').first(), sender);
				}
				if(modal.data('callbackshow') && typeof window[modal.data('callbackshow')] == 'function'){
					window[modal.data('callbackshow')](modal, modal.find('.content').first(), modal.find('.buttons').first(), modal.find('.data').first(), sender);
				}
			}
			modal.unbind('acrit:open');
		}); 	

		// Closing Animation
		modal.bind('acrit:close', function () {
			if(!locked) {
				lockModal();
				if(options.animation == 'fadeAndPop') {
					modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
					modal.animate({
						'top':  $(document).scrollTop(),
						'opacity' : 0
					}, options.animationspeed/2, function() {
						modal.css({'top' : top_real, 'opacity' : 1, 'visibility' : 'hidden'});
						unlockModal();
					});					
				}  	
				if(options.animation == 'fade') {
					modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
					modal.animate({
						'opacity' : 0
					}, options.animationspeed, function() {
						modal.css({'top': top_real, 'opacity' : 1, 'visibility' : 'hidden'});
						unlockModal();
					});					
				}  	
				if(options.animation == 'none') {
					modal.css({'visibility' : 'hidden'});
					modalBG.css({'top': top_real, 'display' : 'none'});	
				}
				if(options.callbackclose && typeof window[options.callbackclose] == 'function') {
					window[options.callbackclose](modal, modal.find('.content').first(), modal.find('.buttons').first(), modal.find('.data').first(), sender);
				}
				if(modal.data('callbackclose') && typeof window[modal.data('callbackclose')] == 'function'){
					window[modal.data('callbackclose')](modal, modal.find('.content').first(), modal.find('.buttons').first(), modal.find('.data').first(), sender);
				}
			}
			modal.unbind('acrit:close');
		});     

		// Set vertical align
		modal.bind('acrit:align', function () {
			modal.delay(options.animationspeed/2).animate({
				'top': ($(document).scrollTop() + ($(window).height() - modal.height()) / 2),
				'opacity' : 1
			}, 0, unlockModal());		
		});

		//Open Modal Immediately
		modal.trigger('acrit:open');

		//Close Modal Listeners
		var closeButton = $('.' + options.dismissmodalclass).bind('click.modalAcritEvent', function () {
			modal.trigger('acrit:close')
		});

		if(options.closeonbackgroundclick) {
			modalBG.css({'cursor':'pointer'})
			modalBG.bind('click.modalAcritEvent', function () {
			modal.trigger('acrit:close')
			});
		}
		$('body').keyup(function(e) {
			if(e.which===27){ modal.trigger('acrit:close'); } // 27 is the keycode for the Escape key
		});

		function unlockModal() { 
			locked = false;
		}
		
		function lockModal() {
			locked = true;
		}
	
	});
}

/* Popups */
$(document).delegate('[data-acrit-modal]', 'click', function(e){
	e.preventDefault();
	var modalLocation = $(this).attr('data-acrit-modal');
	$('#'+modalLocation).acritPopup($(this).data(), $(this));
});
