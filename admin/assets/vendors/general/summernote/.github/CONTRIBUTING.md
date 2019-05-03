## Contributing
* Pull requests are welcome
* Please `don't include dist/* files` on your commits.

## Coding convention
* eslint: https://eslint.org
* eslint rule: https://github.com/summernote/summernote/blob/master/.eslintrc

## Build summernote
```bash
npm install

# build full version of summernote: dist/summernote.js
npm run build

# generate minified copy: dist/summernote.min.js, dist/summernote.css
npm run dist
```
At this point, you should now have a `build/` directory populated with everything you need to use summernote.

## Start local server for developing summernote.
run local server with connect and watch.
```bash
npm run start
# Open a browser on http://localhost:3000.
# If you change source code, automatically reload your page.
```

## Test summernote
run tests with Karma and PhantomJS
```bash
npm run test
```
If you want run tests on other browser,
change the values for `broswers` properties in `karma.conf.js`.

```
karma: {
  all: {
    browsers: ['PhantomJS'],
    reporters: ['progress']
  }
}

```
You can use `Chrome`, `ChromeCanary`, `Firefox`, `Opera`, `Safari`, `PhantomJS` and `IE` beside `PhantomJS`.
Once you run `npm test`, it will watch all javascript file. Therefore karma run tests every time you change code.

## Prepush Hooks
As part of this repo, we use the NPM package husky to implement git hooks. We leverage the prepush hook to prevent bad commits.

## Document structure

```
 - body container: <div class="note-editable">, <td>, <blockquote>, <ul>
 - block node: <div>, <p>, <li>, <h1>, <table>
 - void block node: <hr>
 - inline node: <span>, <b>, <font>, <a>, ...
 - void inline node: <img>
 - text node: #text
```

1. A body container has block node, but `<ul>` has only `<li>` nodes.
2. A body container also has inline nodes sometimes. This inline nodes will be wrapped with `<p>` when enter key pressed.
4. A block node only has inline nodes.
5. A inline nodes has another inline nodes
6. `#text` and void inline node doesn't have children.
