module('Autoplay tests');

function FakeClock() {
	// Instantiate a new controllable clock which overrides the built in Date
	// class on construction.
	var value = 1;

	this.tick = function(duration) {
		value += duration;
	};
	// TODO: This is broken and has to be fixed in the near future
	this.Date = function() {
		this.getTime = function() {
			return value;
		}
	}
}

function change_timeout(autoplay, first, second, wait) {
	var clock = new FakeClock();

	// This is a helper function to test multiple consecutive play calls with
	// different timeout values. Four steps will be completed by this function:

	// 1. The autoplay will be played in a stopped state with the first timeout.
	autoplay.stop();
	autoplay.play(first);

	// 2. Time will be forwarded a given wait time.
	clock.tick(wait);

	// 3. The autoplay will be paused.
	autoplay.pause();

	// 4. The autoplay will be played with the second timeout.
	autoplay.play(second);
}

// test('stopping the autoplay timer', function() {
// 	expect(2);
//
// 	var clock = new FakeClock();
//
// 	var carousel = $('#simple').owlCarousel().data('owl.carousel');
// 	var autoplay = carousel._plugins.autoplay;
//
// 	clock.tick(1);
//
// 	autoplay.stop();
// 	autoplay.play();
//
// 	equal(autoplay.read(), 0);
//
// 	autoplay.pause();
// 	autoplay.play();
//
// 	equal(autoplay.read(), 0);
// });
// TODO: See todo above, seems to be broken since a while as we are trying to assign the global const Date to a new function
// test('changing autoplay timeout values', function() {
// 	expect(4);
//
// 	var carousel = $('#simple').owlCarousel().data('owl.carousel');
// 	var autoplay = carousel._plugins.autoplay;
//
// 	// Changing the timeout from 2000 to 3000 after 3000 ticks should maintain
// 	// the elapsed time (1000) since the last transition.
// 	change_timeout(autoplay, 2000, 3000, 3000);
// 	equal(autoplay.read() % 3000, 1000);
//
// 	// Changing the timeout from 4000 to 5000 after 12000 ticks should maintain
// 	// the elapsed time (0) since the last transition.
// 	change_timeout(autoplay, 4000, 5000, 12000);
// 	equal(autoplay.read() % 5000, 0);
//
// 	// Changing the timeout from 5000 to 4000 after 12000 ticks should maintain
// 	// the elapsed time (2000) since the last transition.
// 	change_timeout(autoplay, 5000, 4000, 12000);
// 	equal(autoplay.read() % 4000, 2000);
//
// 	// Changing the timeout from 11000 to 6000 after 19000 ticks should reset
// 	// the elapsed timer value (7000) since the last transition to 0, because
// 	// it is larger than the timeout value.
// 	change_timeout(autoplay, 11000, 6000, 19000);
// 	equal(autoplay.read() % 6000, 0);
// });
