/*
 * typeahead.js
 * https://github.com/twitter/typeahead.js
 * Copyright 2013-2014 Twitter, Inc. and other contributors; Licensed MIT
 */

var Input = (function() {
  'use strict';

  var specialKeyCodeMap;

  specialKeyCodeMap = {
    9: 'tab',
    27: 'esc',
    37: 'left',
    39: 'right',
    13: 'enter',
    38: 'up',
    40: 'down'
  };

  // constructor
  // -----------

  function Input(o, www) {
    o = o || {};

    if (!o.input) {
      $.error('input is missing');
    }

    www.mixin(this);

    this.$hint = $(o.hint);
    this.$input = $(o.input);

    // the query defaults to whatever the value of the input is
    // on initialization, it'll most likely be an empty string
    this.query = this.$input.val();

    // for tracking when a change event should be triggered
    this.queryWhenFocused = this.hasFocus() ? this.query : null;

    // helps with calculating the width of the input's value
    this.$overflowHelper = buildOverflowHelper(this.$input);

    // detect the initial lang direction
    this._checkLanguageDirection();

    // if no hint, noop all the hint related functions
    if (this.$hint.length === 0) {
      this.setHint =
      this.getHint =
      this.clearHint =
      this.clearHintIfInvalid = _.noop;
    }
  }

  // static methods
  // --------------

  Input.normalizeQuery = function(str) {
    // strips leading whitespace and condenses all whitespace
    return (_.toStr(str)).replace(/^\s*/g, '').replace(/\s{2,}/g, ' ');
  };

  // instance methods
  // ----------------

  _.mixin(Input.prototype, EventEmitter, {

    // ### event handlers

    _onBlur: function onBlur() {
      this.resetInputValue();
      this.trigger('blurred');
    },

    _onFocus: function onFocus() {
      this.queryWhenFocused = this.query;
      this.trigger('focused');
    },

    _onKeydown: function onKeydown($e) {
      // which is normalized and consistent (but not for ie)
      var keyName = specialKeyCodeMap[$e.which || $e.keyCode];

      this._managePreventDefault(keyName, $e);
      if (keyName && this._shouldTrigger(keyName, $e)) {
        this.trigger(keyName + 'Keyed', $e);
      }
    },

    _onInput: function onInput() {
      this._setQuery(this.getInputValue());
      this.clearHintIfInvalid();
      this._checkLanguageDirection();
    },

    // ### private

    _managePreventDefault: function managePreventDefault(keyName, $e) {
      var preventDefault;

      switch (keyName) {
        case 'up':
        case 'down':
          preventDefault = !withModifier($e);
          break;

        default:
          preventDefault = false;
      }

      preventDefault && $e.preventDefault();
    },

    _shouldTrigger: function shouldTrigger(keyName, $e) {
      var trigger;

      switch (keyName) {
        case 'tab':
          trigger = !withModifier($e);
          break;

        default:
          trigger = true;
      }

      return trigger;
    },

    _checkLanguageDirection: function checkLanguageDirection() {
      var dir = (this.$input.css('direction') || 'ltr').toLowerCase();

      if (this.dir !== dir) {
        this.dir = dir;
        this.$hint.attr('dir', dir);
        this.trigger('langDirChanged', dir);
      }
    },

    _setQuery: function setQuery(val, silent) {
      var areEquivalent, hasDifferentWhitespace;

      areEquivalent = areQueriesEquivalent(val, this.query);
      hasDifferentWhitespace = areEquivalent ?
        this.query.length !== val.length : false;

      this.query = val;

      if (!silent && !areEquivalent) {
        this.trigger('queryChanged', this.query);
      }

      else if (!silent && hasDifferentWhitespace) {
        this.trigger('whitespaceChanged', this.query);
      }
    },

    // ### public

    bind: function() {
      var that = this, onBlur, onFocus, onKeydown, onInput;

      // bound functions
      onBlur = _.bind(this._onBlur, this);
      onFocus = _.bind(this._onFocus, this);
      onKeydown = _.bind(this._onKeydown, this);
      onInput = _.bind(this._onInput, this);

      this.$input
      .on('blur.tt', onBlur)
      .on('focus.tt', onFocus)
      .on('keydown.tt', onKeydown);

      // ie8 don't support the input event
      // ie9 doesn't fire the input event when characters are removed
      if (!_.isMsie() || _.isMsie() > 9) {
        this.$input.on('input.tt', onInput);
      }

      else {
        this.$input.on('keydown.tt keypress.tt cut.tt paste.tt', function($e) {
          // if a special key triggered this, ignore it
          if (specialKeyCodeMap[$e.which || $e.keyCode]) { return; }

          // give the browser a chance to update the value of the input
          // before checking to see if the query changed
          _.defer(_.bind(that._onInput, that, $e));
        });
      }

      return this;
    },

    focus: function focus() {
      this.$input.focus();
    },

    blur: function blur() {
      this.$input.blur();
    },

    getLangDir: function getLangDir() {
      return this.dir;
    },

    getQuery: function getQuery() {
      return this.query || '';
    },

    setQuery: function setQuery(val, silent) {
      this.setInputValue(val);
      this._setQuery(val, silent);
    },

    hasQueryChangedSinceLastFocus: function hasQueryChangedSinceLastFocus() {
      return this.query !== this.queryWhenFocused;
    },

    getInputValue: function getInputValue() {
      return this.$input.val();
    },

    setInputValue: function setInputValue(value) {
      this.$input.val(value);
      this.clearHintIfInvalid();
      this._checkLanguageDirection();
    },

    resetInputValue: function resetInputValue() {
      this.setInputValue(this.query);
    },

    getHint: function getHint() {
      return this.$hint.val();
    },

    setHint: function setHint(value) {
      this.$hint.val(value);
    },

    clearHint: function clearHint() {
      this.setHint('');
    },

    clearHintIfInvalid: function clearHintIfInvalid() {
      var val, hint, valIsPrefixOfHint, isValid;

      val = this.getInputValue();
      hint = this.getHint();
      valIsPrefixOfHint = val !== hint && hint.indexOf(val) === 0;
      isValid = val !== '' && valIsPrefixOfHint && !this.hasOverflow();

      !isValid && this.clearHint();
    },

    hasFocus: function hasFocus() {
      return this.$input.is(':focus');
    },

    hasOverflow: function hasOverflow() {
      // 2 is arbitrary, just picking a small number to handle edge cases
      var constraint = this.$input.width() - 2;

      this.$overflowHelper.text(this.getInputValue());

      return this.$overflowHelper.width() >= constraint;
    },

    isCursorAtEnd: function() {
      var valueLength, selectionStart, range;

      valueLength = this.$input.val().length;
      selectionStart = this.$input[0].selectionStart;

      if (_.isNumber(selectionStart)) {
       return selectionStart === valueLength;
      }

      else if (document.selection) {
        // NOTE: this won't work unless the input has focus, the good news
        // is this code should only get called when the input has focus
        range = document.selection.createRange();
        range.moveStart('character', -valueLength);

        return valueLength === range.text.length;
      }

      return true;
    },

    destroy: function destroy() {
      this.$hint.off('.tt');
      this.$input.off('.tt');
      this.$overflowHelper.remove();

      // #970
      this.$hint = this.$input = this.$overflowHelper = $('<div>');
    }
  });

  return Input;

  // helper functions
  // ----------------

  function buildOverflowHelper($input) {
    return $('<pre aria-hidden="true"></pre>')
    .css({
      // position helper off-screen
      position: 'absolute',
      visibility: 'hidden',
      // avoid line breaks and whitespace collapsing
      whiteSpace: 'pre',
      // use same font css as input to calculate accurate width
      fontFamily: $input.css('font-family'),
      fontSize: $input.css('font-size'),
      fontStyle: $input.css('font-style'),
      fontVariant: $input.css('font-variant'),
      fontWeight: $input.css('font-weight'),
      wordSpacing: $input.css('word-spacing'),
      letterSpacing: $input.css('letter-spacing'),
      textIndent: $input.css('text-indent'),
      textRendering: $input.css('text-rendering'),
      textTransform: $input.css('text-transform')
    })
    .insertAfter($input);
  }

  function areQueriesEquivalent(a, b) {
    return Input.normalizeQuery(a) === Input.normalizeQuery(b);
  }

  function withModifier($e) {
    return $e.altKey || $e.ctrlKey || $e.metaKey || $e.shiftKey;
  }
})();
