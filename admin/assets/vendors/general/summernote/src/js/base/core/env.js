import $ from 'jquery';
const isSupportAmd = typeof define === 'function' && define.amd; // eslint-disable-line

/**
 * returns whether font is installed or not.
 *
 * @param {String} fontName
 * @return {Boolean}
 */
function isFontInstalled(fontName) {
  const testFontName = fontName === 'Comic Sans MS' ? 'Courier New' : 'Comic Sans MS';
  const $tester = $('<div>').css({
    position: 'absolute',
    left: '-9999px',
    top: '-9999px',
    fontSize: '200px'
  }).text('mmmmmmmmmwwwwwww').appendTo(document.body);

  const originalWidth = $tester.css('fontFamily', testFontName).width();
  const width = $tester.css('fontFamily', fontName + ',' + testFontName).width();

  $tester.remove();

  return originalWidth !== width;
}

const userAgent = navigator.userAgent;
const isMSIE = /MSIE|Trident/i.test(userAgent);
let browserVersion;
if (isMSIE) {
  let matches = /MSIE (\d+[.]\d+)/.exec(userAgent);
  if (matches) {
    browserVersion = parseFloat(matches[1]);
  }
  matches = /Trident\/.*rv:([0-9]{1,}[.0-9]{0,})/.exec(userAgent);
  if (matches) {
    browserVersion = parseFloat(matches[1]);
  }
}

const isEdge = /Edge\/\d+/.test(userAgent);

let hasCodeMirror = !!window.CodeMirror;
if (!hasCodeMirror && isSupportAmd) {
  // Webpack
  if (typeof __webpack_require__ === 'function') { // eslint-disable-line
    try {
      // If CodeMirror can't be resolved, `require.resolve` will throw an
      // exception and `hasCodeMirror` won't be set to `true`.
      require.resolve('codemirror');
      hasCodeMirror = true;
    } catch (e) {
      // do nothing
    }
  } else if (typeof require !== 'undefined') {
    // Browserify
    if (typeof require.resolve !== 'undefined') {
      try {
        // If CodeMirror can't be resolved, `require.resolve` will throw an
        // exception and `hasCodeMirror` won't be set to `true`.
        require.resolve('codemirror');
        hasCodeMirror = true;
      } catch (e) {
        // do nothing
      }
    // Almond/Require
    } else if (typeof require.specified !== 'undefined') {
      hasCodeMirror = require.specified('codemirror');
    }
  }
}

const isSupportTouch =
  (('ontouchstart' in window) ||
   (navigator.MaxTouchPoints > 0) ||
   (navigator.msMaxTouchPoints > 0));

// [workaround] IE doesn't have input events for contentEditable
// - see: https://goo.gl/4bfIvA
const inputEventName = (isMSIE || isEdge) ? 'DOMCharacterDataModified DOMSubtreeModified DOMNodeInserted' : 'input';

/**
 * @class core.env
 *
 * Object which check platform and agent
 *
 * @singleton
 * @alternateClassName env
 */
export default {
  isMac: navigator.appVersion.indexOf('Mac') > -1,
  isMSIE,
  isEdge,
  isFF: !isEdge && /firefox/i.test(userAgent),
  isPhantom: /PhantomJS/i.test(userAgent),
  isWebkit: !isEdge && /webkit/i.test(userAgent),
  isChrome: !isEdge && /chrome/i.test(userAgent),
  isSafari: !isEdge && /safari/i.test(userAgent),
  browserVersion,
  jqueryVersion: parseFloat($.fn.jquery),
  isSupportAmd,
  isSupportTouch,
  hasCodeMirror,
  isFontInstalled,
  isW3CRangeSupport: !!document.createRange,
  inputEventName
};
