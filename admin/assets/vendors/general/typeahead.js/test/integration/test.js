/* jshint esnext: true, evil: true, sub: true */

var wd = require('yiewd'),
    colors = require('colors'),
    expect = require('chai').expect,
    _ = require('underscore'),
    f = require('util').format,
    env = process.env;

var browser, caps;

browser = (process.env.BROWSER || 'chrome').split(':');

caps = {
  name: f('[%s] typeahead.js ui', browser.join(' , ')),
  browserName: browser[0]
};

setIf(caps, 'version', browser[1]);
setIf(caps, 'platform', browser[2]);
setIf(caps, 'tunnel-identifier', env['TRAVIS_JOB_NUMBER']);
setIf(caps, 'build', env['TRAVIS_BUILD_NUMBER']);
setIf(caps, 'tags', env['CI'] ? ['CI'] : ['local']);

function setIf(obj, key, val) {
  val && (obj[key] = val);
}

describe('jquery-typeahead.js', function() {
  var driver, body, input, hint, dropdown, allPassed = true;

  this.timeout(300000);

  before(function(done) {
    var host = 'ondemand.saucelabs.com', port = 80, username, password;

    if (env['CI']) {
      host = 'localhost';
      port = 4445;
      username = env['SAUCE_USERNAME'];
      password = env['SAUCE_ACCESS_KEY'];
    }

    driver = wd.remote(host, port, username, password);
    driver.configureHttp({
      timeout: 30000,
      retries: 5,
      retryDelay: 200
    });

    driver.on('status', function(info) {
      console.log(info.cyan);
    });

    driver.on('command', function(meth, path, data) {
      console.log(' > ' + meth.yellow, path.grey, data || '');
    });

    driver.run(function*() {
      yield this.init(caps);
      yield this.get('http://localhost:8888/test/integration/test.html');

      body = yield this.elementByTagName('body');
      input = yield this.elementById('states');
      hint = yield this.elementByClassName('tt-hint');
      dropdown = yield this.elementByClassName('tt-menu');

      done();
    });
  });

  afterEach(function(done) {
    allPassed = allPassed && (this.currentTest.state === 'passed');

    driver.run(function*() {
      yield body.click();
      yield this.execute('window.jQuery("#states").typeahead("val", "")');
      done();
    });
  });

  after(function(done) {
    driver.run(function*() {
      yield this.quit();
      yield driver.sauceJobStatus(allPassed);
      done();
    });
  });

  describe('on blur', function() {
    it('should close dropdown', function(done) {
      driver.run(function*() {
        yield input.click();
        yield input.type('mi');
        expect(yield dropdown.isDisplayed()).to.equal(true);

        yield body.click();
        expect(yield dropdown.isDisplayed()).to.equal(false);

        done();
      });
    });

    it('should clear hint', function(done) {
      driver.run(function*() {
        yield input.click();
        yield input.type('mi');
        expect(yield hint.getValue()).to.equal('michigan');

        yield body.click();
        expect(yield hint.getValue()).to.equal('');

        done();
      });
    });
  });

  describe('on query change', function() {
    it('should open dropdown if suggestions', function(done) {
      driver.run(function*() {
        yield input.click();
        yield input.type('mi');

        expect(yield dropdown.isDisplayed()).to.equal(true);

        done();
      });
    });

    it('should close dropdown if no suggestions', function(done) {
      driver.run(function*() {
        yield input.click();
        yield input.type('huh?');

        expect(yield dropdown.isDisplayed()).to.equal(false);

        done();
      });
    });

    it('should render suggestions if suggestions', function(done) {
      driver.run(function*() {
        var suggestions;

        yield input.click();
        yield input.type('mi');

        suggestions = yield dropdown.elementsByClassName('tt-suggestion');

        expect(suggestions).to.have.length('4');
        expect(yield suggestions[0].text()).to.equal('Michigan');
        expect(yield suggestions[1].text()).to.equal('Minnesota');
        expect(yield suggestions[2].text()).to.equal('Mississippi');
        expect(yield suggestions[3].text()).to.equal('Missouri');

        done();
      });
    });

    it('should show hint if top suggestion is a match', function(done) {
      driver.run(function*() {
        yield input.click();
        yield input.type('mi');

        expect(yield hint.getValue()).to.equal('michigan');

        done();
      });
    });

    it('should match hint to query', function(done) {
      driver.run(function*() {
        yield input.click();
        yield input.type('NeW    JE');

        expect(yield hint.getValue()).to.equal('NeW    JErsey');

        done();
      });
    });

    it('should not show hint if top suggestion is not a match', function(done) {
      driver.run(function*() {
        yield input.click();
        yield input.type('ham');

        expect(yield hint.getValue()).to.equal('');

        done();
      });
    });

    it('should not show hint if there is query overflow', function(done) {
      driver.run(function*() {
        yield input.click();
        yield input.type('this    is    a very long    value     so ');

        expect(yield hint.getValue()).to.equal('');

        done();
      });
    });
  });

  describe('on up arrow', function() {
    it('should cycle through suggestions', function(done) {
      driver.run(function*() {
        var suggestions;

        yield input.click();
        yield input.type('mi');

        suggestions = yield dropdown.elementsByClassName('tt-suggestion');

        yield input.type(wd.SPECIAL_KEYS['Up arrow']);
        expect(yield input.getValue()).to.equal('Missouri');
        expect(yield suggestions[3].getAttribute('class')).to.equal('tt-suggestion tt-selectable tt-cursor');

        yield input.type(wd.SPECIAL_KEYS['Up arrow']);
        expect(yield input.getValue()).to.equal('Mississippi');
        expect(yield suggestions[2].getAttribute('class')).to.equal('tt-suggestion tt-selectable tt-cursor');

        yield input.type(wd.SPECIAL_KEYS['Up arrow']);
        expect(yield input.getValue()).to.equal('Minnesota');
        expect(yield suggestions[1].getAttribute('class')).to.equal('tt-suggestion tt-selectable tt-cursor');

        yield input.type(wd.SPECIAL_KEYS['Up arrow']);
        expect(yield input.getValue()).to.equal('Michigan');
        expect(yield suggestions[0].getAttribute('class')).to.equal('tt-suggestion tt-selectable tt-cursor');

        yield input.type(wd.SPECIAL_KEYS['Up arrow']);
        expect(yield input.getValue()).to.equal('mi');
        expect(yield suggestions[0].getAttribute('class')).to.equal('tt-suggestion tt-selectable');
        expect(yield suggestions[1].getAttribute('class')).to.equal('tt-suggestion tt-selectable');
        expect(yield suggestions[2].getAttribute('class')).to.equal('tt-suggestion tt-selectable');
        expect(yield suggestions[3].getAttribute('class')).to.equal('tt-suggestion tt-selectable');

        done();
      });
    });
  });

  describe('on down arrow', function() {
    it('should cycle through suggestions', function(done) {
      driver.run(function*() {
        var suggestions;

        yield input.click();
        yield input.type('mi');

        suggestions = yield dropdown.elementsByClassName('tt-suggestion');

        yield input.type(wd.SPECIAL_KEYS['Down arrow']);
        expect(yield input.getValue()).to.equal('Michigan');
        expect(yield suggestions[0].getAttribute('class')).to.equal('tt-suggestion tt-selectable tt-cursor');

        yield input.type(wd.SPECIAL_KEYS['Down arrow']);
        expect(yield input.getValue()).to.equal('Minnesota');
        expect(yield suggestions[1].getAttribute('class')).to.equal('tt-suggestion tt-selectable tt-cursor');

        yield input.type(wd.SPECIAL_KEYS['Down arrow']);
        expect(yield input.getValue()).to.equal('Mississippi');
        expect(yield suggestions[2].getAttribute('class')).to.equal('tt-suggestion tt-selectable tt-cursor');

        yield input.type(wd.SPECIAL_KEYS['Down arrow']);
        expect(yield input.getValue()).to.equal('Missouri');
        expect(yield suggestions[3].getAttribute('class')).to.equal('tt-suggestion tt-selectable tt-cursor');

        yield input.type(wd.SPECIAL_KEYS['Down arrow']);
        expect(yield input.getValue()).to.equal('mi');
        expect(yield suggestions[0].getAttribute('class')).to.equal('tt-suggestion tt-selectable');
        expect(yield suggestions[1].getAttribute('class')).to.equal('tt-suggestion tt-selectable');
        expect(yield suggestions[2].getAttribute('class')).to.equal('tt-suggestion tt-selectable');
        expect(yield suggestions[3].getAttribute('class')).to.equal('tt-suggestion tt-selectable');

        done();
      });
    });
  });

  describe('on escape', function() {
    it('should close dropdown', function(done) {
      driver.run(function*() {
        yield input.click();
        yield input.type('mi');
        expect(yield dropdown.isDisplayed()).to.equal(true);

        yield input.type(wd.SPECIAL_KEYS['Escape']);
        expect(yield dropdown.isDisplayed()).to.equal(false);

        done();
      });
    });

    it('should clear hint', function(done) {
      driver.run(function*() {
        yield input.click();
        yield input.type('mi');
        expect(yield hint.getValue()).to.equal('michigan');

        yield input.type(wd.SPECIAL_KEYS['Escape']);
        expect(yield hint.getValue()).to.equal('');

        done();
      });
    });
  });

  describe('on tab', function() {
    it('should autocomplete if hint is present', function(done) {
      driver.run(function*() {
        yield input.click();
        yield input.type('mi');

        yield input.type(wd.SPECIAL_KEYS['Tab']);
        expect(yield input.getValue()).to.equal('Michigan');

        done();
      });
    });

    it('should select if cursor is on suggestion', function(done) {
      driver.run(function*() {
        var suggestions;

        yield input.click();
        yield input.type('mi');

        suggestions = yield dropdown.elementsByClassName('tt-suggestion');
        yield input.type(wd.SPECIAL_KEYS['Down arrow']);
        yield input.type(wd.SPECIAL_KEYS['Down arrow']);
        yield input.type(wd.SPECIAL_KEYS['Tab']);

        expect(yield dropdown.isDisplayed()).to.equal(false);
        expect(yield input.getValue()).to.equal('Minnesota');

        done();
      });
    });
  });

  describe('on right arrow', function() {
    it('should autocomplete if hint is present', function(done) {
      driver.run(function*() {
        yield input.click();
        yield input.type('mi');

        yield input.type(wd.SPECIAL_KEYS['Right arrow']);
        expect(yield input.getValue()).to.equal('Michigan');

        done();
      });
    });
  });

  describe('on suggestion click', function() {
    it('should select suggestion', function(done) {
      driver.run(function*() {
        var suggestions;

        yield input.click();
        yield input.type('mi');

        suggestions = yield dropdown.elementsByClassName('tt-suggestion');
        yield suggestions[1].click();

        expect(yield dropdown.isDisplayed()).to.equal(false);
        expect(yield input.getValue()).to.equal('Minnesota');

        done();
      });
    });
  });

  describe('on enter', function() {
    it('should select if cursor is on suggestion', function(done) {
      driver.run(function*() {
        var suggestions;

        yield input.click();
        yield input.type('mi');

        suggestions = yield dropdown.elementsByClassName('tt-suggestion');
        yield input.type(wd.SPECIAL_KEYS['Down arrow']);
        yield input.type(wd.SPECIAL_KEYS['Down arrow']);
        yield input.type(wd.SPECIAL_KEYS['Return']);

        expect(yield dropdown.isDisplayed()).to.equal(false);
        expect(yield input.getValue()).to.equal('Minnesota');

        done();
      });
    });
  });
});
