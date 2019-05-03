/**
 * Modernizr Support Plugin
 *
 * @version 2.3.4
 * @author Vivid Planet Software GmbH
 * @author Artus Kolanowski
 * @author David Deutsch
 * @license The MIT License (MIT)
 */
;(function($, Modernizr, window, document, undefined) {

	var events = {
		transition: {
			end: {
				WebkitTransition: 'webkitTransitionEnd',
				MozTransition: 'transitionend',
				OTransition: 'oTransitionEnd',
				transition: 'transitionend'
			}
		},
		animation: {
			end: {
				WebkitAnimation: 'webkitAnimationEnd',
				MozAnimation: 'animationend',
				OAnimation: 'oAnimationEnd',
				animation: 'animationend'
			}
		}
	};

	if (!Modernizr) {
		throw new Error('Modernizr is not loaded.');
	}

	$.each([ 'cssanimations', 'csstransitions', 'csstransforms', 'csstransforms3d', 'prefixed' ], function(i, property) {
		if (typeof Modernizr[property] == 'undefined') {
			throw new Error([ 'Modernizr "', property, '" is not loaded.' ].join(''));
		}
	});

	if (Modernizr.csstransitions) {
		/* jshint -W053 */
		$.support.transition = new String(Modernizr.prefixed('transition'))
		$.support.transition.end = events.transition.end[ $.support.transition ];
		// fix transitionend support detection, which does not work properly for older Android versions,
        	// as it does not give the prefixed event name. here we use Modernizr to ensure the correct event.
        	// see:
        	// https://github.com/Modernizr/Modernizr/issues/897
        	// https://github.com/niksy/modernizr-detects/commit/05d148fc4f3813b1412c836325a9ca78c7a63f4d
        	if (/Android 4\.[123]/.test(navigator.userAgent)) {
                	$.support.transition.end = 'webkitTransitionEnd';
        	}
	}

	if (Modernizr.cssanimations) {
		/* jshint -W053 */
		$.support.animation = new String(Modernizr.prefixed('animation'))
		$.support.animation.end = events.animation.end[ $.support.animation ];
	}

	if (Modernizr.csstransforms) {
		/* jshint -W053 */
		$.support.transform = new String(Modernizr.prefixed('transform'));
		$.support.transform3d = Modernizr.csstransforms3d;
	}
})(window.Zepto || window.jQuery, window.Modernizr, window, document);
