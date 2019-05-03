Unit tests, written with [QUnit](http://docs.jquery.com/QUnit), are used to
expose bugs for squashing, prevent bugs from respawning, and suppress new
bugs when adding new features and making changes.

# Running the tests

The simplest way to run the tests is to open `tests/tests.html` in your browser.
The test suites will automatically run themselves and present their results.

To run the tests from the command line, download and install
[PhantomJS](http://phantomjs.org/), and run `run-qunit.js` with it:

    $ cd tests/
    $ phantomjs run-qunit.js tests.html

Failed tests and their failed assertions will be printed to the console.  A
results summary will be printed at the end.

To generate coverage statistics, use [JSCoverage](http://siliconforks.com/jscoverage/)
to instrument the js files:

    $ cd tests/
    $ jscoverage ../js/ ../instrumented/
    $ phantomjs run-qunit.js tests.html

Coverage percentage will be included in the output summary, and a highlighted
line-by-line html file will be generated.

# Shout-out

Thanks to Rod @ While One Fork for the
[CIS guide](http://whileonefork.blogspot.com/2011/10/integrating-javascript-tests-into-cli.html)
on putting the above together.

# Adding tests

Tests go in js files in the `tests/suites/` directory tree.  QUnit organizes
tests into suites called "modules"; there is one module per js file.  If the
tests you are adding do not fit into an existing module, create a new one at
`tests/suites/<new module>.js`, where `<new module>` is a broad yet
descriptive name for the suite.  If tests have many year-specific cases (ie,
behave differently in leap years vs normal years, or have specific buggy
behavior in a certain year), create the module in a new directory,
`tests/suites/<new module>/<year>.js`, where `<new module>` is the decriptive
name and `<year>` is the four-digit year the tests pertain to.

In order for new tests to be run, they must be imported into `tests/tests.html`.
Find the script includes headed by the html comment `<!-- Test suites -->`, and
add a new one to the list which includes the new js files.

# Can I use this?

By all means, please do!  Just note that I stopped working on this structure
once it fit my needs, there's no real support for it, and it may change in the
future.  Otherwise, have at it.
