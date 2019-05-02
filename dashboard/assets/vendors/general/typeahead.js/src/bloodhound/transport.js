/*
 * typeahead.js
 * https://github.com/twitter/typeahead.js
 * Copyright 2013-2014 Twitter, Inc. and other contributors; Licensed MIT
 */

var Transport = (function() {
  'use strict';

  var pendingRequestsCount = 0,
      pendingRequests = {},
      maxPendingRequests = 6,
      sharedCache = new LruCache(10);

  // constructor
  // -----------

  function Transport(o) {
    o = o || {};

    this.cancelled = false;
    this.lastReq = null;

    this._send = o.transport;
    this._get = o.limiter ? o.limiter(this._get) : this._get;

    this._cache = o.cache === false ? new LruCache(0) : sharedCache;
  }

  // static methods
  // --------------

  Transport.setMaxPendingRequests = function setMaxPendingRequests(num) {
    maxPendingRequests = num;
  };

  Transport.resetCache = function resetCache() {
    sharedCache.reset();
  };

  // instance methods
  // ----------------

  _.mixin(Transport.prototype, {

    // ### private

    _fingerprint: function fingerprint(o) {
      o = o || {};
      return o.url + o.type + $.param(o.data || {});
    },

    _get: function(o, cb) {
      var that = this, fingerprint, jqXhr;

      fingerprint = this._fingerprint(o);

      // #149: don't make a network request if there has been a cancellation
      // or if the url doesn't match the last url Transport#get was invoked with
      if (this.cancelled || fingerprint !== this.lastReq) { return; }

      // a request is already in progress, piggyback off of it
      if (jqXhr = pendingRequests[fingerprint]) {
        jqXhr.done(done).fail(fail);
      }

      // under the pending request threshold, so fire off a request
      else if (pendingRequestsCount < maxPendingRequests) {
        pendingRequestsCount++;
        pendingRequests[fingerprint] =
          this._send(o).done(done).fail(fail).always(always);
      }

      // at the pending request threshold, so hang out in the on deck circle
      else {
        this.onDeckRequestArgs = [].slice.call(arguments, 0);
      }

      function done(resp) {
        cb(null, resp);
        that._cache.set(fingerprint, resp);
      }

      function fail() {
        cb(true);
      }

      function always() {
        pendingRequestsCount--;
        delete pendingRequests[fingerprint];

        // ensures request is always made for the last query
        if (that.onDeckRequestArgs) {
          that._get.apply(that, that.onDeckRequestArgs);
          that.onDeckRequestArgs = null;
        }
      }
    },

    // ### public

    get: function(o, cb) {
      var resp, fingerprint;

      cb = cb || $.noop;
      o = _.isString(o) ? { url: o } : (o || {});

      fingerprint = this._fingerprint(o);

      this.cancelled = false;
      this.lastReq = fingerprint;

      // in-memory cache hit
      if (resp = this._cache.get(fingerprint)) {
        cb(null, resp);
      }

      // go to network
      else {
        this._get(o, cb);
      }
    },

    cancel: function() {
      this.cancelled = true;
    }
  });

  return Transport;
})();
