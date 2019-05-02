# toastr
**toastr** is a Javascript library for non-blocking notifications. jQuery is required. The goal is to create a simple core library that can be customized and extended.

[![Build Status](https://travis-ci.org/CodeSeven/toastr.svg)](https://travis-ci.org/CodeSeven/toastr)
Browser testing provided by BrowserStack.

## Current Version
2.1.4

## Demo
- Demo can be found at http://codeseven.github.io/toastr/demo.html
- [Demo using FontAwesome icons with toastr](http://plnkr.co/edit/6W9URNyyp2ItO4aUWzBB?p=preview)

## [CDNjs](https://cdnjs.com/libraries/toastr.js)
Toastr is hosted at CDN JS

#### Debug
- [//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css](//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css)

#### Minified
- [//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js](//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js)
- [//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css](//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css)

## Install

#### [NuGet Gallery](http://nuget.org/packages/toastr)
```
Install-Package toastr
```

#### [Bower](http://bower.io/search/?q=toastr)
```
bower install toastr
```

#### [npm](https://www.npmjs.com/package/toastr)
```
npm install --save toastr
```

#### [Ruby on Rails](https://github.com/tylergannon/toastr-rails)
```ruby
# Gemfile

gem 'toastr-rails'
```

```coffee
# application.coffee

#= require toastr
```

```scss
// application.scss

@import "toastr";
```




## Wiki and Change Log
[Wiki including Change Log](https://github.com/CodeSeven/toastr/wiki)

## Breaking Changes

#### Animation Changes
The following animations options have been deprecated and should be replaced:

 - Replace `options.fadeIn` with `options.showDuration`
 - Replace `options.onFadeIn` with `options.onShown`
 - Replace `options.fadeOut` with `options.hideDuration`
 - Replace `options.onFadeOut` with `options.onHidden`

## Quick Start

### 3 Easy Steps
For other API calls, see the [demo](http://codeseven.github.io/toastr/demo.html).

1. Link to toastr.css `<link href="toastr.css" rel="stylesheet"/>`

2. Link to toastr.js `<script src="toastr.js"></script>`

3. use toastr to display a toast for info, success, warning or error
	```js
	// Display an info toast with no title
	toastr.info('Are you the 6 fingered man?')
	```

### Other Options
```js
// Display a warning toast, with no title
toastr.warning('My name is Inigo Montoya. You killed my father, prepare to die!')

// Display a success toast, with a title
toastr.success('Have fun storming the castle!', 'Miracle Max Says')

// Display an error toast, with a title
toastr.error('I do not think that word means what you think it means.', 'Inconceivable!')

// Immediately remove current toasts without using animation
toastr.remove()

// Remove current toasts using animation
toastr.clear()

// Override global options
toastr.success('We do have the Kapua suite available.', 'Turtle Bay Resort', {timeOut: 5000})
```

### Escape HTML characters
In case you want to escape HTML characters in title and message

	toastr.options.escapeHtml = true;

### Close Button
Optionally enable a close button
```js
toastr.options.closeButton = true;
````

Optionally override the close button's HTML.

```js
toastr.options.closeHtml = '<button><i class="icon-off"></i></button>';
```

You can also override the CSS/LESS for `#toast-container .toast-close-button`

Optionally override the hide animation when the close button is clicked (falls back to hide configuration).
```js
toastr.options.closeMethod = 'fadeOut';
toastr.options.closeDuration = 300;
toastr.options.closeEasing = 'swing';
```

### Display Sequence
Show newest toast at bottom (top is default)
```js
toastr.options.newestOnTop = false;
```

### Callbacks
```js
// Define a callback for when the toast is shown/hidden/clicked
toastr.options.onShown = function() { console.log('hello'); }
toastr.options.onHidden = function() { console.log('goodbye'); }
toastr.options.onclick = function() { console.log('clicked'); }
toastr.options.onCloseClick = function() { console.log('close button clicked'); }
```

### Animation Options
Toastr will supply default animations, so you do not have to provide any of these settings. However you have the option to override the animations if you like.

#### Easings
Optionally override the animation easing to show or hide the toasts. Default is swing. swing and linear are built into jQuery.
```js
toastr.options.showEasing = 'swing';
toastr.options.hideEasing = 'linear';
toastr.options.closeEasing = 'linear';
```

Using the jQuery Easing plugin (http://www.gsgd.co.uk/sandbox/jquery/easing/)
```js
toastr.options.showEasing = 'easeOutBounce';
toastr.options.hideEasing = 'easeInBack';
toastr.options.closeEasing = 'easeInBack';
```

#### Animation Method
Use the jQuery show/hide method of your choice. These default to fadeIn/fadeOut. The methods fadeIn/fadeOut, slideDown/slideUp, and show/hide are built into jQuery.
```js
toastr.options.showMethod = 'slideDown';
toastr.options.hideMethod = 'slideUp';
toastr.options.closeMethod = 'slideUp';
```

### Prevent Duplicates
Rather than having identical toasts stack, set the preventDuplicates property to true. Duplicates are matched to the previous toast based on their message content.
```js
toastr.options.preventDuplicates = true;
```

### Timeouts
Control how toastr interacts with users by setting timeouts appropriately. Timeouts can be disabled by setting them to 0.
```js
toastr.options.timeOut = 30; // How long the toast will display without user interaction
toastr.options.extendedTimeOut = 60; // How long the toast will display after a user hovers over it
```


### Progress Bar
Visually indicate how long before a toast expires.
```js
toastr.options.progressBar = true;
```

### rtl
Flip the toastr to be displayed properly for right-to-left languages.
```js
toastr.options.rtl = true;
```

## Building Toastr

To build the minified and css versions of Toastr you will need [node](http://nodejs.org) installed. (Use Homebrew or Chocolatey.)

```
npm install -g gulp karma-cli
npm install
```

At this point the dependencies have been installed and you can build Toastr

- Run the analytics `gulp analyze`
- Run the test `gulp test`
- Run the build `gulp`

## Contributing

For a pull request to be considered it must resolve a bug, or add a feature which is beneficial to a large audience.

Pull requests must pass existing unit tests, CI processes, and add additional tests to indicate successful operation of a new feature, or the resolution of an identified bug.

Requests must be made against the `develop` branch. Pull requests submitted against the `master` branch will not be considered.

All pull requests are subject to approval by the repository owners, who have sole discretion over acceptance or denial.

## Authors
**John Papa**

+ [http://twitter.com/John_Papa](http://twitter.com/John_Papa)

**Tim Ferrell**

+ [http://twitter.com/ferrell_tim](http://twitter.com/ferrell_tim)

**Hans Fjällemark**

+ [http://twitter.com/hfjallemark](http://twitter.com/hfjallemark)

## Credits
Inspired by https://github.com/Srirangan/notifer.js/.

## Copyright
Copyright © 2012-2015

## License
toastr is under MIT license - http://www.opensource.org/licenses/mit-license.php
