/*
 * typeahead.js
 * https://github.com/twitter/typeahead.js
 * Copyright 2013-2014 Twitter, Inc. and other contributors; Licensed MIT
 */

// inspired by https://github.com/jharding/lru-cache

var LruCache = (function() {
  'use strict';

  function LruCache(maxSize) {
    this.maxSize = _.isNumber(maxSize) ? maxSize : 100;
    this.reset();

    // if max size is less than 0, provide a noop cache
    if (this.maxSize <= 0) {
      this.set = this.get = $.noop;
    }
  }

  _.mixin(LruCache.prototype, {
    set: function set(key, val) {
      var tailItem = this.list.tail, node;

      // at capacity
      if (this.size >= this.maxSize) {
        this.list.remove(tailItem);
        delete this.hash[tailItem.key];

        this.size--;
      }

      // writing over existing key
      if (node = this.hash[key]) {
        node.val = val;
        this.list.moveToFront(node);
      }

      // new key
      else {
        node = new Node(key, val);

        this.list.add(node);
        this.hash[key] = node;

        this.size++;
      }
    },

    get: function get(key) {
      var node = this.hash[key];

      if (node) {
        this.list.moveToFront(node);
        return node.val;
      }
    },

    reset: function reset() {
      this.size = 0;
      this.hash = {};
      this.list = new List();
    }
  });

  function List() {
    this.head = this.tail = null;
  }

  _.mixin(List.prototype, {
    add: function add(node) {
      if (this.head) {
        node.next = this.head;
        this.head.prev = node;
      }

      this.head = node;
      this.tail = this.tail || node;
    },

    remove: function remove(node) {
      node.prev ? node.prev.next = node.next : this.head = node.next;
      node.next ? node.next.prev = node.prev : this.tail = node.prev;
    },

    moveToFront: function(node) {
      this.remove(node);
      this.add(node);
    }
  });

  function Node(key, val) {
    this.key = key;
    this.val = val;
    this.prev = this.next = null;
  }

  return LruCache;

})();
