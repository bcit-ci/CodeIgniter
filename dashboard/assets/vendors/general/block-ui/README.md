# BlockUI - Page or element overlay  [![NPM version][npm-image]][npm-url]

### NOTE: This is a fork from the official version, for publishing on npm. See https://github.com/malsup/blockui/pull/114

## Getting Started
Download either the [production version][min] or the [development version][max] of BlockUI.

[min]: http://malsup.github.com/min/jquery.blockUI.min.js
[max]: http://malsup.github.com/jquery.blockUI.js

In your web page:

```html
<!-- include jQuery -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
<!-- include BlockUI -->
<script src="http://path/to/your/copy/of/jquery.blockUI.js"></script>
<script>
// invoke blockUI as needed -->
$(document).on('click', '#myButton', function() {
   $.blockUI();
});
></script>
</pre>
```

## Documentation, Demos and FAQ
Everything you need to know can be found here:
[http://jquery.malsup.com/block/](http://jquery.malsup.com/block/)


## Copyright and License
Copyright &copy; 2007-2013 M. Alsup.

The BlockUI plugin is dual licensed under the [MIT](http://malsup.github.com/mit-license.txt) and [GPL](http://malsup.github.com/gpl-license-v2.txt) licenses.

You may use either license.  The MIT license is recommended for most projects because it is simple and easy to understand and it places almost no restrictions on what you can do with the plugin.

If the GPL suits your project better you are also free to use the plugin under that license.

You do not have to do anything special to choose one license or the other and you don't have to notify anyone which license you are using. You are free to use the BlockUI plugin in commercial projects as long as the copyright header is left intact.

[npm-url]: https://www.npmjs.com/package/block-ui/
[npm-image]: http://img.shields.io/npm/v/block-ui.svg
