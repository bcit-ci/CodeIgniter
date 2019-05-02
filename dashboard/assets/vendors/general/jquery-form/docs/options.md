---
---

## ajaxForm and ajaxSubmit Options
Both `ajaxForm` and `ajaxSubmit` support numerous options which can be provided using plain JavaScript `options` object containing any of the options below:  
**Note:** Aside from the options listed below, you can also pass any of the standard [$.ajax settings](https://api.jquery.com/jQuery.ajax/#jQuery-ajax-settings) to ajaxForm and ajaxSubmit.


### beforeSerialize
Default: `null`  
Callback function to be invoked before the form is serialized. This provides an opportunity to manipulate the form before it's values are retrieved. The `beforeSerialize` function is invoked with two arguments: the jQuery object for the form, and the Options Object passed into ajaxForm/ajaxSubmit.

```javascript
beforeSerialize: function($form, options) {
  // return false to cancel submit
}
```


### beforeSubmit
Default: `null`  
Callback function to be invoked before the form is submitted. The 'beforeSubmit' callback can be provided as a hook for running pre-submit logic or for validating the form data. If the 'beforeSubmit' callback returns false then the form will not be submitted. The 'beforeSubmit' callback is invoked with three arguments: the form data in array format, the jQuery object for the form, and the Options Object passed into ajaxForm/ajaxSubmit.

```javascript
beforeSubmit: function(arr, $form, options) {
  // The array of form data takes the following form:
  // [ { name: 'username', value: 'jresig' }, { name: 'password', value: 'secret' } ]

  // return false to cancel submit
}
```


### clearForm
Default: `null`  
Boolean flag indicating whether the form should be cleared if the submit is successful


### data
Default: `{}`  
An object containing extra data that should be submitted along with the form.  

```javascript
data: { key1: 'value1', key2: 'value2' }
```


### dataType
Default: `null`  
Expected data type of the response. One of: null, 'xml', 'script', or 'json'. The `dataType` option provides a means for specifying how the server response should be handled. This maps directly to the `jQuery.httpData` method. The following values are supported:

**'xml'**: if dataType == 'xml' the server response is treated as XML and the 'success' callback method, if specified, will be passed the responseXML value

**'json'**: if dataType == 'json' the server response will be evaluted and passed to the 'success' callback, if specified

**'script'**: if dataType == 'script' the server response is evaluated in the global context


### error
Callback function to be invoked upon error.


### forceSync
Default: `false`  
Boolean value. Set to true to remove short delay before posting form when uploading files (or using the iframe option). The delay is used to allow the browser to render DOM updates prior to performing a native form submit. This improves usability when displaying notifications to the user, such as "Please Wait…" **(version added: 2.38)**


### iframe
Default: `false`  
Boolean flag indicating whether the form should always target the server response to an iframe. This is useful in conjuction with file uploads. See the _File Uploads_ documentation on the [Code Samples](#code-samples) page for more info.


### iframeSrc
Default: `about:blank`  
Default value for HTTPS pages: `javascript:false`  
String value that should be used for the iframe's src attribute when/if an iframe is used.


### iframeTarget
Default: `null`  
Identifies the iframe element to be used as the response target for file uploads. By default, the plugin will create a temporary iframe element to capture the response when uploading files. This options allows you to use an existing iframe if you wish. When using this option the plugin will make no attempt at handling the response from the server. **(version added: 2.76)**


### method
Default: value of form's `method` attribute (or `GET` if none found)  
The HTTP method to use for the request (e.g. 'POST', 'GET', 'PUT'). **(version added: 4.2.0)**


### replaceTarget
Default: `false`  
Optionally used along with the the [`target`](#target) option. Set to `true` if the target should be replaced or `false` if only the target _contents_ should be replaced. **(version added: 2.43)**

### resetForm
Default: `null`  
Boolean flag indicating whether the form should be reset if the submit is successful


### semantic
Default: `false`  
Boolean flag indicating whether data must be submitted in strict semantic order (slower). Note that the normal form serialization is done in semantic order with the exception of input elements of `type="image"`.  
**Note:** You should _only_ set the `semantic` option to `true` if your server has strict semantic requirements **and** your form contains an input element of `type="image"`.


### success
Default: `null`  
Callback function to be invoked after the form has been submitted. If a 'success' callback function is provided it is invoked after the response has been returned from the server. It is passed the following standard jQuery arguments:

1. `data`, formatted according to the dataType parameter or the dataFilter callback function, if specified
2. `textStatus`, string
3. `jqXHR`, object
4. `$form` jQuery object containing form element


### target
Default: `null`  
Identifies the element(s) in the page to be updated with the server response. This value may be specified as a jQuery selection string, a jQuery object, or a DOM element.


### type
Default: value of form's `method` attribute (or 'GET' if none found)  
The HTTP method to use for the request (e.g. 'POST', 'GET', 'PUT').  
An alias for `method` option. Overridden by the `method` value if both are present.


### uploadProgress
Default: `null`  
Callback function to be invoked with upload progress information (if supported by the browser). The callback is passed the following arguments:

1. event; the browser event
2. position (integer)
3. total (integer)
4. percentComplete (integer)


### url
Default: value of form's `action` attribute  
URL to which the form data will be submitted.


## Example
```javascript
// prepare Options Object
var options = {
  target:  '#divToUpdate',
  url:     'comment.php',
  success: function() {
    alert('Thanks for your comment!');
  }
};

// pass options to ajaxForm
$('#myForm').ajaxForm(options);
```
