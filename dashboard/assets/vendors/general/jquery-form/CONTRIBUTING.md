# Contributing to jQuery Form

Want to contribute to jQuery Form? That's great! Contributions are most welcome!
Here are a couple of guidelines that will help you contribute. Before we get started: Please note that this project is released with a [Contributor Code of Conduct](CODE_OF_CONDUCT.md) to ensure that this project is a welcoming place for **everyone** to contribute to. By participating in this project you agree to abide by its terms.

#### Overview

* [Contribution workflow](#contribution-workflow)
* [Testing](#testing)
* [Reporting a bug](#reporting-a-bug)
* [Contributing to an existing issue](#contributing-to-an-existing-issue)
* [Feature Requests](#feature-requests)
* [Additional info](#additional-info)

## Contribution workflow

1. Fork the repository in GitHub with the `Fork` button.
2. Switch to a new branch (ie. `new-feature`), and work from there:  
  `git checkout -b new-feature`
3. Make your feature addition or bug fix.
4. After setting up your [testing enviroment](#testing), run the tests:

  ```shell
  grunt test
  ```

  If the tests all pass, move on to step 5.

5. Send a pull request (PR). Bonus points for topic branches.
  * Please make sure all of your commits are atomic (one feature per commit).
  * Use sensible commit messages.
    * Always write a clear log message for your commits. One-line messages are fine for small changes, but bigger changes should look like this:
    ```shell
    $ git commit -m "A brief summary of the commit"
    >
    > A paragraph describing what changed and its impact."
    ```
  * If your PR fixes a separate issue number, include it in the commit message.

### Things to keep in mind

* Smaller PRs are likely to be merged more quickly than bigger changes.
* If it is a useful PR, it **will** get merged in eventually.
* This project is using [Semantic Versioning 2.0.0](http://semver.org/)

## Testing

jQuery Form uses [Node.js](https://nodejs.org/), [Grunt](https://gruntjs.com/), [ESLint](http://eslint.org/), [Mocha](https://mochajs.org/), and [Chai](http://chaijs.com/) to automate the building and validation of source code. Here is how to set that up:

1. Get [Node.js](https://nodejs.org/) (includes [NPM](https://www.npmjs.com/), necessary for the next step)
2. Install Grunt CLI:

  ```shell
  npm install -g grunt-cli
  ```

3. Install dependencies:

  ```shell
  npm install
  ```

4. Run the tests by opening `test/test.html` in your web browser or using Grunt:

  ```shell
  grunt test
  ```

## Reporting a bug

So you've found a bug, and want to help us fix it? Before filing a bug report, please double-check the bug hasn't already been reported. You can do so [on our issue tracker](https://github.com/jquery-form/form/issues?q=is%3Aopen+is%3Aissue). If something hasn't been raised, you can go ahead and create a new issue with the following information:

* Which version of the plugin are you using?
* Which version of the jQuery library are you using?
* What browsers (and versions) have you tested in?
* How can the error be reproduced?
* If possible, include a link to a [JSFiddle](https://jsfiddle.net/) or [CodePen](https://codepen.io/) example of the error.

If you want to be really thorough, there is a great overview on Stack Overflow of [what you should consider when reporting a bug](https://stackoverflow.com/questions/240323/how-to-report-bugs-the-smart-way).

It goes without saying that you're welcome to help investigate further and/or find a fix for the bug. If you want to do so, just mention it in your bug report and offer your help!

## Contributing to an existing issue

### Finding an issue to work on

We've got a few open issues and are always glad to get help on that front. You can view the list of issues [here](https://github.com/jquery-form/form/issues). (Here's [a good article](https://medium.freecodecamp.com/finding-your-first-open-source-project-or-bug-to-work-on-1712f651e5ba) on how to find your first bug to fix).

Before getting to work, take a look at the issue and at the conversation around it. Has someone already offered to work on the issue? Has someone been assigned to the issue? If so, you might want to check with them to see whether they're still actively working on it.

If the issue is a few months old, it might be a good idea to write a short comment to double-check that the issue or feature is still a valid one to jump on.

Feel free to ask for more detail on what is expected: are there any more details or specifications you need to know?
And if at any point you get stuck: don't hesitate to ask for help.

### Making your contribution

We've outlined the contribution workflow [here](#contribution-workflow). If you're a first-timer, don't worry! GitHub has a ton of guides to help you through your first pull request: You can find out more about pull requests [here](https://help.github.com/articles/about-pull-requests/) and about creating a pull request [here](https://help.github.com/articles/creating-a-pull-request/).

## Feature Requests

* You can _request_ a new feature by [submitting an issue](https://github.com/jquery-form/form/issues).
* If you would like to _implement_ a new feature:
  * For a **Major Feature**, first open an issue and outline your proposal so that it can be discussed. This will also allow us to better coordinate our efforts, prevent duplication of work, and help you to craft the change so that it is successfully accepted into the project.
  * **Small Features** can be crafted and directly [submitted as a Pull Request](#contribution-workflow).

## Additional info

Especially if you're a newcomer to Open Source and you've found some little bumps along the way while contributing, we recommend you write about them. [Here](https://medium.freecodecamp.com/new-contributors-to-open-source-please-blog-more-920af14cffd)'s a great article about why writing about your experience is important; this will encourage other beginners to try their luck at Open Source, too!
