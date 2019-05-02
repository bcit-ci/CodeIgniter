/*
 * typeahead.js
 * https://github.com/twitter/typeahead.js
 * Copyright 2013-2014 Twitter, Inc. and other contributors; Licensed MIT
 */

var PersistentStorage = (function() {
  'use strict';

  var LOCAL_STORAGE;

  try {
    LOCAL_STORAGE = window.localStorage;

    // while in private browsing mode, some browsers make
    // localStorage available, but throw an error when used
    LOCAL_STORAGE.setItem('~~~', '!');
    LOCAL_STORAGE.removeItem('~~~');
  } catch (err) {
    LOCAL_STORAGE = null;
  }

  // constructor
  // -----------

  function PersistentStorage(namespace, override) {
    this.prefix = ['__', namespace, '__'].join('');
    this.ttlKey = '__ttl__';
    this.keyMatcher = new RegExp('^' + _.escapeRegExChars(this.prefix));

    // for testing purpose
    this.ls = override || LOCAL_STORAGE;

    // if local storage isn't available, everything becomes a noop
    !this.ls && this._noop();
  }

  // instance methods
  // ----------------

  _.mixin(PersistentStorage.prototype, {
    // ### private

    _prefix: function(key) {
      return this.prefix + key;
    },

    _ttlKey: function(key) {
      return this._prefix(key) + this.ttlKey;
    },

    _noop: function() {
      this.get =
      this.set =
      this.remove =
      this.clear =
      this.isExpired = _.noop;
    },

    _safeSet: function(key, val) {
      try {
        this.ls.setItem(key, val);
      } catch (err) {
        // hit the localstorage limit so clean up and better luck next time
        if (err.name === 'QuotaExceededError') {
          this.clear();
          this._noop();
        }
      }
    },

    // ### public

    get: function(key) {
      if (this.isExpired(key)) {
        this.remove(key);
      }

      return decode(this.ls.getItem(this._prefix(key)));
    },

    set: function(key, val, ttl) {
      if (_.isNumber(ttl)) {
        this._safeSet(this._ttlKey(key), encode(now() + ttl));
      }

      else {
        this.ls.removeItem(this._ttlKey(key));
      }

      return this._safeSet(this._prefix(key), encode(val));
    },

    remove: function(key) {
      this.ls.removeItem(this._ttlKey(key));
      this.ls.removeItem(this._prefix(key));

      return this;
    },

    clear: function() {
      var i, keys = gatherMatchingKeys(this.keyMatcher);

      for (i = keys.length; i--;) {
        this.remove(keys[i]);
      }

      return this;
    },

    isExpired: function(key) {
      var ttl = decode(this.ls.getItem(this._ttlKey(key)));

      return _.isNumber(ttl) && now() > ttl ? true : false;
    }
  });

  return PersistentStorage;

  // helper functions
  // ----------------

  function now() {
    return new Date().getTime();
  }

  function encode(val) {
    // convert undefined to null to avoid issues with JSON.parse
    return JSON.stringify(_.isUndefined(val) ? null : val);
  }

  function decode(val) {
    return $.parseJSON(val);
  }

  function gatherMatchingKeys(keyMatcher) {
    var i, key, keys = [], len = LOCAL_STORAGE.length;

    for (i = 0; i < len; i++) {
      if ((key = LOCAL_STORAGE.key(i)).match(keyMatcher)) {
        keys.push(key.replace(keyMatcher, ''));
      }
    }

    return keys;
  }
})();
