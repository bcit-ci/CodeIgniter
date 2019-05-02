---
---

## API
The Form Plugin API provides several methods that allow you to easily manage form data and form submission.

### ajaxForm
Prepares a form to be submitted via AJAX by adding all of the necessary event listeners. It does **not** submit the form. Use `ajaxForm` in your document's `ready` function to prepare your form(s) for AJAX submission. `ajaxForm` takes zero or one argument. The single argument can be either a callback function or an [Options Object](http://malsup.com/jquery/form/#options-object).  
Chainable: Yes.

**Note:** You can pass any of the standard [$.ajax settings](https://api.jquery.com/jQuery.ajax/#jQuery-ajax-settings) to ajaxForm

```javascript
$(function() {
  $('#myFormId').ajaxForm();
});
```


### ajaxSubmit
Immediately submits the form via AJAX. In the most common use case this is invoked in response to the user clicking a submit button on the form. `ajaxSubmit` takes zero or one argument. The single argument can be either a callback function or an [Options Object](http://malsup.com/jquery/form/#options-object).  
Chainable: Yes.

**Note:** You can pass any of the standard [$.ajax settings](https://api.jquery.com/jQuery.ajax/#jQuery-ajax-settings) to ajaxSubmit.

```javascript
// attach handler to form's submit event
$('#myFormId').submit(function() {
    // submit the form
    $(this).ajaxSubmit();
    // return false to prevent normal browser submit and page navigation
    return false;
});
```


### formSerialize
Serializes the form into a query string. This method will return a string in the format: `name1=value1&name2=value2`  
Chainable: No, this method returns a String.

```javascript
var queryString = $('#myFormId').formSerialize();
// the data could now be submitted using $.get, $.post, $.ajax, etc
$.post('myscript.php', queryString);
```


### fieldSerialize
Serializes field elements into a query string. This is handy when you need to serialize only part of a form. This method will return a string in the format: `name1=value1&name2=value2`  
Chainable: No, this method returns a String.

```javascript
var queryString = $('#myFormId .specialFields').fieldSerialize();
```


### fieldValue
Returns the value(s) of the element(s) in the matched set in an array. As of version .91, this method **always** returns an array. If no valid value can be determined the array will be empty, otherwise it will contain one or more values.  
Chainable: No, this method returns an array.

```javascript
// get the value of the password input
var value = $('#myFormId :password').fieldValue();
alert('The password is: ' + value[0]);
```


### resetForm
Resets the form to its original state by invoking the form element's native <abbr title="Document Object Model">DOM</abbr> method.  
Chainable: Yes.

```javascript
$('#myFormId').resetForm();
```


### clearForm
Clears the form elements. This method emptys all of the text inputs, password inputs and textarea elements, clears the selection in any select elements, and unchecks all radio and checkbox inputs.  
Chainable: Yes.

```javascript
$('#myFormId').clearForm();
```


### clearFields
Clears field elements. This is handy when you need to clear only a part of the form.  
Chainable: Yes.

```javascript
$('#myFormId .specialFields').clearFields();
```
