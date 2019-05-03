[![build status](https://secure.travis-ci.org/twitter/typeahead.js.svg?branch=master)](http://travis-ci.org/twitter/typeahead.js)
[![Built with Grunt](https://cdn.gruntjs.com/builtwith.png)](http://gruntjs.com/)


[typeahead.js][gh-page]
=======================

Inspired by [twitter.com]'s autocomplete search functionality, typeahead.js is 
a flexible JavaScript library that provides a strong foundation for building 
robust typeaheads.

The typeahead.js library consists of 2 components: the suggestion engine, 
[Bloodhound], and the UI view, [Typeahead]. 
The suggestion engine is responsible for computing suggestions for a given 
query. The UI view is responsible for rendering suggestions and handling DOM 
interactions. Both components can be used separately, but when used together, 
they can provide a rich typeahead experience.

<!-- section links -->

[gh-page]: http://twitter.github.io/typeahead.js/
[twitter.com]: https://twitter.com
[Bloodhound]: https://github.com/twitter/typeahead.js/blob/master/doc/bloodhound.md
[Typeahead]: https://github.com/twitter/typeahead.js/blob/master/doc/jquery_typeahead.md

Getting Started
---------------

How you acquire typeahead.js is up to you.

Preferred method:
* Install with [Bower]: `$ bower install typeahead.js`

Other methods:
* [Download zipball of latest release][zipball].
* Download the latest dist files individually:
  * *[bloodhound.js]* (standalone suggestion engine)
  * *[typeahead.jquery.js]* (standalone UI view)
  * *[typeahead.bundle.js]* (*bloodhound.js* + *typeahead.jquery.js*)
  * *[typeahead.bundle.min.js]*

**Note:** both *bloodhound.js* and *typeahead.jquery.js* have a dependency on 
[jQuery] 1.9+.

<!-- section links -->

[Bower]: http://bower.io/
[zipball]: http://twitter.github.com/typeahead.js/releases/latest/typeahead.js.zip
[bloodhound.js]: http://twitter.github.com/typeahead.js/releases/latest/bloodhound.js
[typeahead.jquery.js]: http://twitter.github.com/typeahead.js/releases/latest/typeahead.jquery.js
[typeahead.bundle.js]: http://twitter.github.com/typeahead.js/releases/latest/typeahead.bundle.js
[typeahead.bundle.min.js]: http://twitter.github.com/typeahead.js/releases/latest/typeahead.bundle.min.js
[jQuery]: http://jquery.com/

Documentation 
-------------

* [Typeahead Docs]
* [Bloodhound Docs]

[Typeahead Docs]: https://github.com/twitter/typeahead.js/blob/master/doc/jquery_typeahead.md
[Bloodhound Docs]: https://github.com/twitter/typeahead.js/blob/master/doc/bloodhound.md

Examples
--------

For some working examples of typeahead.js, visit the [examples page].

<!-- section links -->

[examples page]: http://twitter.github.io/typeahead.js/examples

Browser Support
---------------

* Chrome
* Firefox 3.5+
* Safari 4+
* Internet Explorer 8+
* Opera 11+

**NOTE:** typeahead.js is not tested on mobile browsers.

Customer Support
----------------

For general questions about typeahead.js, tweet at [@typeahead].

For technical questions, you should post a question on [Stack Overflow] and tag 
it with [typeahead.js][so tag].

<!-- section links -->

[Stack Overflow]: http://stackoverflow.com/
[@typeahead]: https://twitter.com/typeahead
[so tag]: http://stackoverflow.com/questions/tagged/typeahead.js

Issues
------

Discovered a bug? Please create an issue here on GitHub!

https://github.com/twitter/typeahead.js/issues

Versioning
----------

For transparency and insight into our release cycle, releases will be numbered 
with the following format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

* Breaking backwards compatibility bumps the major
* New additions without breaking backwards compatibility bumps the minor
* Bug fixes and misc changes bump the patch

For more information on semantic versioning, please visit http://semver.org/.

Testing
-------

Tests are written using [Jasmine] and ran with [Karma]. To run
the test suite with PhantomJS, run `$ npm test`.

<!-- section links -->

[Jasmine]: http://jasmine.github.io/
[Karma]: http://karma-runner.github.io/

Developers
----------

If you plan on contributing to typeahead.js, be sure to read the 
[contributing guidelines]. A good starting place for new contributors are issues
labeled with [entry-level]. Entry-level issues tend to require minor changes 
and provide developers a chance to get more familiar with typeahead.js before
taking on more challenging work.

In order to build and test typeahead.js, you'll need to install its dev 
dependencies (`$ npm install`) and have [grunt-cli] 
installed (`$ npm install -g grunt-cli`). Below is an overview of the available 
Grunt tasks that'll be useful in development.

* `grunt build` – Builds *typeahead.js* from source.
* `grunt lint` – Runs source and test files through JSHint.
* `grunt watch` – Rebuilds *typeahead.js* whenever a source file is modified.
* `grunt server` – Serves files from the root of typeahead.js on localhost:8888. 
  Useful for using *test/playground.html* for debugging/testing.
* `grunt dev` – Runs `grunt watch` and `grunt server` in parallel.

<!-- section links -->

[contributing guidelines]: https://github.com/twitter/typeahead.js/blob/master/CONTRIBUTING.md
[entry-level]: https://github.com/twitter/typeahead.js/issues?&labels=entry-level&state=open
[grunt-cli]: https://github.com/gruntjs/grunt-cli

Maintainers
-----------

* **Jake Harding** 
  * [@JakeHarding](https://twitter.com/JakeHarding) 
  * [GitHub](https://github.com/jharding)

* **You?**

Authors
-------

* **Jake Harding** 
  * [@JakeHarding](https://twitter.com/JakeHarding) 
  * [GitHub](https://github.com/jharding)

* **Veljko Skarich**
  * [@vskarich](https://twitter.com/vskarich) 
  * [GitHub](https://github.com/vskarich)

* **Tim Trueman**
  * [@timtrueman](https://twitter.com/timtrueman) 
  * [GitHub](https://github.com/timtrueman)

License
-------

Copyright 2013 Twitter, Inc.

Licensed under the MIT License
