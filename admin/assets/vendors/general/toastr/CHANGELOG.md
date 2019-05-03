# 2.1.4 (2017-12-07)
- Same as 2.1.3, except 2.1.4 is on NPM

# 2.1.3 (2016-07-22)

## New Features
- Right to left text support
- Added callbacks for click events on close button

## Bug Fixes
- Fixed some CSS that prevented touch events from propagating on iOS devices
- Removed role attribute because it duplicated voice over on text-to-speech readers; kept aria-live.
- Consistent line-heights for close button to resolve issues with Bootstrap.

## Dev Ops
- Removed Plato from gulp

# 2.1.2 (2015-08-06)

## New Features
- Added HTML escaping (use toastr.options.escapeHTML)
- Added more options for overriding animations, specifically to closing a toast
- Events are now passed into the onClick handler of the toast

## Bug Fixes
- Resolved issue with centered toasts that would override top and bottom margins

## Dev Ops
- Removed `module` and `require` from the `.jshintrc` file
- Cleaned up the readme
- Builds moved to the `builds` folder
- Made href calls relative to the transport in the demo
- CSS is now built by the included LESS file as part of a `gulp` call
- Removed un-used deps

# 2.1.1 (2015-02-19)

## New Features
- Support for explicitly clearing a toast [jstawski](https://github.com/jstawski) | [PR 226](https://github.com/CodeSeven/toastr/pull/226)

## Dev Ops
- Refactor to use gulp

# 2.1.0  (2014-10-15)

## New Features
- Prevent duplicate sequential toasts [SBero](https://github.com/sbero) | [aa083d8](https://github.com/CodeSeven/toastr/commit/ccb377b6015d557dbb987df74750b97b3aa083d8) 
- Add support for top-center and bottom-center toasts [showwin](https://github.com/showwin) | [86a4798](https://github.com/CodeSeven/toastr/commit/86a4798e76c7d8516521780b7bd085d6bb4c371b)
- Add feature allowing progress bars to be shown for toasts [TravisTX](https://github.com/TravisTX) | [PR 189](https://github.com/CodeSeven/toastr/pull/189) 

`toastr.options.preventDuplicates = true;`

`toastr.options.progressBar = true;`

## Bug Fixes
- None

## Breaking Changes
- None

## Dev Ops

- Add karma test runner
- Added Travis CI hooks

# 2.0.3 (2014-05-17)

## New Features
 - None
 
## Bug Fixes
- positionClass changes were not being honored due to 2.0.2 release changes. Refactored getContainer to only get the container, unless a 2nd boolean parameter is passed in in which case it will also create it if the container did not exist [871c2a6](https://github.com/CodeSeven/toastr/commit/871c2a6e438bb6b996cfb80286720604a4cf00fd)

## Breaking Changes
 - None

# 2.0.2 (2014-04-29)

## New Features
- Added simple ARIA reader support ([45c6362](https://github.com/CodeSeven/toastr/commit/45c63628476f6b085a6579dc681f4fe61ba5820c))
- Added SASS support (direct port of CSS for now) ([b4c8b34](https://github.com/CodeSeven/toastr/commit/b4c8b3460efb8aa51c730dd38c35ef6b025db2cc))

## Bug Fixes
- Added sourcemap for the min file ([1da4bd1](https://github.com/CodeSeven/toastr/commit/1da4bd1dad21bcfc7fcfe73da1abb185cf2c3f9f))
- IE 8 does not support stopPropagation on the event ([6989573](https://github.com/CodeSeven/toastr/commit/698957325a8e7bf63990f71ee409b911d69bc8ec))
- Media query width fixes ([ea2f5db](https://github.com/CodeSeven/toastr/commit/ea2f5db6e5314dcfe48eb34176583849c177c00e))
- Fix of onHidden firing twice when clicking on it then moving mouse out of toast ([ad613b9](https://github.com/CodeSeven/toastr/commit/ad613b9f18feeec630497590b85ca75c52141ea3) , [#105](https://github.com/CodeSeven/toastr/issues/105))
- Clear all toasts followed by a new toast now displays correctly ([3126a53](https://github.com/CodeSeven/toastr/commit/3126a533e0ab12ec3ff374e155a37fd38bd23bb6) , [#149](https://github.com/CodeSeven/toastr/issues/149) , [#118](https://github.com/CodeSeven/toastr/issues/118))

## Breaking Changes
- None

# 2.0.1  (2013-09-01)

## New Features

### Close Button
Optionally enable a close button

    toastr.options.closeButton = true;

Optionally override the close button's HTML. 

    toastr.options.closeHtml = '<button><i class="icon-off"></i></button>';

You can also override the CSS/LESS for `#toast-container .toast-close-button`

### Callbacks
	// Define a callback for when the toast is shown/hidden
	toastr.options.onShown = function() { console.log('hello'); }
	toastr.options.onHidden = function() { console.log('goodbye'); }

### Animation Options
Toastr will supply default animations, so you do not have to provide any of these settings. However you have the option to override the animations if you like.

####Easings
Optionally override the animation easing to show or hide the toasts. Default is swing. swing and linear are built into jQuery.

	toastr.options.showEasing = 'swing';
	toastr.options.hideEasing = 'linear';

Using the jQuery Easing plugin (http://www.gsgd.co.uk/sandbox/jquery/easing/)

	toastr.options.showEasing = 'easeOutBounce';
	toastr.options.hideEasing = 'easeInBack';

####Animation Method
Use the jQuery show/hide method of your choice. These default to fadeIn/fadeOut. The methods fadeIn/fadeOut, slideDown/slideUp, and show/hide are built into jQuery.

	toastr.options.showMethod = 'slideDown'; 
	toastr.options.hideMethod = 'slideUp'; 
	

###Timeouts
Control how toastr interacts with users by setting timeouts appropriately.

	toastr.options.timeout = 30; // How long the toast will display without user interaction
	toastr.options.extendedTimeOut = 60; // How long the toast will display after a user hovers over it

## Breaking Changes

###Animation Changes
The following animations options have been deprecated and should be replaced:
 
 - Replace `options.fadeIn` with `options.showDuration`
 - Replace `options.onFadeIn` with `options.onShown`
 - Replace `options.fadeOut` with `options.hideDuration`
 - Replace `options.onFadeOut` with `options.onHidden`

# Version 1.3.1

## Display Sequence
Show newest toast at bottom (top is default)

	toastr.options.newestOnTop = false;
