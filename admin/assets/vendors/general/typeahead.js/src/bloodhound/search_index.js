/*
 * typeahead.js
 * https://github.com/twitter/typeahead.js
 * Copyright 2013-2014 Twitter, Inc. and other contributors; Licensed MIT
 */

var SearchIndex = window.SearchIndex = (function() {
  'use strict';

  var CHILDREN = 'c', IDS = 'i';

  // constructor
  // -----------

  function SearchIndex(o) {
    o = o || {};

    if (!o.datumTokenizer || !o.queryTokenizer) {
      $.error('datumTokenizer and queryTokenizer are both required');
    }

    this.identify = o.identify || _.stringify;
    this.datumTokenizer = o.datumTokenizer;
    this.queryTokenizer = o.queryTokenizer;

    this.reset();
  }

  // instance methods
  // ----------------

  _.mixin(SearchIndex.prototype, {

    // ### public

    bootstrap: function bootstrap(o) {
      this.datums = o.datums;
      this.trie = o.trie;
    },

    add: function(data) {
      var that = this;

      data = _.isArray(data) ? data : [data];

      _.each(data, function(datum) {
        var id, tokens;

        that.datums[id = that.identify(datum)] = datum;
        tokens = normalizeTokens(that.datumTokenizer(datum));

        _.each(tokens, function(token) {
          var node, chars, ch;

          node = that.trie;
          chars = token.split('');

          while (ch = chars.shift()) {
            node = node[CHILDREN][ch] || (node[CHILDREN][ch] = newNode());
            node[IDS].push(id);
          }
        });
      });
    },

    get: function get(ids) {
      var that = this;

      return _.map(ids, function(id) { return that.datums[id]; });
    },

    search: function search(query) {
      var that = this, tokens, matches;

      tokens = normalizeTokens(this.queryTokenizer(query));

      _.each(tokens, function(token) {
        var node, chars, ch, ids;

        // previous tokens didn't share any matches
        if (matches && matches.length === 0) {
          return false;
        }

        node = that.trie;
        chars = token.split('');

        while (node && (ch = chars.shift())) {
          node = node[CHILDREN][ch];
        }

        if (node && chars.length === 0) {
          ids = node[IDS].slice(0);
          matches = matches ? getIntersection(matches, ids) : ids;
        }

        // break early if we find out there are no possible matches
        else {
          matches = [];
          return false;
        }
      });

      return matches ?
        _.map(unique(matches), function(id) { return that.datums[id]; }) : [];
    },

    all: function all() {
      var values = [];

      for (var key in this.datums) {
        values.push(this.datums[key]);
      }

      return values;
    },

    reset: function reset() {
      this.datums = {};
      this.trie = newNode();
    },

    serialize: function serialize() {
      return { datums: this.datums, trie: this.trie };
    }
  });

  return SearchIndex;

  // helper functions
  // ----------------

  function normalizeTokens(tokens) {
   // filter out falsy tokens
    tokens = _.filter(tokens, function(token) { return !!token; });

    // normalize tokens
    tokens = _.map(tokens, function(token) { return token.toLowerCase(); });

    return tokens;
  }

  function newNode() {
    var node = {};

    node[IDS] = [];
    node[CHILDREN] = {};

    return node;
  }

  function unique(array) {
    var seen = {}, uniques = [];

    for (var i = 0, len = array.length; i < len; i++) {
      if (!seen[array[i]]) {
        seen[array[i]] = true;
        uniques.push(array[i]);
      }
    }

    return uniques;
  }

  function getIntersection(arrayA, arrayB) {
    var ai = 0, bi = 0, intersection = [];

    arrayA = arrayA.sort();
    arrayB = arrayB.sort();

    var lenArrayA = arrayA.length, lenArrayB = arrayB.length;

    while (ai < lenArrayA && bi < lenArrayB) {
      if (arrayA[ai] < arrayB[bi]) {
        ai++;
      }

      else if (arrayA[ai] > arrayB[bi]) {
        bi++;
      }

      else {
        intersection.push(arrayA[ai]);
        ai++;
        bi++;
      }
    }

    return intersection;
  }
})();
