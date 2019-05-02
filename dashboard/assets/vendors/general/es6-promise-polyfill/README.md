[![NPM version](https://img.shields.io/npm/v/es6-promise-polyfill.svg)](https://www.npmjs.com/package/es6-promise-polyfill)
[![Build Status](https://travis-ci.org/lahmatiy/es6-promise-polyfill.svg?branch=master)](https://travis-ci.org/lahmatiy/es6-promise-polyfill)

# ES6 Promise polyfill

This is a polyfill of [ES6 Promise](https://github.com/domenic/promises-unwrapping). The implementation based on [Jake Archibald implementation](https://github.com/jakearchibald/es6-promise) a subset of [rsvp.js](https://github.com/tildeio/rsvp.js). If you're wanting extra features and more debugging options, check out the [full library](https://github.com/tildeio/rsvp.js).

For API details and how to use promises, see the <a href="http://www.html5rocks.com/en/tutorials/es6/promises/">JavaScript Promises HTML5Rocks article</a>.

## Notes

The main target: implementation should be conformance with browser's implementations and to be minimal as possible in size. So it's strictly polyfill of ES6 Promise specification and nothing more.

It passes both [Promises/A+ test suite](https://github.com/promises-aplus/promises-tests) and [rsvp.js test suite](https://github.com/jakearchibald/es6-promise/tree/master/test). And as small as 2,6KB min (or 1KB min+gzip).

The polyfill uses `setImmediate` if available, or fallback to use `setTimeout`. Use [setImmediate polyfill](https://github.com/YuzuJS/setImmediate) by @YuzuJS to reach better performance.

## How to use

### Browser

To install:

```sh
bower install es6-promise-polyfill
```

To use:

```htmpl
<script src="bower_components/es6-promise-polyfill/promise.min.js"></script>
<script>
  var promise = new Promise(...);
</script>
```

### Node.js

To install:

```sh
npm install es6-promise-polyfill
```

To use:

```js
var Promise = require('es6-promise-polyfill').Promise;
var promise = new Promise(...);
```

## Usage in IE<9

`catch` is a reserved word in IE<9, meaning `promise.catch(func)` throws a syntax error. To work around this, use a string to access the property:

```js
promise['catch'](function(err) {
  // ...
});
```

Or use `.then` instead:

```js
promise.then(undefined, function(err) {
  // ...
});
```

## License

Licensed under the MIT License.
