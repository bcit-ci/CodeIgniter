jQuery#typeahead
----------------

The UI component of typeahead.js is available as a jQuery plugin. It's 
responsible for rendering suggestions and handling DOM interactions.

Table of Contents
-----------------

* [Features](#features)
* [Usage](#usage)
  * [API](#api)
  * [Options](#options)
  * [Datasets](#datasets)
  * [Custom Events](#custom-events)
  * [Class Names](#class-names)

Features
--------

* Displays suggestions to end-users as they type
* Shows top suggestion as a hint (i.e. background text)
* Supports custom templates to allow for UI flexibility
* Works well with RTL languages and input method editors
* Highlights query matches within the suggestion
* Triggers custom events to encourage extensibility

Usage
-----

### API

* [`jQuery#typeahead(options, [*datasets])`](#jquerytypeaheadoptions-datasets)
* [`jQuery#typeahead('val')`](#jquerytypeaheadval)
* [`jQuery#typeahead('val', val)`](#jquerytypeaheadval-val)
* [`jQuery#typeahead('destroy')`](#jquerytypeaheaddestroy)
* [`jQuery.fn.typeahead.noConflict()`](#jqueryfntypeaheadnoconflict)

#### jQuery#typeahead(options, [\*datasets])

For a given `input[type="text"]`, enables typeahead functionality. `options` 
is an options hash that's used for configuration. Refer to [Options](#options) 
for more info regarding the available configs. Subsequent arguments 
(`*datasets`), are individual option hashes for datasets. For more details 
regarding datasets, refer to [Datasets](#datasets).

```javascript
$('.typeahead').typeahead({
  minLength: 3,
  highlight: true
},
{
  name: 'my-dataset',
  source: mySource
});
```

#### jQuery#typeahead('val')

Returns the current value of the typeahead. The value is the text the user has 
entered into the `input` element.

```javascript
var myVal = $('.typeahead').typeahead('val');
```

#### jQuery#typeahead('val', val)

Sets the value of the typeahead. This should be used in place of `jQuery#val`.

```javascript
$('.typeahead').typeahead('val', myVal);
```

#### jQuery#typeahead('open')

Opens the suggestion menu.

```javascript
$('.typeahead').typeahead('open');
```

#### jQuery#typeahead('close')

Closes the suggestion menu.

```javascript
$('.typeahead').typeahead('close');
```

#### jQuery#typeahead('destroy')

Removes typeahead functionality and reverts the `input` element back to its 
original state.

```javascript
$('.typeahead').typeahead('destroy');
```

#### jQuery.fn.typeahead.noConflict()

Returns a reference to the typeahead plugin and reverts `jQuery.fn.typeahead` 
to its previous value. Can be used to avoid naming collisions. 

```javascript
var typeahead = jQuery.fn.typeahead.noConflict();
jQuery.fn._typeahead = typeahead;
```

### Options

When initializing a typeahead, there are a number of options you can configure.

* `highlight` – If `true`, when suggestions are rendered, pattern matches
  for the current query in text nodes will be wrapped in a `strong` element with
  its class set to `{{classNames.highlight}}`. Defaults to `false`.

* `hint` – If `false`, the typeahead will not show a hint. Defaults to `true`.

* `minLength` – The minimum character length needed before suggestions start 
  getting rendered. Defaults to `1`.

* `classNames` – For overriding the default class names used. See 
  [Class Names](#class-names) for more details.

### Datasets

A typeahead is composed of one or more datasets. When an end-user modifies the
value of a typeahead, each dataset will attempt to render suggestions for the
new value. 

For most use cases, one dataset should suffice. It's only in the scenario where
you want rendered suggestions to be grouped based on some sort of categorical 
relationship that you'd need to use multiple datasets. For example, on 
twitter.com, the search typeahead groups results into recent searches, trends, 
and accounts – that would be a great use case for using multiple datasets.

Datasets can be configured using the following options.

* `source` – The backing data source for suggestions. Expected to be a function 
  with the signature `(query, syncResults, asyncResults)`. `syncResults` should
  be called with suggestions computed synchronously and `asyncResults` should be 
  called with suggestions computed asynchronously (e.g. suggestions that come 
  for an AJAX request). `source` can also be a Bloodhound instance. 
  **Required**.

* `async` – Lets the dataset know if async suggestions should be expected. If
  not set, this information is inferred from the signature of `source` i.e.
  if the `source` function expects 3 arguments, `async` will be set to `true`.

* `name` – The name of the dataset. This will be appended to 
  `{{classNames.dataset}}-` to form the class name of the containing DOM 
  element. Must only consist of underscores, dashes, letters (`a-z`), and 
  numbers. Defaults to a random number.

* `limit` – The max number of suggestions to be displayed. Defaults to `5`.

* `display` – For a given suggestion, determines the string representation 
  of it. This will be used when setting the value of the input control after a 
  suggestion is selected. Can be either a key string or a function that 
  transforms a suggestion object into a string. Defaults to stringifying the 
  suggestion.

* `templates` – A hash of templates to be used when rendering the dataset. Note
  a precompiled template is a function that takes a JavaScript object as its
  first argument and returns a HTML string.

  * `notFound` – Rendered when `0` suggestions are available for the given 
    query. Can be either a HTML string or a precompiled template. If it's a 
    precompiled template, the passed in context will contain `query`.

  * `pending` - Rendered when `0` synchronous suggestions are available but
    asynchronous suggestions are expected. Can be either a HTML string or a 
    precompiled template. If it's a precompiled template, the passed in context 
    will contain `query`.

  * `header`– Rendered at the top of the dataset when suggestions are present. 
    Can be either a HTML string or a precompiled template. If it's a precompiled 
    template, the passed in context will contain `query` and `suggestions`.

  * `footer`– Rendered at the bottom of the dataset when suggestions are 
    present. Can be either a HTML string or a precompiled template. If it's a 
    precompiled template, the passed in context will contain `query` and
    `suggestions`.

  * `suggestion` – Used to render a single suggestion. If set, this has to be a 
    precompiled template. The associated suggestion object will serve as the 
    context. Defaults to the value of `display` wrapped in a `div` tag i.e. 
    `<div>{{value}}</div>`.

### Custom Events

The following events get triggered on the input element during the life-cycle of
a typeahead.

* `typeahead:active` – Fired when the typeahead moves to active state.

* `typeahead:idle` – Fired when the typeahead moves to idle state.

* `typeahead:open` – Fired when the results container is opened.

* `typeahead:close` – Fired when the results container is closed.

* `typeahead:change` – Normalized version of the native [`change` event]. 
  Fired when input loses focus and the value has changed since it originally 
  received focus.

* `typeahead:render` – Fired when suggestions are rendered for a dataset. The
  event handler will be invoked with 4 arguments: the jQuery event object, the
  suggestions that were rendered, a flag indicating whether the suggestions
  were fetched asynchronously, and the name of the dataset the rendering 
  occurred in.

* `typeahead:select` – Fired when a suggestion is selected. The event handler 
  will be invoked with 2 arguments: the jQuery event object and the suggestion
  object that was selected.

* `typeahead:autocomplete` – Fired when a autocompletion occurs. The 
  event handler will be invoked with 2 arguments: the jQuery event object and 
  the suggestion object that was used for autocompletion.

* `typeahead:cursorchange` – Fired when the results container cursor moves. The 
  event handler will be invoked with 2 arguments: the jQuery event object and 
  the suggestion object that was moved to.

* `typeahead:asyncrequest` – Fired when an async request for suggestions is 
  sent. The event handler will be invoked with 3 arguments: the jQuery event 
  object, the current query, and the name of the dataset the async request 
  belongs to.

* `typeahead:asynccancel` – Fired when an async request is cancelled. The event 
  handler will be invoked with 3 arguments: the jQuery event object, the current 
  query, and the name of the dataset the async request belonged to.

* `typeahead:asyncreceive` – Fired when an async request completes. The event 
  handler will be invoked with 3 arguments: the jQuery event object, the current 
  query, and the name of the dataset the async request belongs to.

Example usage:

```
$('.typeahead').bind('typeahead:select', function(ev, suggestion) {
  console.log('Selection: ' + suggestion);
});
```

**NOTE**: Every event does not supply the same arguments. See the event
descriptions above for details on each event's argument list.

<!-- section links -->

[`change` event]: https://developer.mozilla.org/en-US/docs/Web/Events/change

### Class Names

* `input` - Added to input that's initialized into a typeahead. Defaults to 
  `tt-input`.

* `hint` - Added to hint input. Defaults to `tt-hint`.

* `menu` - Added to menu element. Defaults to `tt-menu`.

* `dataset` - Added to dataset elements. to Defaults to `tt-dataset`.

* `suggestion` - Added to suggestion elements. Defaults to `tt-suggestion`.

* `empty` - Added to menu element when it contains no content. Defaults to 
  `tt-empty`.

* `open` - Added to menu element when it is opened. Defaults to `tt-open`.

* `cursor` - Added to suggestion element when menu cursor moves to said 
  suggestion. Defaults to `tt-cursor`.

* `highlight` - Added to the element that wraps highlighted text. Defaults to 
  `tt-highlight`.

To override any of these defaults, you can use the `classNames` option:

```javascript
$('.typeahead').typeahead({
  classNames: {
    input: 'Typeahead-input',
    hint: 'Typeahead-hint',
    selectable: 'Typeahead-selectable'
  }
});
```
