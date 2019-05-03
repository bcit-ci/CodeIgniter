# Opening an Issue

The GitHub issue tracker is exclusively for opening demonstrable bugs with the library or for discussing/implementing enhancements. If you need general help with Waypoints try searching through existing closed tickets, searching through the [#jquery-waypoints](http://stackoverflow.com/questions/tagged/jquery-waypoints) tag on StackOverflow, or asking your question there using that tag. If you do ask a question on StackOverflow, please follow the guidelines for [asking a good question](http://stackoverflow.com/help/how-to-ask).

If you're opening a ticket for a bug:

- Give a clear explanation of the bug.
- Try to provide a link to a [JSFiddle](http://jsfiddle.net/) or [CodePen](http://codepen.io/) or similar reduced test case.
- If you cannot provide a reduced test case, please provide a link to a live site demonstrating your bug and include in the ticket the relevant Waypoints code.

If you're interested in discussing a possible new feature:

- Search closed tickets for discussions that may have already occurred.
- Open a ticket and let's talk!

# Pull Requests

- Please send the pull request against the master branch.
- Note any tickets that the pull request addresses.
- Add any necessary tests (see below).
- Follow the coding style of the current codebase.

# Tests

Tests are written in [Jasmine](http://jasmine.github.io/) and run through the [testem](https://github.com/airportyh/testem) test runner. To run them locally you'll need to:

- Install, if you haven't already: [PhantomJS](http://phantomjs.org/), node, and [Bower](bower.io).
- `npm install`
- `bower install`

You can then run the tests one time by running `npm test`, or enter TDD mode by running `npm run tdd`.
