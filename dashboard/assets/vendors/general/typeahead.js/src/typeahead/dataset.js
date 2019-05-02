/*
 * typeahead.js
 * https://github.com/twitter/typeahead.js
 * Copyright 2013-2014 Twitter, Inc. and other contributors; Licensed MIT
 */

var Dataset = (function() {
  'use strict';

  var keys, nameGenerator;

  keys = {
    val: 'tt-selectable-display',
    obj: 'tt-selectable-object'
  };

  nameGenerator = _.getIdGenerator();

  // constructor
  // -----------

  function Dataset(o, www) {
    o = o || {};
    o.templates = o.templates || {};

    // DEPRECATED: empty will be dropped in v1
    o.templates.notFound = o.templates.notFound || o.templates.empty;

    if (!o.source) {
      $.error('missing source');
    }

    if (!o.node) {
      $.error('missing node');
    }

    if (o.name && !isValidName(o.name)) {
      $.error('invalid dataset name: ' + o.name);
    }

    www.mixin(this);

    this.highlight = !!o.highlight;
    this.name = o.name || nameGenerator();

    this.limit = o.limit || 5;
    this.displayFn = getDisplayFn(o.display || o.displayKey);
    this.templates = getTemplates(o.templates, this.displayFn);

    // use duck typing to see if source is a bloodhound instance by checking
    // for the __ttAdapter property; otherwise assume it is a function
    this.source = o.source.__ttAdapter ? o.source.__ttAdapter() : o.source;

    // if the async option is undefined, inspect the source signature as
    // a hint to figuring out of the source will return async suggestions
    this.async = _.isUndefined(o.async) ? this.source.length > 2 : !!o.async;

    this._resetLastSuggestion();

    this.$el = $(o.node)
    .addClass(this.classes.dataset)
    .addClass(this.classes.dataset + '-' + this.name);
  }

  // static methods
  // --------------

  Dataset.extractData = function extractData(el) {
    var $el = $(el);

    if ($el.data(keys.obj)) {
      return {
        val: $el.data(keys.val) || '',
        obj: $el.data(keys.obj) || null
      };
    }

    return null;
  };

  // instance methods
  // ----------------

  _.mixin(Dataset.prototype, EventEmitter, {

    // ### private

    _overwrite: function overwrite(query, suggestions) {
      suggestions = suggestions || [];

      // got suggestions: overwrite dom with suggestions
      if (suggestions.length) {
        this._renderSuggestions(query, suggestions);
      }

      // no suggestions, expecting async: overwrite dom with pending
      else if (this.async && this.templates.pending) {
        this._renderPending(query);
      }

      // no suggestions, not expecting async: overwrite dom with not found
      else if (!this.async && this.templates.notFound) {
        this._renderNotFound(query);
      }

      // nothing to render: empty dom
      else {
        this._empty();
      }

      this.trigger('rendered', this.name, suggestions, false);
    },

    _append: function append(query, suggestions) {
      suggestions = suggestions || [];

      // got suggestions, sync suggestions exist: append suggestions to dom
      if (suggestions.length && this.$lastSuggestion.length) {
        this._appendSuggestions(query, suggestions);
      }

      // got suggestions, no sync suggestions: overwrite dom with suggestions
      else if (suggestions.length) {
        this._renderSuggestions(query, suggestions);
      }

      // no async/sync suggestions: overwrite dom with not found
      else if (!this.$lastSuggestion.length && this.templates.notFound) {
        this._renderNotFound(query);
      }

      this.trigger('rendered', this.name, suggestions, true);
    },

    _renderSuggestions: function renderSuggestions(query, suggestions) {
      var $fragment;

      $fragment = this._getSuggestionsFragment(query, suggestions);
      this.$lastSuggestion = $fragment.children().last();

      this.$el.html($fragment)
      .prepend(this._getHeader(query, suggestions))
      .append(this._getFooter(query, suggestions));
    },

    _appendSuggestions: function appendSuggestions(query, suggestions) {
      var $fragment, $lastSuggestion;

      $fragment = this._getSuggestionsFragment(query, suggestions);
      $lastSuggestion = $fragment.children().last();

      this.$lastSuggestion.after($fragment);

      this.$lastSuggestion = $lastSuggestion;
    },

    _renderPending: function renderPending(query) {
      var template = this.templates.pending;

      this._resetLastSuggestion();
      template && this.$el.html(template({
        query: query,
        dataset: this.name,
      }));
    },

    _renderNotFound: function renderNotFound(query) {
      var template = this.templates.notFound;

      this._resetLastSuggestion();
      template && this.$el.html(template({
        query: query,
        dataset: this.name,
      }));
    },

    _empty: function empty() {
      this.$el.empty();
      this._resetLastSuggestion();
    },

    _getSuggestionsFragment: function getSuggestionsFragment(query, suggestions) {
      var that = this, fragment;

      fragment = document.createDocumentFragment();
      _.each(suggestions, function getSuggestionNode(suggestion) {
        var $el, context;

        context = that._injectQuery(query, suggestion);

        $el = $(that.templates.suggestion(context))
        .data(keys.obj, suggestion)
        .data(keys.val, that.displayFn(suggestion))
        .addClass(that.classes.suggestion + ' ' + that.classes.selectable);

        fragment.appendChild($el[0]);
      });

      this.highlight && highlight({
        className: this.classes.highlight,
        node: fragment,
        pattern: query
      });

      return $(fragment);
    },

    _getFooter: function getFooter(query, suggestions) {
      return this.templates.footer ?
        this.templates.footer({
          query: query,
          suggestions: suggestions,
          dataset: this.name
        }) : null;
    },

    _getHeader: function getHeader(query, suggestions) {
      return this.templates.header ?
        this.templates.header({
          query: query,
          suggestions: suggestions,
          dataset: this.name
        }) : null;
    },

    _resetLastSuggestion: function resetLastSuggestion() {
      this.$lastSuggestion = $();
    },

    _injectQuery: function injectQuery(query, obj) {
      return _.isObject(obj) ? _.mixin({ _query: query }, obj) : obj;
    },

    // ### public

    update: function update(query) {
      var that = this, canceled = false, syncCalled = false, rendered = 0;

      // cancel possible pending update
      this.cancel();

      this.cancel = function cancel() {
        canceled = true;
        that.cancel = $.noop;
        that.async && that.trigger('asyncCanceled', query);
      };

      this.source(query, sync, async);
      !syncCalled && sync([]);

      function sync(suggestions) {
        if (syncCalled) { return; }

        syncCalled = true;
        suggestions = (suggestions || []).slice(0, that.limit);
        rendered = suggestions.length;

        that._overwrite(query, suggestions);

        if (rendered < that.limit && that.async) {
          that.trigger('asyncRequested', query);
        }
      }

      function async(suggestions) {
        suggestions = suggestions || [];

        // if the update has been canceled or if the query has changed
        // do not render the suggestions as they've become outdated
        if (!canceled && rendered < that.limit) {
          that.cancel = $.noop;
          rendered += suggestions.length;
          that._append(query, suggestions.slice(0, that.limit - rendered));

          that.async && that.trigger('asyncReceived', query);
        }
      }
    },

    // cancel function gets set in #update
    cancel: $.noop,

    clear: function clear() {
      this._empty();
      this.cancel();
      this.trigger('cleared');
    },

    isEmpty: function isEmpty() {
      return this.$el.is(':empty');
    },

    destroy: function destroy() {
      // #970
      this.$el = $('<div>');
    }
  });

  return Dataset;

  // helper functions
  // ----------------

  function getDisplayFn(display) {
    display = display || _.stringify;

    return _.isFunction(display) ? display : displayFn;

    function displayFn(obj) { return obj[display]; }
  }

  function getTemplates(templates, displayFn) {
    return {
      notFound: templates.notFound && _.templatify(templates.notFound),
      pending: templates.pending && _.templatify(templates.pending),
      header: templates.header && _.templatify(templates.header),
      footer: templates.footer && _.templatify(templates.footer),
      suggestion: templates.suggestion || suggestionTemplate
    };

    function suggestionTemplate(context) {
      return $('<div>').text(displayFn(context));
    }
  }

  function isValidName(str) {
    // dashes, underscores, letters, and numbers
    return (/^[_a-zA-Z0-9-]+$/).test(str);
  }
})();
