# Changelog

## v4.0.1

- Improve performance of `enableAll`. (Issue #454)
- Handle edge case bug where Waypoint initialization during a specific part of iOS scroll bounce would cause an immediate trigger of it. (Issue #499)
- Maintain `window` Context/resize-handler even when there are only non-window-context waypoints. (Issue #442)

## v4.0.0

- Allow Sticky option `wrapper` to accept false, which will not create a wrapper and instead use the preexisting parent element.   (Pull #416)
- Waypoints that are immediately triggered on creation because they've already passed their trigger point now run their handlers on the next animation frame. This contains Zalgo. (Issue #384)
- Pass the jQuery object of items added during an Infinite page load to the `onAfterPageLoad` callback. (Pull #398)
- Add `enabled` option, `enable` and `disable` methods to the Inview shortcut (Pull #406)
- Make the Inview instance `this` within the callbacks, rather than the invdividual underlying waypoints. (Issue #412)
- Account for changes to jQuery 3 around calling `offset` on the window. (Pull #430)
- Add `context` option to Inview. (Issue #433)

## v3.1.1

- Fix bad `isWindow` checks causing errors in IE8-. (Issue #372)

## v3.1.0

- Add `Waypoint.disableAll` and `Waypoint.enableAll` methods.
- Fix Illegal Invocation errors stemming from non-window context use of `requestAnimationFrame`. (Pull #366)
- Keep disabled waypoints from triggering debug script errors. (Pull #365)
- Allow Infinite Scroll items to be root elements in the AJAX response. (Pull #361)
- In debug script, detect display none and fixed positioning defined in CSS.

## v3.0.1

- Add semicolons to the end of built files to aid in clean concatenation. (Issue #353)

## v3.0.0

- Remove hard jQuery dependency. Create builds for jQuery, Zepto, and no DOM framework. (Issue #282)
- Expose `Waypoint` and `Context` classes. (Issue #281)
- Add `Group` class and `group` option for grouping waypoints. Make `continuous` option work within these groups. (Issue #264)
- Add Inview shortcut. (Issue #131)
- Extend `continuous` option to cover refreshes. (Issue #166)
- Throttle resize and scroll handlers using `requestAnimationFrame` instead of a set millisecond timeout. Fallback to the old 60 FPS `setTimeout` throttle for unsupported browsers. (Issue #242)
- Add debugging script for diagnosing common problems.
- Remove `triggerOnce` option.
- Add `viewportWidth` utility method.
- Remove all traces of CoffeeScript.

## v2.0.5

- Allow sticky users to define which direction the stuck class shold be applied. (Issue #192)
- Fix bug where short content on a large screen could cause the infinite shortcut to stall after the first page load. (Issue #207)
- Make `unsticky` safe to use on any element. Previously it would unwrap the parent even if the element had never had `sticky` called on it or already had `unsticky` called previously. (Issue #225)
- Fix bug that would cause handlers to be overwritten when trying to reuse an options object. (Issue #253)
- Remove "More" link when infinite shortcut reaches last page. (Issue #260)
- Fix use of `this` instead of `window`, causing Browserify to fail. (Issue #262)
- Stop using deprecated jQuery `load` method. (Issue #283)

## v2.0.4

- Fix enable, disable, and destroys calls not chaining the jQuery object. (Issue #244) (Thanks [@robharper](https://github.com/robharper))
- Fix destroy not unregistering internal waypoint references if underlying node has been removed from the document, causing memory leaks. (Issue #243)

## v2.0.3

- Add "unsticky" function for sticky shortcut. (Issue #130)
- Exit early from Infinite shortcut if no "more" link exists. (Issue #140)
- Delay height evaluation of sticky shortcut wrapper. (Issue #151)
- Fix errors with Infinite shortcut's parsing of HTML with jQuery 1.9+. (Issue #163)


## v2.0.2

- Add AMD support. (Issue #116)
- Work around iOS issue with cancelled `setTimeout` timers by not using scroll throttling on touch devices. (Issue #120)
- If defined, execute `handler` option passed to sticky shortcut at the end of the stuck/unstuck change. (Issue #123)

## v2.0.1

- Lower default throttle values for `scrollThrottle` and `resizeThrottle`.
- Fix Issue #104: Pixel offsets written as strings are interpreted as %s.
- Fix Issue #100: Work around IE not firing scroll event on document shortening by forcing a scroll check on `refresh` calls.

## v2.0.0

- Rewrite Waypoints in CoffeeScript.
- Add Sticky and Infinite shortcut scripts.
- Allow multiple Waypoints on each element. (Issue #40)
- Allow horizontal scrolling Waypoints. (Issue #14)
- API additions: (#69, 83, 88)
    - prev, next, above, below, left, right, extendFn, enable, disable
- API subtractions:
    - remove
- Remove custom 'waypoint.reached' jQuery Event from powering the trigger.
- $.waypoints now returns object with vertical+horizontal properties and HTMLElement arrays instead of jQuery object (to preserve trigger order instead of jQuery's forced source order).
- Add enabled option.

## v1.1.7

- Actually fix the post-load bug in Issue #28 from v1.1.3.

## v1.1.6

- Fix potential memory leak by unbinding events on empty context elements.

## v1.1.5

- Make plugin compatible with Browserify/RequireJS. (Thanks [@cjroebuck](https://github.com/cjroebuck))

## v1.1.4

- Add handler option to give alternate binding method.
  
## v1.1.3

- Fix cases where waypoints are added post-load and should be triggered immediately.
  
## v1.1.2

- Fixed error thrown by waypoints with triggerOnce option that were triggered via resize refresh.

## v1.1.1

- Fixed bug in initialization where all offsets were being calculated as if set to 0 initially, causing unwarranted triggers during the subsequent refresh.
- Added `onlyOnScroll`, an option for individual waypoints that disables triggers due to an offset refresh that crosses the current scroll point. (All credit to [@knuton](https://github.com/knuton) on this one.)

## v1.1

- Moved the continuous option out of global settings and into the options
  object for individual waypoints.
- Added the context option, which allows for using waypoints within any
  scrollable element, not just the window.

## v1.0.2

- Moved scroll and resize handler bindings out of load.  Should play nicer with async loaders like Head JS and LABjs.
- Fixed a 1px off error when using certain % offsets.
- Added unit tests.

## v1.0.1

- Added $.waypoints('viewportHeight').
- Fixed iOS bug (using the new viewportHeight method).
- Added offset function alias: 'bottom-in-view'.

## v1.0

- Initial release.
