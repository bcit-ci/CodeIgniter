# noUiSlider

noUiSlider is a lightweight JavaScript range slider.

- **No dependencies**
- All modern browsers and IE > 9 are supported
- Fully **responsive**
- **Touch support** on Android, iOS and Windows devices
- Tons of [examples](https://refreshless.com/nouislider/examples) and answered [Stack Overflow questions](https://stackoverflow.com/questions/tagged/nouislider)

License
-------
As of release 12.0.0, noUiSlider is licensed [MIT](https://choosealicense.com/licenses/mit/). You can use it **for free** and **without any attribution**, in any personal or commercial project.

[Documentation](https://refreshless.com/nouislider/)
-------
An extensive documentation, including **examples**, **options** and **configuration details**, is available here: [noUiSlider documentation](https://refreshless.com/nouislider/).

Changelog
---------

### 12.1.0 (*2018-10-25*)
- Added: `unconstrained` behaviour (#747, #815, #913)
- Added: `setHandle` API (#917)
- Changed: point to `nouislider.js` in `package.json`.`main` (#921)

### 12.0.0 (*2018-09-14*) 
- Change: License changed to MIT;
- Change: Build process is now based on NPM scripts, phasing out the Grunt task runner.
- Fixed: Aria values are now as per spec (#889);
- Change: Pips formatting are now written as HTML (#875);
- Change: The `filter` option is now called for all pips (#754);
- Added: The `filter` option can now return `-1` to hide a pip (#754);
- Added: `keyboardSupport` option (#867, #895);
- Added: `documentElement` option (#821);

### 11.1.0 (*2018-04-02*)
- Change: `null` options are now handled consistently (#856);
- Fixed: Missing transform origin in IE9 (#870);
- Fixed: `padding` on one side of the slider could not exceed `50%` (#865);

### 11.0.3 (*2018-01-21*)
Refactor of source code. There are no meaningful changes in the distributed files;

### 11.0.2 (*2018-01-20*)
- Fixed: Slider ignores clicks on `.noUi-target` outside of `.noUi-base` (#842);
- Fixed: `.noUi-origin` moving out of the page causes horizontal scrolling (#852);
- Fixed: Relative `.noUi-handle` has unintended margin (#854);

### 11.0.0 (*2018-01-12*)
noUiSlider 11 does not include any breaking API changes.
Unless major changes were made to the stylesheet or you specifically depend
on the handle/connect order in the DOM, there should be no issues upgrading.
- Change: Use CSS transforms for handle movement, resulting in a massive performance improvement (#718);
- Change: Support multitouch by default;
- Change: Handle stacking is now on `.noUi-origin` instead of `.noUi-handle`;
- Added: A `.noUi-connects` element holding all `.noUi-connect` elements;
- Added: `[data-value]` property for `.noUi-value` in pips (#733);
- Added: `padding` option can now take an array for different padding values at both sides of a slider (#822);
- Removed: `useRequestAnimationFrame` option. No longer needed with CSS transforms;
- Removed: `multitouch` option. Now enabled by default;
- Fixed: Slider could ignore end events it should handle (#704, #805, #834);
- Fixed: Stop depending on array type (#801);
- Fixed: `set` method might bypass margin option (#823);
- Fixed: Alignment of pips for RTL sliders (#795);
- Fixed: Several issues regarding pips (#812, #826, #832);

### 10.1.0 (*2017-07-26*)
- Added: `multitouch` option (#793);

### 10.0.0 (*2017-05-28*)
- Change: Change event listeners to be passive (#785);
- Fixed: Pips are now updated when calling `updateOptions` (#669);
- Fixed: Content Security Policy issue with pips;
- Added: `removePips` method;
- Added: aria support (#685);
- Added: `ariaFormat` option (controls `aria-valuetext`);
- Fixed: throw a better error when mistakenly trying to initialize noUiSlider with `null` (#658);
- Fixed: Made order of events consistent and documented it (#775);
- Fixed: Border radius of connect bar, white space wrapping of tooltips (#773, #774);
- Fixed: Slider now uses `ownerDocument` instead of `document` (#767);

### 9.2.0 (*2017-01-17*)
- Added: Version number to exceptions;
- Added: `noUiSlider.version` holds current version number;
- Added: Throw exception on invalid `pips` configuration (#721);
- Added: Merged pull request that uses less preprocessor to generate CSS (#735);

### 9.1.0 (*2016-12-10*)
- Fixed: Slider not properly handling multitouch (#700, #704);
- Fixed: Removed a querySelector for the currently active handle (#720);
- Fixed: Removed iOS/webkit flashes on tap;
- Fixed: Incorrect error when using margin/limit with a step smaller than 0 (#736);
- Fixed: Drag option using incorrect cursor arrows (#681);
- Added: New `padding` option (#711);
- Added: Re-introduced `.noUi-handle-lower` and `.noUi-handle-upper` classes removed in 9.0.0;
- Added: Compatibility for legacy `connect` options removed in 9.0.0;

### 9.0.0 (*2016-09-26*)
- Added: Support for **more than 2 handles**;
- Added: `format` option can be updated (#641);
- Added: `reset` method the return slider to start values (#673);
- Change: `connect` option is now implemented as a separate node;
- Change: all event arguments, including the handle number, are now in slider order;
- Change: `updateOptions` now **modifies the original options** object. The reference in `slider.noUiSlider.options` remains up to date (#678);
- Change: more events fire when using various `behaviour` options (#664);
- Change: on `rtl` sliders, handles are now visually positioned from the sliders `right`/`bottom` edge;
- Change: events for `rtl` sliders now fire in the same order as for `ltr` sliders (with incremental handleNumbers);
- Change: internal `Spectrum` component is no longer `direction` aware;
- Change: `limit` and `margin` must be divisible by `step` (if set);
- Removed: `.noUi-stacking` class. Handles now stack themselves;
- ~~Removed~~ (returned in 9.1.0): `.noUi-handle-lower` and `.noUi-handle-upper` classes;
- Removed: `.noUi-background`. Use `.noUi-target` instead;
- ~~Removed~~ (backward compatibility in 9.1.0): `connect: 'lower'` and `connect: 'upper'`. These settings are replaced by `connect: [true, false]`;
- Fixed: default tooltip color (#687);
- Fixed: `margin` and `limit` calculated improperly after calling `updateOptions` with a new `range` option;
- Fixed: `range` option was required in update, even when not updating it (#682);
- Fixed: Cursor styling is now consistent for disabled handles and sliders (#644);
- Fixed: Sliders now ignore touches when the screen is touched multiple times (#649, #663, #668);

Devices
-------
Devices/browsers tested:
- Surface Pro 3 (Windows 10)
- iPad Air 2 (iOS 9.3)
- iPad 3 (iOS 8.4)
- Moto E (Android 5.1, Chrome)
- Lumia 930 (WP8.1, IE10 mobile)
- Lumia 930 (WM10, Edge)
- OnePlus 3 (Android 6)
	+ Chrome
	+ Firefox
- Asus S400C (Windows 10, Touch + mouse)
	+ Chrome
	+ Firefox
	+ Edge
	+ IE11
	+ IE10 (Emulated)
	+ IE9 (Emulated)

Webpack
-------
In order to use this with webpack, the easiest way to work with it is by using the [`ProvidePlugin`](https://webpack.js.org/plugins/provide-plugin/):

```javascript
// webpack.config.js
var webpack = require('webpack');
...
plugins: [
	new webpack.ProvidePlugin({
		noUiSlider: 'nouislider'
	})
]
...
```

If you're using ES6 imports, a simple [import with side effect](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/import#Import_a_module_for_its_side_effects_only)
is enough:

```
import 'nouislider';
```
