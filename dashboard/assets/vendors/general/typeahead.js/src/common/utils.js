/*
 * typeahead.js
 * https://github.com/twitter/typeahead.js
 * Copyright 2013-2014 Twitter, Inc. and other contributors; Licensed MIT
 */

var _ = (function() {
  'use strict';

  return {
    isMsie: function() {
      // from https://github.com/ded/bowser/blob/master/bowser.js
      return (/(msie|trident)/i).test(navigator.userAgent) ?
        navigator.userAgent.match(/(msie |rv:)(\d+(.\d+)?)/i)[2] : false;
    },

    isBlankString: function(str) { return !str || /^\s*$/.test(str); },

    // http://stackoverflow.com/a/6969486
    escapeRegExChars: function(str) {
      return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&');
    },

    isString: function(obj) { return typeof obj === 'string'; },

    isNumber: function(obj) { return typeof obj === 'number'; },

    isArray: $.isArray,

    isFunction: $.isFunction,

    isObject: $.isPlainObject,

    isUndefined: function(obj) { return typeof obj === 'undefined'; },

    isElement: function(obj) { return !!(obj && obj.nodeType === 1); },

    isJQuery: function(obj) { return obj instanceof $; },

    toStr: function toStr(s) {
      return (_.isUndefined(s) || s === null) ? '' : s + '';
    },

    bind: $.proxy,

    each: function(collection, cb) {
      // stupid argument order for jQuery.each
      $.each(collection, reverseArgs);

      function reverseArgs(index, value) { return cb(value, index); }
    },

    map: $.map,

    filter: $.grep,

    every: function(obj, test) {
      var result = true;

      if (!obj) { return result; }

      $.each(obj, function(key, val) {
        if (!(result = test.call(null, val, key, obj))) {
          return false;
        }
      });

      return !!result;
    },

    some: function(obj, test) {
      var result = false;

      if (!obj) { return result; }

      $.each(obj, function(key, val) {
        if (result = test.call(null, val, key, obj)) {
          return false;
        }
      });

      return !!result;
    },

    mixin: $.extend,

    identity: function(x) { return x; },

    clone: function(obj) { return $.extend(true, {}, obj); },

    getIdGenerator: function() {
      var counter = 0;
      return function() { return counter++; };
    },

    templatify: function templatify(obj) {
      return $.isFunction(obj) ? obj : template;

      function template() { return String(obj); }
    },

    defer: function(fn) { setTimeout(fn, 0); },

    debounce: function(func, wait, immediate) {
      var timeout, result;

      return function() {
        var context = this, args = arguments, later, callNow;

        later = function() {
          timeout = null;
          if (!immediate) { result = func.apply(context, args); }
        };

        callNow = immediate && !timeout;

        clearTimeout(timeout);
        timeout = setTimeout(later, wait);

        if (callNow) { result = func.apply(context, args); }

        return result;
      };
    },

    throttle: function(func, wait) {
      var context, args, timeout, result, previous, later;

      previous = 0;
      later = function() {
        previous = new Date();
        timeout = null;
        result = func.apply(context, args);
      };

      return function() {
        var now = new Date(),
            remaining = wait - (now - previous);

        context = this;
        args = arguments;

        if (remaining <= 0) {
          clearTimeout(timeout);
          timeout = null;
          previous = now;
          result = func.apply(context, args);
        }

        else if (!timeout) {
          timeout = setTimeout(later, remaining);
        }

        return result;
      };
    },

    stringify: function(val) {
      return _.isString(val) ? val : JSON.stringify(val);
    },

    noop: function() {}
  };
})();
