# sticky-js
[![npm version](https://badge.fury.io/js/sticky-js.svg)](https://badge.fury.io/js/sticky-js)
[![Bower version](https://badge.fury.io/bo/sticky.js.svg)](https://badge.fury.io/bo/sticky.js)

> Sticky-js is a library for sticky elements written in vanilla javascript. With this library you can easily set sticky elements on your website. It's also responsive.

[DEMO](https://rgalus.github.io/sticky-js/)

## Features

- Written in vanilla javascript, no dependencies needed
- Lightweight (minified: ~6.08kb, gzipped: ~1.67kb)
- It can be sticky to the entire page or to selected parent container
- No additional CSS needed

## Install

````
npm install sticky-js
````

````
bower install sticky.js
````

## Usage

Simply include

```html
<script src="sticky.min.js"></script>
```

Then have element

```html
<div class="selector">Sticky element</div>
```

Initialize in javascript

```js
var sticky = new Sticky('.selector');
```

Syntax

```js
new Sticky([selector:string], [global options:object])
```

CommonJS
```js
var Sticky = require('sticky-js');

var sticky = new Sticky('.selector');
```

## Examples

Multiple sticky elements with data-sticky-container and [options](https://github.com/rgalus/sticky-js#available-options)

```html
<div class="row" data-sticky-container>
  <div class="medium-2 columns">
    <img src="http://placehold.it/250x250" class="sticky" data-margin-top="20" data-sticky-for="1023" data-sticky-class="is-sticky">
  </div>
  <div class="medium-8 columns">
    <h1>Sticky-js</h1>
    <p>Lorem ipsum.....</p>
  </div>
  <div class="medium-2 columns">
    <img src="http://placehold.it/250x250" class="sticky" data-margin-top="20" data-sticky-for="1023" data-sticky-class="is-sticky">
  </div>
</div>

<script src="sticky.min.js"></script>
<script>
  var sticky = new Sticky('.sticky');
</script>
```

## Methods

Update sticky, e.g. when parent container (data-sticky-container) change height

```js
var sticky = new Sticky('.sticky');

// and when parent change height..
sticky.update();
```

Destroy sticky element

```js
var sticky = new Sticky('.sticky');

sticky.destroy();
```

## Available options

Option | Type | Default | Description
------ | ---- | ------- | ----
data-sticky-wrap | boolean | false | When it's `true` sticky element is wrapped in `<span></span>` which has sticky element dimensions. Prevents content from "jumping".
data-margin-top | number | 0 | Margin between page and sticky element when scrolled
data-sticky-for | number | 0 | Breakpoint which when is bigger than viewport width, sticky is activated and when is smaller, then sticky is destroyed
data-sticky-class | string | null | Class added to sticky element when it is stuck

### Development

Clone this repository and run

```js
npm start
```

## Browser Compatibility

Library is using ECMAScript 5 features.

* IE 9+
* Chrome 23+
* Firefox 21+
* Safari 6+
* Opera 15+

If you need this library working with older browsers you should use ECMAScript 5 polyfill.

[Full support](http://caniuse.com/#search=ECMAScript%205)

* * *

### Website

https://rgalus.github.io/sticky-js/

### License

[MIT License](https://github.com/rgalus/sticky-js/blob/master/LICENSE)
