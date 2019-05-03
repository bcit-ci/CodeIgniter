# jQuery Form [![Build Status](https://travis-ci.org/jquery-form/form.svg?branch=master)](https://travis-ci.org/jquery-form/form)

## Overview
The jQuery Form Plugin allows you to easily and unobtrusively upgrade HTML forms to use AJAX. The main methods, ajaxForm and ajaxSubmit, gather information from the form element to determine how to manage the submit process. Both of these methods support numerous options which allows you to have full control over how the data is submitted.

No special markup is needed, just a normal form. Submitting a form with AJAX doesn't get any easier than this!

## Community
Want to contribute to jQuery Form? Awesome! See [CONTRIBUTING](CONTRIBUTING.md) for more information.

### Code of Conduct
Please note that this project is released with a [Contributor Code of Conduct](CODE_OF_CONDUCT.md) to ensure that this project is a welcoming place for **everyone** to contribute to. By participating in this project you agree to abide by its terms.

## Requirements
Requires jQuery 1.7.2 or later. Compatible with jQuery 2.x.x and 3.x.x.

## Download
* **Development:** [src/jquery.form.js
](https://github.com/jquery-form/form/blob/master/src/jquery.form.js)
* **Production/Minified:** [dist/jquery.form.min.js
](https://github.com/jquery-form/form/blob/master/dist/jquery.form.min.js)

### CDN
```html
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>
```
or
```html
<script src="https://cdn.jsdelivr.net/gh/jquery-form/form@4.2.2/dist/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>
```

---

## API

### jqXHR
The jqXHR object is stored in element <em>data</em>-cache with the <code>jqxhr</code> key after each <code>ajaxSubmit</code>
call. It can be accessed like this:

````javascript
var form = $('#myForm').ajaxSubmit({ /* options */ });
var xhr = form.data('jqxhr');

xhr.done(function() {
...
});
````

### ajaxForm( options )
Prepares a form to be submitted via AJAX by adding all of the necessary event listeners. It does **not** submit the form. Use `ajaxForm` in your document's `ready` function to prepare existing forms for AJAX submission, or with the `delegation` option to handle forms not yet added to the DOM.  
Use ajaxForm when you want the plugin to manage all the event binding for you.

````javascript
// prepare all forms for ajax submission
$('form').ajaxForm({
	target: '#myResultsDiv'
});
````

### ajaxSubmit( options )
Immediately submits the form via AJAX. In the most common use case this is invoked in response to the user clicking a submit button on the form.
Use ajaxSubmit if you want to bind your own submit handler to the form.

````javascript
// bind submit handler to form
$('form').on('submit', function(e) {
	e.preventDefault(); // prevent native submit
	$(this).ajaxSubmit({
		target: 'myResultsDiv'
	})
});
````

---

## Options
**Note:** All standard [$.ajax](http://api.jquery.com/jQuery.ajax) options can be used.

### beforeSerialize
Callback function invoked prior to form serialization. Provides an opportunity to manipulate the form before its values are retrieved. Returning `false` from the callback will prevent the form from being submitted. The callback is invoked with two arguments: the jQuery wrapped form object and the options object.

````javascript
beforeSerialize: function($form, options) {
    // return false to cancel submit
}
````

### beforeSubmit
Callback function invoked prior to form submission. Returning `false` from the callback will prevent the form from being submitted. The callback is invoked with three arguments: the form data in array format, the jQuery wrapped form object, and the options object.

````javascript
beforeSubmit: function(arr, $form, options) {
    // form data array is an array of objects with name and value properties
    // [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ]
    // return false to cancel submit
}
````

### filtering
Callback function invoked before processing fields. This provides a way to filter elements.

````javascript
filtering: function(el, index) {
	if ( !$(el).hasClass('ignore') ) {
		return el;
	}
}
````

### clearForm
Boolean flag indicating whether the form should be cleared if the submit is successful

### data
An object containing extra data that should be submitted along with the form.

````
data: { key1: 'value1', key2: 'value2' }
````

### dataType
Expected data type of the response. One of: null, 'xml', 'script', or 'json'. The dataType option provides a means for specifying how the server response should be handled. This maps directly to jQuery's dataType method. The following values are supported:

* 'xml': server response is treated as XML and the 'success' callback method, if specified, will be passed the responseXML value
* 'json': server response will be evaluted and passed to the 'success' callback, if specified
* 'script': server response is evaluated in the global context

### delegation
true to enable support for event delegation
*requires jQuery v1.7+*

````javascript
// prepare all existing and future forms for ajax submission
$('form').ajaxForm({
    delegation: true
});
````

### error
Callback function to be invoked upon error.

### forceSync
Only applicable when explicity using the iframe option or when uploading files on browses that don't support XHR2.
Set to `true` to remove the short delay before posting form when uploading files. The delay is used to allow the browser to render DOM updates prior to performing a native form submit. This improves usability when displaying notifications to the user, such as "Please Wait..."

### iframe
Boolean flag indicating whether the form should *always* target the server response to an iframe instead of leveraging XHR when possible.

### iframeSrc
String value that should be used for the iframe's src attribute when an iframe is used.

### iframeTarget
Identifies the iframe element to be used as the response target for file uploads. By default, the plugin will create a temporary iframe element to capture the response when uploading files. This options allows you to use an existing iframe if you wish. When using this option the plugin will make no attempt at handling the response from the server.

### method
The HTTP method to use for the request (e.g. 'POST', 'GET', 'PUT').

### replaceTarget
Optionally used along with the the target option. Set to true if the target should be replaced or false if only the target contents should be replaced.

### resetForm
Boolean flag indicating whether the form should be reset if the submit is successful

### semantic
Boolean flag indicating whether data must be submitted in strict semantic order (slower). Note that the normal form serialization is done in semantic order with the exception of input elements of `type="image"`. You should only set the semantic option to true if your server has strict semantic requirements and your form contains an input element of `type="image"`.

### success
Callback function to be invoked after the form has been submitted. If a 'success' callback function is provided it is invoked after the response has been returned from the server. It is passed the following standard jQuery arguments:

1. `data`, formatted according to the dataType parameter or the dataFilter callback function, if specified
2. `textStatus`, string
3. `jqXHR`, object
4. `$form` jQuery object containing form element

### target
Identifies the element(s) in the page to be updated with the server response. This value may be specified as a jQuery selection string, a jQuery object, or a DOM element.

### type
The HTTP method to use for the request (e.g. 'POST', 'GET', 'PUT').  
An alias for `method` option. Overridden by the `method` value if both are present.

### uploadProgress
Callback function to be invoked with upload progress information (if supported by the browser). The callback is passed the following arguments:

1. event; the browser event
2. position (integer)
3. total (integer)
4. percentComplete (integer)

### url
URL to which the form data will be submitted.

---

## Utility Methods
### formSerialize
Serializes the form into a query string. This method will return a string in the format: `name1=value1&name2=value2`

````javascript
var queryString = $('#myFormId').formSerialize();
````

### fieldSerialize
Serializes field elements into a query string. This is handy when you need to serialize only part of a form. This method will return a string in the format: `name1=value1&name2=value2`

````javascript
var queryString = $('#myFormId .specialFields').fieldSerialize();
````

### fieldValue
Returns the value(s) of the element(s) in the matched set in an array. This method always returns an array. If no valid value can be determined the array will be empty, otherwise it will contain one or more values.

### resetForm
Resets the form to its original state by invoking the form element's native DOM method.

### clearForm
Clears the form elements. This method emptys all of the text inputs, password inputs and textarea elements, clears the selection in any select elements, and unchecks all radio and checkbox inputs. It does *not* clear hidden field values.

### clearFields
Clears selected field elements. This is handy when you need to clear only a part of the form.

---

## File Uploads
The Form Plugin supports use of [XMLHttpRequest Level 2]("http://www.w3.org/TR/XMLHttpRequest/") and [FormData](https://developer.mozilla.org/en/XMLHttpRequest/FormData) objects on browsers that support these features. As of today (March 2012) that includes Chrome, Safari, and Firefox. On these browsers (and future Opera and IE10) files uploads will occur seamlessly through the XHR object and progress updates are available as the upload proceeds. For older browsers, a fallback technology is used which involves iframes. [More Info](http://malsup.com/jquery/form/#file-upload)

---

## Contributors
This project has transferred from [github.com/malsup/form](https://github.com/malsup/form/), courtesy of [Mike Alsup](https://github.com/malsup).  
See [CONTRIBUTORS](CONTRIBUTORS.md) for details.

## License

This project is dual licensed under the LGPLv2.1 (or later) or MIT licenses:

* [GNU Lesser General Public License v2.1](LICENSE)
* [MIT](LICENSE-MIT)

---

Additional documentation and examples for version 3.51- at: [http://malsup.com/jquery/form/](http://malsup.com/jquery/form/)
