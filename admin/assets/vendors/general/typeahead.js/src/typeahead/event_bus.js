/*
 * typeahead.js
 * https://github.com/twitter/typeahead.js
 * Copyright 2013-2014 Twitter, Inc. and other contributors; Licensed MIT
 */

var EventBus = (function() {
  'use strict';

  var namespace, deprecationMap;

  namespace = 'typeahead:';

  // DEPRECATED: will be remove in v1
  //
  // NOTE: there is no deprecation plan for the opened and closed event
  // as their behavior has changed enough that it wouldn't make sense
  deprecationMap = {
    render: 'rendered',
    cursorchange: 'cursorchanged',
    select: 'selected',
    autocomplete: 'autocompleted'
  };

  // constructor
  // -----------

  function EventBus(o) {
    if (!o || !o.el) {
      $.error('EventBus initialized without el');
    }

    this.$el = $(o.el);
  }

  // instance methods
  // ----------------

  _.mixin(EventBus.prototype, {

    // ### private

    _trigger: function(type, args) {
      var $e;

      $e = $.Event(namespace + type);
      (args = args || []).unshift($e);

      this.$el.trigger.apply(this.$el, args);

      return $e;
    },

    // ### public

    before: function(type) {
      var args, $e;

      args = [].slice.call(arguments, 1);
      $e = this._trigger('before' + type, args);

      return $e.isDefaultPrevented();
    },

    trigger: function(type) {
      var deprecatedType;

      this._trigger(type, [].slice.call(arguments, 1));

      // TODO: remove in v1
      if (deprecatedType = deprecationMap[type]) {
        this._trigger(deprecatedType, [].slice.call(arguments, 1));
      }
    }
  });

  return EventBus;
})();
