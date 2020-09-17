$.fn.acritTeacher = function(options){
	
	let
		This = $(this),
		defaultOptions,
		animateTime = 100,
		scrollTime = 350,
		strokeMargin = 5,
		currStep = 1,
		steps = {},
		overlayBg,
		overlayData,
		overlayControlsTop,
		overlayControlsBottom,
		strokes,
		animating = false,
		splashScreen,
		stepIndex = 1,
		handlers = {},
		handlerIndex = 0,
		closing = false,
		goingPrev = false,
		goingNext = false;
	
	if(typeof options == 'string'){
		return this;
	}
	
	// Options
	defaultOptions = {
		debug: false,
		testOption: true,
		labels: {
			title: '',
			start: 'Start',
			next: 'Next',
			finish: 'Finish',
			close: 'Close',
			confirmExit: 'Really close?',
			descriptionToggle: 'More'
		},
		splashScreen: false,
		callbackStart: function(){},
		callbackFinish: function(){},
		callbackClose: function(){},
		steps: [],
		animateTime: animateTime
	}
	options = $.extend({}, defaultOptions, options);
	splashScreen = typeof options.splashScreen == 'object';
	if(splashScreen){
		currStep = 0;
		steps[currStep] = {};
	}
	for(let i in options.steps){
		steps[stepIndex++] = options.steps[i];
	}
	this.steps = steps;
	
	
	// 1. PREPARE
	
	if(typeof overlayBg == 'undefined'){
		overlayBg = $('<div class="acrit_teacher_overlay_bg" />').hide().fadeOut(0).appendTo($('body'));
	}
	
	if(typeof overlayData == 'undefined'){
		overlayData = $('<div class="acrit_teacher_overlay_data"/>').hide().fadeOut(0).appendTo($('body'));
		overlayData.append($('<div class="acrit_teacher_content_wrapper"/>')
			.append('<div class="acrit_teacher_content"><table><tbody><tr><td></td></tr></tbody></table></div>'));
	}
	
	if(typeof overlayControlsTop == 'undefined'){
		overlayControlsTop = $('<div class="acrit_teacher_overlay_controls_top"/>').hide().fadeOut(0)
			.appendTo($('body'));
		overlayControlsTop.append($('<div class="acrit_teacher_progress"/>'));
		overlayControlsTop.append($('<div class="acrit_teacher_overlay_title"/>'));
		overlayControlsTop.append($('<div class="acrit_teacher_close" title="'+options.labels.close+'">&times;</div>'));
	}
	
	if(typeof overlayControlsBottom == 'undefined'){
		overlayControlsBottom = $('<div class="acrit_teacher_overlay_controls_bottom"/>').hide().fadeOut(0)
			.appendTo($('body'));
		overlayControlsBottom.append($('<div class="acrit_teacher_step_data"/>')
			.append($('<div class="acrit_teacher_step_css_styles"/>'))
			.append($('<div class="acrit_teacher_step_title"/>'))
			.append($('<div class="acrit_teacher_step_description"/>'))
			.append($('<div class="acrit_teacher_step_description_toggle"/>')
				.append('<a>'+options.labels.descriptionToggle+'</a>'))
		);
		overlayControlsBottom.append($('<div class="acrit_teacher_buttons">')
			.append($('<div class="acrit_teacher_button acrit_teacher_button_prev">')
				.append($('<input type="button">').val(options.labels.prev).hide()))
			.append($('<div class="acrit_teacher_button acrit_teacher_button_next">')
				.append($('<input type="button">').val(options.labels.start).hide()))
		);
		//
		$('.acrit_teacher_step_description_toggle a', overlayControlsBottom).bind('click', function(e){
			e.preventDefault();
			let divDescription = $(this).parent().parent().find('.acrit_teacher_step_description');
			if(!divDescription.is(':animated')){
				divDescription.slideToggle();
			}
		});
	}
	
	if(typeof strokes == 'undefined'){
		strokes = [];
	}
	
	
	// 2. INTERNAL METHODS
	
	this.log = function(){
		if(options.debug || window.acritTeacherDebug === true){
			console.log.apply(this, arguments);
		}
	}
	
	// Start current teacher
	this.teacherStart = function(){
		let canStart = true;
		if(this.isRestricted()){
			canStart = false;
		}
		this.log('callbackStart');
		if(typeof options.callbackStart == 'function'){
			if($.proxy(options.callbackStart, this)(options) === false){
				canStart = false;
			}
		}
		if(canStart){
			this.addEvents();
			this.fixTabControl(false);
			this.htmlOverflowEnable(false);
			this.restrictOtherTeachers(true);
			this.showOverlayBg(true);
			this.showOverlayData(true);
			this.showOverlayControls(true);
			this.buildSplashScreen();
			this.repaintStrokesOnAction();
			this.repaintStrokesByInterval();
			this.displayProgress();
		}
	}
	
	// Close current teacher
	this.teacherClose = function(force){
		let canClose = true;
		if(!force && options.labels.confirmExit != undefined && options.labels.confirmExit.length){
			if(!confirm(options.labels.confirmExit)){
				canClose = false;
			}
		}
		if(canClose){
			this.closing = true;
			this.showOverlayBg(false);
			this.showOverlayData(false);
			this.showOverlayControls(false);
			this.clearAllData();
			this.htmlOverflowEnable(true, animateTime);
			this.restrictOtherTeachers(false, animateTime);
			this.executeCurrentStepOut();
			this.fixTabControl(true);
			this.log('callbackClose');
			if(typeof options.callbackClose == 'function'){
				$.proxy(options.callbackClose, this)(options);
			}
			setTimeout($.proxy(function(){
				delete window.acritTeacher;
			}, this), animateTime);
			this.removeEvents();
			this.closeBxPopups();
		}
	}
	
	// Restrict more than one teacher
	this.restrictOtherTeachers = function(flag, delay){
		delay = parseInt(delay);
		if(isNaN(delay) || delay < 0){
			delay = 0;
		}
		setTimeout($.proxy(function(){
			if(flag){
				window.acritTeacher = this;
			}
			else{
				delete window.acritTeacher;
			}
		}, this), delay);
	}
	
	// Is restricted by other?
	this.isRestricted = function(){
		return !!window.acritTeacher;
	}
	
	// Add event handlers
	this.addEvents = function(){
		
		// Repaint strokes on popup close
		this.eventBxRepaintStrokes = $.proxy(function(popup){
			this.repaintStrokesDelay(10);
		}, this);
		BX.addCustomEvent(window, 'onWindowClose', this.eventBxRepaintStrokes);
		
		// Repaint strokes on window resize
		this.eventWindowResize = $.proxy(function(){
			this.repaintStrokes();
		}, this);
		$(window).on('resize', this.eventWindowResize);
		
		// Handler mouseup for repaint strokes with delay
		this.eventBodyMouseUp = $.proxy(function(e){
			this.repaintStrokesDelay(10);
		}, this);
		$('body').on('mouseup', this.eventBodyMouseUp);
		
		// Handler for click Prev
		this.eventPrevClick = $.proxy(function(e){
			if(this.isVisible()){
				this.goNext(true);
			}
		}, this);
		$('body').on('click', '.acrit_teacher_button_prev input[type="button"]', this.eventPrevClick);
		
		// Handler for click Next
		this.eventNextClick = $.proxy(function(e){
			if(this.isVisible()){
				if(e.altKey){
					this.goToStep(prompt('Select step index, min=1, max=' + options.steps.length, ''));
				}
				else{
					this.goNext(false);
				}
			}
		}, this);
		$('body').on('click', '.acrit_teacher_button_next input[type="button"]', this.eventNextClick);
		
		// Handler for click Close
		this.eventCloseClick = $.proxy(function(e){
			this.teacherClose();
		}, this);
		$('body').on('click', '.acrit_teacher_close', this.eventCloseClick);
		
		// Handler for press Esc key
		this.eventEscPress = $.proxy(function(e){
			if(overlayData.is(':visible') && e.keyCode == 27){
				if(!this.checkAnyBxPopupOpen()){
					this.teacherClose();
				}
				else{
					this.repaintStrokesDelay(10);
				}
			}
		}, this);
		$('body').on('keydown', this.eventEscPress);
	}
	
	// Remove event handlers
	this.removeEvents = function(){
		BX.removeCustomEvent(window, 'onWindowClose', this.eventBxRepaintStrokes);
		$(window).off('resize', this.eventWindowResize);
		$('body').off('mouseup', this.eventBodyMouseUp);
		$('body').off('click', '.acrit_teacher_button_prev input[type="button"]', this.eventPrevClick);
		$('body').off('click', '.acrit_teacher_button_next input[type="button"]', this.eventNextClick);
		$('body').off('click', '.acrit_teacher_close', this.eventCloseClick);
		$('body').off('keydown', this.eventEscPress);
	}
	
	// Fix TabControl panels (top && bottom)
	this.fixTabControl = function(flag){
		this.log('fixTabControl: ', flag ? 'Y' : 'N');
		if(typeof options.tabControlName == 'string'){
			options.tabControl = window[options.tabControlName];
			if(options.tabControl){
				if(options.tabControl.acritTeacherFixedTop == undefined){
					options.tabControl.acritTeacherFixedTop = options.tabControl.bFixed.top;
				}
				if(options.tabControl.acritTeacherFixedBottom == undefined){
					options.tabControl.acritTeacherFixedBottom = options.tabControl.bFixed.bottom;
				}
				if(flag){
					if(options.tabControl.acritTeacherFixedTop){
						options.tabControl.ToggleFix('top', options.tabControl.acritTeacherFixedTop);
					}
					if(options.tabControl.acritTeacherFixedBottom){
						options.tabControl.ToggleFix('bottom', options.tabControl.acritTeacherFixedBottom);
					}
				}
				else{
					if(options.tabControl.acritTeacherFixedTop){
						options.tabControl.ToggleFix('top', false);
					}
					if(options.tabControl.acritTeacherFixedBottom){
						options.tabControl.ToggleFix('bottom', false);
					}
				}
			}
		}
	}
	
	// Close all BX-popups (BX.CDialog)
	this.closeBxPopups = function(){
		if(this.checkAnyBxPopupOpen()){
			for(let i in window){
				if(window[i] instanceof BX.CDialog){
					window[i].Close();
				}
			}
		}
	}
	
	// Check if at least 1 popup is open
	this.checkAnyBxPopupOpen = function(){
		let result = false;
		for(let i in window){
			if(window[i] instanceof BX.CDialog && window[i].isOpen){
				result = true;
				break;
			}
		}
		return result;
	}
	
	// Restrict page scrolling
	this.htmlOverflowEnable = function(flag, delay){
		delay = parseInt(delay);
		if(isNaN(delay) || delay < 0){
			delay = 0;
		}
		setTimeout($.proxy(function(){
			$('html').toggleClass('acrit_teacher_html', !flag);
		}, this), delay);
	}
	
	// Show/hide background overlay
	this.showOverlayBg = function(flag){
		if(flag){
			overlayBg.fadeIn(animateTime);
		}
		else{
			overlayBg.fadeOut(animateTime);
		}
	}
	
	// Show/hide data overlay
	this.showOverlayData = function(flag){
		if(flag){
			overlayData.fadeIn(animateTime);
		}
		else{
			overlayData.fadeOut(animateTime);
		}
	}
	
	// Show/hide controls overlay
	this.showOverlayControls = function(flag){
		if(flag){
			overlayControlsTop.fadeIn(animateTime);
			overlayControlsBottom.fadeIn(animateTime);
		}
		else{
			overlayControlsTop.fadeOut(animateTime);
			overlayControlsBottom.fadeOut(animateTime);
		}
	}
	
	// Show/hide stroke
	this.strokeElement = function(elements, flag, accessible){
		let
			strokeId,
			stroke,
			selector = $(elements).selector;
		elements = $(elements).get();
		for(let i in elements){
			if(flag === true){
				strokeId = $.trim(Math.random()).substr(2);
				$(elements[i]).attr('data-stroke-id', strokeId);
				elementAccessible = !!flag && (!!accessible || !!elements[i].acritTeacherAccessible);
				stroke = $('<div>').hide().fadeOut(0).addClass('acrit_teacher_stroke').appendTo($('body'))
					.attr({
						'data-stroke-id': strokeId,
						'data-control-accessible': elementAccessible ? 'Y' : 'N',
						'data-selector': selector
					}).fadeIn(animateTime);
				strokes.push({
					item: elements[i],
					stroke: stroke
				});
			}
			else{
				strokeId = $(elements[i]).attr('data-stroke-id');
				for(let i in strokes){
					if($(strokes[i].item).attr('data-stroke-id') == strokeId){
						this.removeStroke(strokes[i]);
						delete strokes[i];
					}
				}
			}
		}
		this.repaintStrokes();
	}
	
	// Remove all strokes
	this.removeStroke = function(strokeItem){
		if(strokeItem){
			let
				item = strokeItem.item,
				stroke = strokeItem.stroke;
			$(item).removeAttr('data-stroke-id')
				.removeClass('acrit_teacher_control_highlighted')
				.removeClass('acrit_teacher_control_highlighted_relative')
				.removeClass('acrit_teacher_control_accessible');
			$(stroke).fadeOut(animateTime, function(){
				$(this).remove();
			});
		}
	}
	
	// Remove all strokes
	this.removeAllStrokes = function(){
		let strokes_old = strokes;
		for(let i in strokes){
			this.removeStroke(strokes[i]);
			delete strokes[i];
		}
	}
	
	// Replace stroke
	this.repaintStrokes = function(){
		let
			offset,
			item,
			stroke;
		for(let i in strokes){
			item = $(strokes[i].item);
			stroke = $(strokes[i].stroke);
			offset = item.offset();
			stroke.css({
				top: offset.top - strokeMargin,
				left: offset.left - strokeMargin,
				width: item.outerWidth() + 2 * strokeMargin,
				height: item.outerHeight() + 2 * strokeMargin,
			});
			item.addClass('acrit_teacher_control_highlighted');
			if(item.css('position') == 'static'){
				item.addClass('acrit_teacher_control_highlighted_relative');
			}
			if(stroke.attr('data-control-accessible') == 'Y'){
				item.addClass('acrit_teacher_control_accessible');
			}
			if(item.is(':visible')){
				stroke.show();
			}
			else{
				stroke.hide();
			}
		}
	}
	
	// Replace stroke with delay
	this.repaintStrokesDelay = function(delay){
		if(!overlayData.is(':visible')){
			return;
		}
		if(!delay){
			delay = 50;
		}
		setTimeout($.proxy(function(){
			this.repaintStrokes();
		}, this), delay);
	}
	
	// Replace stroke on action
	this.repaintStrokesOnAction = function(){
		let elements = overlayBg.add(overlayData).add(overlayControlsTop).add(overlayControlsBottom);
		elements.on('mousedown', $.proxy(function(){
			setTimeout($.proxy(function(){
				this.repaintStrokesDelay();
			}, this), 50);
		}, this));
	}
	
	// Replace stroke by interval
	this.repaintStrokesByInterval = function(){
		setInterval($.proxy(function(){
			this.repaintStrokes();
		}, this), 250);
	}
	
	// Clear all data (on exit)
	this.clearAllData = function(){
		let elements = overlayBg.add(overlayData).add(overlayControlsTop).add(overlayControlsBottom);
		elements.fadeOut(animateTime, function(){
			$(this).remove();
		});
		this.removeAllStrokes();
		this.removeAllHandlers();
	}
	
	// Set title (on top controls overlay)
	this.setTitle = function(title){
		$('.acrit_teacher_overlay_title', overlayControlsTop).html(title);
	}
	
	// Set content (on data overlay)
	this.setContent = function(content){
		$('.acrit_teacher_content > table > tbody > tr > td', overlayData).html(content);
	}
	
	// Set splash screen content
	this.buildSplashScreen = function(){
		if(splashScreen){
			let divContent = $('.acrit_teacher_content > table > tbody > tr > td', overlayData);
			if(options.splashScreen.html != undefined){
				divContent.html(options.splashScreen.html);
			}
			else if (options.splashScreen.description != undefined){
				let divSplashScreen = $('<div class="acrit_teacher_splash_screen" />'),
					divSplashContent = $('<div class="acrit_teacher_splash_screen_content" />').appendTo(divSplashScreen);
				$('<div class="acrit_teacher_splash_screen_title" />').html(options.labels.title).appendTo(divSplashContent);
				$('<div class="acrit_teacher_splash_screen_description" />').html(options.splashScreen.description)
					.appendTo(divSplashContent);
				$('<div class="acrit_teacher_button acrit_teacher_button_next" />').appendTo(divSplashScreen)
					.append($('<input type="button">').attr('value', options.labels.start));
				if(options.splashScreen.cssStyles != undefined){
					$('<style>'+options.splashScreen.cssStyles+'</style>').appendTo(divSplashScreen);
				}
				divSplashScreen.appendTo(divContent);
			}
			else{
				$('.acrit_teacher_button.acrit_teacher_button_next input[type="button"]', overlayControlsBottom).show()
					.trigger('click');
			}
		}
		else{
			this.skipSplashScreen();
		}
	}
	
	// Skip splash screen
	this.skipSplashScreen = function(){
		let animateTimeOriginal = animateTime;
		animateTime = 0;
		this.goNext(false);
		animateTime = animateTimeOriginal;
	}
	
	// Go next step
	this.goNext = function(prev){
		if(!overlayData.is(':visible')){
			return;
		}
		if(animating){
			return;
		}
		this.log('');
		this.log('[GO NEXT]');
		//
		this.goingPrev = prev === true;
		this.goingNext = prev !== true;
		//
		let
			btnPrev = $('.acrit_teacher_button_prev input[type="button"]', overlayControlsBottom),
			btnNext = $('.acrit_teacher_button_next input[type="button"]', overlayControlsBottom),
			currStepData = steps[currStep] ? steps[currStep] : {},
			stepDelta = prev === true ? -1 : 1,
			currStepElements = currStepData.elements,
			currStepElementsCallback = currStepData.callbackElements,
			canGoNext = true,
			strokeHideTimeout = 0,
			nextStep,
			nextStepData,
			nextIsLast,
			nextIsSplashScreen,
			isFinish = btnNext.attr('data-finish') == 'Y';
		// Skip some steps by callback
		while(true){
			nextStep = currStep + stepDelta;
			nextStepData = steps[nextStep];
			if(nextStepData){
				if(typeof nextStepData.callbackSkip == 'function'){
					if($.proxy(nextStepData.callbackSkip, this)(options, nextStepData) === true){
						currStep += stepDelta;
						continue;
					}
				}
				nextIsLast = steps[nextStep + 1] == undefined;
				nextIsSplashScreen = nextStep == 0;
				break;
			}
			isFinish = true;
			break;
		}
		//
		this.log('callbackOut #'+currStep);
		if(typeof currStepData.callbackOut == 'function'){
			if($.proxy(currStepData.callbackOut, this)(options, currStepData, nextStepData) === false){
				canGoNext = false;
			}
		}
		this.log('callbackIn #'+nextStep);
		if(canGoNext && nextStepData){
			if(typeof nextStepData.callbackIn == 'function'){
				if($.proxy(nextStepData.callbackIn, this)(options, currStepData, nextStepData) === false){
					canGoNext = false;
				}
			}
		}
		if(canGoNext && prev && nextIsSplashScreen){
			canGoNext = false;
		}
		if(canGoNext && this.closing){
			canGoNext = false;
		}
		if(canGoNext){
			animating = true;
			// Set buttons
			if(nextStep > 1){
				btnPrev.fadeIn(animateTime);
			}
			else{
				btnPrev.fadeOut(animateTime);
			}
			btnNext.show();
			// Set title && content
			this.setTitle(options.labels.title);
			this.setContent(false);
			// Remove current strokes
			this.removeAllStrokes();
			// Hide prev step data
			if(typeof currStepElementsCallback == 'function'){
				currStepElements = $.proxy(currStepElementsCallback, this)(options);
			}
			this.displayTitle(false, false);
			//
			if(currStepElements && currStepElements.length) {
				strokeHideTimeout = animateTime;
				this.strokeElement(currStepElements, false);
			}
			//
			setTimeout($.proxy(function(){
				//
				animating = false;
				// Process function
				let
					interval,
					intervalBeforeFunction = $.proxy(function(){
						animating = true;
						this.log('callbackBeforeRepeat #'+nextStep);
						if($.proxy(nextStepData.callbackBefore, this)(options, nextStepData) !== false){
							animating = false;
							doGoNext();
						}
					}, this),
					doGoNext = $.proxy(function(){
						clearInterval(interval);
						currStep += stepDelta;
						this.currStep = currStep;
						this.log('doGoNext #'+nextStep);
						// Center viewport and display data
						this.centerViewportAndDisplayData(nextStepData, nextStep);
						// Css
						$('.acrit_teacher_step_css_styles', overlayControlsBottom).html('');
						if(typeof nextStepData.cssStyles != 'undefined'){
							$('.acrit_teacher_step_css_styles', overlayControlsBottom)
								.html('<style>'+nextStepData.cssStyles+'</style>');
						}
						// Set button finish flag
						if(nextIsLast){
							btnNext.val(options.labels.finish).attr('data-finish', 'Y');
						}
						else{
							btnNext.val(options.labels.next);
						}
						// Show/hide button
						let buttonVisible = 'Y';
						if(nextStepData.buttonVisible != undefined){
							buttonVisible = nextStepData.buttonVisible == 'N' ? 'N' : 'Y';
						}
						if(typeof nextStepData.callbackButtonVisible == 'function'){
							buttonVisible = $.proxy(nextStepData.callbackButtonVisible, this)(options, nextStepData) == 'N' 
								? 'N' : 'Y';
						}
						btnNext.css('visibility', buttonVisible == 'N' ? 'hidden' : '');
						// Display progress
						this.displayProgress();
					}, this)
				//
				if(nextStepData){
					// Callback before
					this.log('callbackBefore #'+nextStep);
					if(typeof nextStepData.callbackBefore == 'function'){
						interval = setInterval(intervalBeforeFunction, 50);
						intervalBeforeFunction();
					}
					else{
						doGoNext();
					}
				}
				else if(isFinish){
					let canClose = true;
					this.log('callbackFinish');
					if(typeof options.callbackFinish == 'function'){
						if($.proxy(options.callbackFinish, this)(options, currStepData, nextStepData) === false){
							canClose = false;
						}
					}
					if(canClose){
						this.teacherClose(true);
					}
				}
			}, this), animateTime);
		}
	}
	
	// Same as this.goNext, but with delay
	this.goNextDelay = function(delay){
		animating = true;
		setTimeout($.proxy(function(){
			animating = false;
			this.goNext(false);
		}, this), delay);
	}
	
	// Center viewport by elements and display data
	this.centerViewportAndDisplayData = function(stepData, stepIndex){
		this.log(stepData);
		let
			elements = stepData.elements,
			callbackElements = stepData.callbackElements,
			scrollTop = stepData.scrollTop,
			timeoutDelay = animateTime,
			title = stepData.title;
		// Simulate click on needed tab / subtab
		if(typeof stepData.tabId == 'string'){
			let tab = $('#tab_cont_'+stepData.tabId),
				divTabControl = tab.closest('.adm-detail-block'),
				tabControlName = divTabControl.attr('id').replace(/_layout$/, ''),
				tabControl = window[tabControlName];
			if(tabControl && !tabControl.bExpandTabs) {
				tab.trigger('click');
			}
		}
		if(typeof stepData.subTabId == 'string'){
			let subtab = $('#view_tab_'+stepData.subTabId);
			subtab.trigger('click');
		}
		setTimeout($.proxy(function(){
			// Prepare elements
			if(typeof callbackElements == 'function'){
				elements = $.proxy(callbackElements, this)(options, stepData);
			}
			// Prepare scroll top
			if(typeof scrollTop == 'function'){
				scrollTop = $.proxy(scrollTop, this)(options, stepData);
			}
			scrollTop = this.getElementsViewportScrollTop(elements, scrollTop);
			// Center viewport and display data
			if(scrollTop !== false && scrollTop != $(document).scrollTop()){
				$('html, body').animate({scrollTop: scrollTop}, scrollTime);
				timeoutDelay += scrollTime;
			}
			//
			animating = true;
			setTimeout($.proxy(function(){
				this.strokeElement(elements, true, stepData.accessible === true || stepData.accessible == 'Y');
				this.displayTitle(stepData.title, stepData.description);
				setTimeout($.proxy(function(){
					animating = false;
				}, this), animateTime);
				// Callback after
				this.log('callbackAfter #'+stepIndex);
				if(typeof stepData.callbackAfter == 'function'){
					$.proxy(stepData.callbackAfter, this)(options, stepData);
				}
			}, this), timeoutDelay);
		}, this), 10);
	}
	
	// Diplay progress
	this.displayProgress = function(){
		let
			progress = options.steps.length ? (currStep / options.steps.length) * 100 : 0,
			divProgress = $('.acrit_teacher_progress', overlayControlsTop).css('width', progress + '%');
		divProgress.toggleClass('acrit_teacher_progress_full', currStep == options.steps.length && currStep > 0);
	}
	
	// Display step title
	this.displayTitle = function(title, description){
		// Prepare title
		if(typeof title == 'function'){
			title = $.proxy(title, this)(options);
		}
		// Prepare description
		if(typeof description == 'function'){
			description = $.proxy(description, this)(options);
		}
		//
		$('.acrit_teacher_step_data', overlayControlsBottom).fadeOut(25, function(){
			$('.acrit_teacher_step_title', this).html(title);
			$('.acrit_teacher_step_description', this).html(description).hide();
			$('.acrit_teacher_step_description_toggle', this).toggle(typeof description == 'string');
			$(this).fadeIn(25);
		});
	}
	
	// Get value for scrollTop on centering viewport
	this.getElementsViewportScrollTop = function(elements, scrollTop){
		let result = false;
		if(scrollTop != undefined){
			scrollTop = parseInt(scrollTop);
		}
		if(isNaN(scrollTop)){
			if(elements && elements instanceof jQuery){
				elements = elements.filter(':visible');
				if(elements.length){
					let
						valuesMin = elements.get().map(function(element){
							return $(element).offset().top;
						}),
						valuesMax = elements.get().map(function(element){
							return $(element).offset().top + $(element).outerHeight();
						}),
						topMin = Math.min.apply(Math, valuesMin),
						topMax = Math.max.apply(Math, valuesMax),
						topMiddle = topMin + (topMax - topMin) / 2;
					result = topMiddle - ($(window).height() / 2);
					if(topMax - topMin > $('.acrit_teacher_content', overlayData).height() - 4 * strokeMargin){ // If block is too big by height
						result = topMin - 2 * strokeMargin - $('.acrit_teacher_overlay_title', overlayControlsTop).outerHeight();
					}
					if(result < 0){
						result = 60;
					}
				}
			}
		}
		else{
			result = scrollTop;
		}
		return result;
	}
	
	// Sleep
	this.sleep = function(milliseconds, callback){
		const date = Date.now();
		let currentDate = null;
		do {
			currentDate = Date.now();
		}
		while (currentDate - date < milliseconds);
		if(typeof callback == 'function'){
			callback();
		}
	}
	
	// Add handler
	this.addHandler = function(element, event, callback){
		callbackProxy = $.proxy(callback, this);
		// jQuery handler
		if(element instanceof jQuery){
			$(element).each(function(){
				handlers[++handlerIndex] = {
					element: this,
					event: event,
					callback: callback
				};
			});
			element.on(event, callbackProxy);
			this.log('Added handler');
		}
		// BX handler
		else{
			handlers[++handlerIndex] = {
				element: element,
				event: event,
				callback: callback
			};
			BX.addCustomEvent(element, event, callbackProxy);
			this.log('Added BX handler');
		}
		return callback;
	}
	
	// Remove handler
	this.removeHandler = function(element, event, callback){
		let elem, This = this;
		callbackProxy = $.proxy(callback, this);
		// jQuery handler
		if(element instanceof jQuery){
			for(let i in handlers){
				$(element).each(function(){
					elementThis = this;
					if(handlers[i] != undefined) {
						$(handlers[i].element).each(function(){
							if(this == elementThis){
								if(handlers[i].event == event && handlers[i].callback == callback){
									$(elementThis).off(event, callbackProxy);
									delete handlers[i];
									This.log('Removed handler');
								}
							}
						});
					}
				});
			}
		}
		// BX handler
		else{
			for(let i in handlers){
				if(handlers[i].element == element && handlers[i].event == event && handlers[i].callback == callback){
					BX.removeCustomEvent(element, event, callbackProxy);
					delete handlers[i];
					This.log('Removed BX handler');
				}
			}
		}
	}
	
	// Remove all handlers
	this.removeAllHandlers = function(){
		for(let i in handlers){
			callback = $.proxy(handlers[i].callback, this);
			$(handlers[i].element).off(handlers[i].event, callback);
			delete handlers[i];
		}
	}
	
	// Go to selected step
	this.goToStep = function(customStepIndex){
		customStepIndex = parseInt(customStepIndex);
		if(!isNaN(customStepIndex) && customStepIndex > 0){
			if(customStepIndex <= options.steps.length){
				currStep = customStepIndex - 1;
				this.goNext(false);
			}
			else{
				alert('Max step is ' + options.steps.length);
			}
		}
	}
	
	// Execute callbackOut on exit
	this.executeCurrentStepOut = function(){
		if(currStep > 1 && steps[currStep] != undefined ) {
			if(typeof steps[currStep].callbackOut == 'function'){
				$.proxy(steps[currStep].callbackOut, this)(options, steps[currStep], 
					typeof steps[currStep+1] != undefined ? steps[currStep+1] : {});
			}
		}
	}
	
	// Check if teacher is run
	this.isVisible = function(){
		return overlayData.is(':visible');
	}
	
	// 3. INITIAL ACTIONS
	
	this.teacherStart();
	
	return this;
};
