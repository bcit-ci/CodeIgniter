/*
 * typeahead.js
 * https://github.com/twitter/typeahead.js
 * Copyright 2013-2014 Twitter, Inc. and other contributors; Licensed MIT
 */

var Menu = (function() {
  'use strict';

  // constructor
  // -----------

  function Menu(o, www) {
    var that = this;

    o = o || {};

    if (!o.node) {
      $.error('node is required');
    }

    www.mixin(this);

    this.$node = $(o.node);

    // the latest query #update was called with
    this.query = null;
    this.datasets = _.map(o.datasets, initializeDataset);

    function initializeDataset(oDataset) {
      var node = that.$node.find(oDataset.node).first();
      oDataset.node = node.length ? node : $('<div>').appendTo(that.$node);

      return new Dataset(oDataset, www);
    }
  }

  // instance methods
  // ----------------

  _.mixin(Menu.prototype, EventEmitter, {

    // ### event handlers

    _onSelectableClick: function onSelectableClick($e) {
      this.trigger('selectableClicked', $($e.currentTarget));
    },

    _onRendered: function onRendered(type, dataset, suggestions, async) {
      this.$node.toggleClass(this.classes.empty, this._allDatasetsEmpty());
      this.trigger('datasetRendered', dataset, suggestions, async);
    },

    _onCleared: function onCleared() {
      this.$node.toggleClass(this.classes.empty, this._allDatasetsEmpty());
      this.trigger('datasetCleared');
    },

    _propagate: function propagate() {
      this.trigger.apply(this, arguments);
    },

    // ### private

    _allDatasetsEmpty: function allDatasetsEmpty() {
      return _.every(this.datasets, isDatasetEmpty);

      function isDatasetEmpty(dataset) { return dataset.isEmpty(); }
    },

    _getSelectables: function getSelectables() {
      return this.$node.find(this.selectors.selectable);
    },

    _removeCursor: function _removeCursor() {
      var $selectable = this.getActiveSelectable();
      $selectable && $selectable.removeClass(this.classes.cursor);
    },

    _ensureVisible: function ensureVisible($el) {
      var elTop, elBottom, nodeScrollTop, nodeHeight;

      elTop = $el.position().top;
      elBottom = elTop + $el.outerHeight(true);
      nodeScrollTop = this.$node.scrollTop();
      nodeHeight = this.$node.height() +
        parseInt(this.$node.css('paddingTop'), 10) +
        parseInt(this.$node.css('paddingBottom'), 10);

      if (elTop < 0) {
        this.$node.scrollTop(nodeScrollTop + elTop);
      }

      else if (nodeHeight < elBottom) {
        this.$node.scrollTop(nodeScrollTop + (elBottom - nodeHeight));
      }
    },

    // ### public

    bind: function() {
    var that = this, onSelectableClick;

      onSelectableClick = _.bind(this._onSelectableClick, this);
      this.$node.on('click.tt', this.selectors.selectable, onSelectableClick);

      _.each(this.datasets, function(dataset) {
        dataset
        .onSync('asyncRequested', that._propagate, that)
        .onSync('asyncCanceled', that._propagate, that)
        .onSync('asyncReceived', that._propagate, that)
        .onSync('rendered', that._onRendered, that)
        .onSync('cleared', that._onCleared, that);
      });

      return this;
    },

    isOpen: function isOpen() {
      return this.$node.hasClass(this.classes.open);
    },

    open: function open() {
      this.$node.addClass(this.classes.open);
    },

    close: function close() {
      this.$node.removeClass(this.classes.open);
      this._removeCursor();
    },

    setLanguageDirection: function setLanguageDirection(dir) {
      this.$node.attr('dir', dir);
    },

    selectableRelativeToCursor: function selectableRelativeToCursor(delta) {
      var $selectables, $oldCursor, oldIndex, newIndex;

      $oldCursor = this.getActiveSelectable();
      $selectables = this._getSelectables();

      // shifting before and after modulo to deal with -1 index
      oldIndex = $oldCursor ? $selectables.index($oldCursor) : -1;
      newIndex = oldIndex + delta;
      newIndex = (newIndex + 1) % ($selectables.length + 1) - 1;

      // wrap new index if less than -1
      newIndex = newIndex < -1 ? $selectables.length - 1 : newIndex;

      return newIndex === -1 ? null : $selectables.eq(newIndex);
    },

    setCursor: function setCursor($selectable) {
      this._removeCursor();

      if ($selectable = $selectable && $selectable.first()) {
        $selectable.addClass(this.classes.cursor);

        // in the case of scrollable overflow
        // make sure the cursor is visible in the node
        this._ensureVisible($selectable);
      }
    },

    getSelectableData: function getSelectableData($el) {
      return ($el && $el.length) ? Dataset.extractData($el) : null;
    },

    getActiveSelectable: function getActiveSelectable() {
      var $selectable = this._getSelectables().filter(this.selectors.cursor).first();

      return $selectable.length ? $selectable : null;
    },

    getTopSelectable: function getTopSelectable() {
      var $selectable = this._getSelectables().first();

      return $selectable.length ? $selectable : null;
    },

    update: function update(query) {
      var isValidUpdate = query !== this.query;

      // don't update if the query hasn't changed
      if (isValidUpdate) {
        this.query = query;
        _.each(this.datasets, updateDataset);
      }

      return isValidUpdate;

      function updateDataset(dataset) { dataset.update(query); }
    },

    empty: function empty() {
      _.each(this.datasets, clearDataset);

      this.query = null;
      this.$node.addClass(this.classes.empty);

      function clearDataset(dataset) { dataset.clear(); }
    },

    destroy: function destroy() {
      this.$node.off('.tt');

      // #970
      this.$node = $('<div>');

      _.each(this.datasets, destroyDataset);

      function destroyDataset(dataset) { dataset.destroy(); }
    }
  });

  return Menu;
})();
