/*
 * typeahead.js
 * https://github.com/twitter/typeahead.js
 * Copyright 2013-2014 Twitter, Inc. and other contributors; Licensed MIT
 */

var Prefetch = (function() {
  'use strict';

  var keys;

  keys = { data: 'data', protocol: 'protocol', thumbprint: 'thumbprint' };

  // constructor
  // -----------

  // defaults for options are handled in options_parser
  function Prefetch(o) {
    this.url = o.url;
    this.ttl = o.ttl;
    this.cache = o.cache;
    this.prepare = o.prepare;
    this.transform = o.transform;
    this.transport = o.transport;
    this.thumbprint = o.thumbprint;

    this.storage = new PersistentStorage(o.cacheKey);
  }

  // instance methods
  // ----------------

  _.mixin(Prefetch.prototype, {

    // ### private

    _settings: function settings() {
      return { url: this.url, type: 'GET', dataType: 'json' };
    },

    // ### public

    store: function store(data) {
      if (!this.cache) { return; }

      this.storage.set(keys.data, data, this.ttl);
      this.storage.set(keys.protocol, location.protocol, this.ttl);
      this.storage.set(keys.thumbprint, this.thumbprint, this.ttl);
    },

    fromCache: function fromCache() {
      var stored = {}, isExpired;

      if (!this.cache) { return null; }

      stored.data = this.storage.get(keys.data);
      stored.protocol = this.storage.get(keys.protocol);
      stored.thumbprint = this.storage.get(keys.thumbprint);

      // the stored data is considered expired if the thumbprints
      // don't match or if the protocol it was originally stored under
      // has changed
      isExpired =
        stored.thumbprint !== this.thumbprint ||
        stored.protocol !== location.protocol;

      // TODO: if expired, remove from local storage

      return stored.data && !isExpired ? stored.data : null;
    },

    fromNetwork: function(cb) {
      var that = this, settings;

      if (!cb) { return; }

      settings = this.prepare(this._settings());
      this.transport(settings).fail(onError).done(onResponse);

      function onError() { cb(true); }
      function onResponse(resp) { cb(null, that.transform(resp)); }
    },

    clear: function clear() {
      this.storage.clear();
      return this;
    }
  });

  return Prefetch;
})();
