/*
 * typeahead.js
 * https://github.com/twitter/typeahead.js
 * Copyright 2013-2014 Twitter, Inc. and other contributors; Licensed MIT
 */

var oParser = (function() {
  'use strict';

  return function parse(o) {
    var defaults, sorter;

    defaults = {
      initialize: true,
      identify: _.stringify,
      datumTokenizer: null,
      queryTokenizer: null,
      sufficient: 5,
      sorter: null,
      local: [],
      prefetch: null,
      remote: null
    };

    o = _.mixin(defaults, o || {});

    // throw error if required options are not set
    !o.datumTokenizer && $.error('datumTokenizer is required');
    !o.queryTokenizer && $.error('queryTokenizer is required');

    sorter = o.sorter;
    o.sorter = sorter ? function(x) { return x.sort(sorter); } : _.identity;

    o.local = _.isFunction(o.local) ? o.local() : o.local;
    o.prefetch = parsePrefetch(o.prefetch);
    o.remote = parseRemote(o.remote);

    return o;
  };

  function parsePrefetch(o) {
    var defaults;

    if (!o) { return null; }

    defaults = {
      url: null,
      ttl: 24 * 60 * 60 * 1000, // 1 day
      cache: true,
      cacheKey: null,
      thumbprint: '',
      prepare: _.identity,
      transform: _.identity,
      transport: null
    };

    // support basic (url) and advanced configuration
    o = _.isString(o) ? { url: o } : o;
    o = _.mixin(defaults, o);

    // throw error if required options are not set
    !o.url && $.error('prefetch requires url to be set');

    // DEPRECATED: filter will be dropped in v1
    o.transform = o.filter || o.transform;

    o.cacheKey = o.cacheKey || o.url;
    o.thumbprint = VERSION + o.thumbprint;
    o.transport = o.transport ? callbackToDeferred(o.transport) : $.ajax;

    return o;
  }

  function parseRemote(o) {
    var defaults;

    if (!o) { return; }

    defaults = {
      url: null,
      cache: true, // leave undocumented
      prepare: null,
      replace: null,
      wildcard: null,
      limiter: null,
      rateLimitBy: 'debounce',
      rateLimitWait: 300,
      transform: _.identity,
      transport: null
    };

    // support basic (url) and advanced configuration
    o = _.isString(o) ? { url: o } : o;
    o = _.mixin(defaults, o);

    // throw error if required options are not set
    !o.url && $.error('remote requires url to be set');

    // DEPRECATED: filter will be dropped in v1
    o.transform = o.filter || o.transform;

    o.prepare = toRemotePrepare(o);
    o.limiter = toLimiter(o);
    o.transport = o.transport ? callbackToDeferred(o.transport) : $.ajax;

    delete o.replace;
    delete o.wildcard;
    delete o.rateLimitBy;
    delete o.rateLimitWait;

    return o;
  }

  function toRemotePrepare(o) {
    var prepare, replace, wildcard;

    prepare = o.prepare;
    replace = o.replace;
    wildcard = o.wildcard;

    if (prepare) { return prepare; }

    if (replace) {
      prepare = prepareByReplace;
    }

    else if (o.wildcard) {
      prepare = prepareByWildcard;
    }

    else {
      prepare = idenityPrepare;
    }

    return prepare;

    function prepareByReplace(query, settings) {
      settings.url = replace(settings.url, query);
      return settings;
    }

    function prepareByWildcard(query, settings) {
      settings.url = settings.url.replace(wildcard, encodeURIComponent(query));
      return settings;
    }

    function idenityPrepare(query, settings) {
      return settings;
    }
  }

  function toLimiter(o) {
    var limiter, method, wait;

    limiter = o.limiter;
    method = o.rateLimitBy;
    wait = o.rateLimitWait;

    if (!limiter) {
      limiter = /^throttle$/i.test(method) ? throttle(wait) : debounce(wait);
    }

    return limiter;

    function debounce(wait) {
      return function debounce(fn) { return _.debounce(fn, wait); };
    }

    function throttle(wait) {
      return function throttle(fn) { return _.throttle(fn, wait); };
    }
  }

  function callbackToDeferred(fn) {
    return function wrapper(o) {
      var deferred = $.Deferred();

      fn(o, onSuccess, onError);

      return deferred;

      function onSuccess(resp) {
        // defer in case fn is synchronous, otherwise done
        // and always handlers will be attached after the resolution
        _.defer(function() { deferred.resolve(resp); });
      }

      function onError(err) {
        // defer in case fn is synchronous, otherwise done
        // and always handlers will be attached after the resolution
        _.defer(function() { deferred.reject(err); });
      }
    };
  }
})();
