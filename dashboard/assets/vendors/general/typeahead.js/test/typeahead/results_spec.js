describe('Menu', function() {
  var www = WWW();

  beforeEach(function() {
    var $fixture;

    jasmine.Dataset.useMock();

    setFixtures('<div id="menu-fixture"></div>');

    $fixture = $('#jasmine-fixtures');
    this.$node = $fixture.find('#menu-fixture');
    this.$node.html(fixtures.html.dataset);

    this.view = new Menu({ node: this.$node, datasets: [{}] }, www).bind();
    this.dataset = this.view.datasets[0];
  });

  it('should throw an error if node is missing', function() {
    expect(noNode).toThrow();
    function noNode() { new Menu({ datasets: [{}] }, www); }
  });

  describe('when click event is triggered on a selectable', function() {
    it('should trigger selectableClicked', function() {
      var spy;

      this.view.onSync('selectableClicked', spy = jasmine.createSpy());

      this.$node.find(www.selectors.selectable).first().click();

      expect(spy).toHaveBeenCalled();
    });
  });

  describe('when rendered is triggered on a dataset', function() {
    it('should add empty class to node if empty', function() {
      this.dataset.isEmpty.andReturn(true);

      this.$node.removeClass(www.classes.empty);
      this.dataset.trigger('rendered');

      expect(this.$node).toHaveClass(www.classes.empty);
    });

    it('should remove empty class from node if not empty', function() {
      this.dataset.isEmpty.andReturn(false);

      this.$node.addClass(www.classes.empty);
      this.dataset.trigger('rendered');

      expect(this.$node).not.toHaveClass(www.classes.empty);
    });

    it('should trigger datasetRendered', function() {
      var spy;

      this.view.onSync('datasetRendered', spy = jasmine.createSpy());
      this.dataset.trigger('rendered');

      expect(spy).toHaveBeenCalled();
    });
  });

  describe('when cleared is triggered on a dataset', function() {
    it('should add empty class to node if empty', function() {
      this.dataset.isEmpty.andReturn(true);

      this.$node.removeClass(www.classes.empty);
      this.dataset.trigger('cleared');

      expect(this.$node).toHaveClass(www.classes.empty);
    });

    it('should remove empty class from node if not empty', function() {
      this.dataset.isEmpty.andReturn(false);

      this.$node.addClass(www.classes.empty);
      this.dataset.trigger('cleared');

      expect(this.$node).not.toHaveClass(www.classes.empty);
    });

    it('should trigger datasetCleared', function() {
      var spy;

      this.view.onSync('datasetCleared', spy = jasmine.createSpy());
      this.dataset.trigger('cleared');

      expect(spy).toHaveBeenCalled();
    });
  });

  describe('when asyncRequested is triggered on a dataset', function() {
    it('should propagate event', function() {
      var spy = jasmine.createSpy();

      this.dataset.onSync('asyncRequested', spy);
      this.dataset.trigger('asyncRequested');

      expect(spy).toHaveBeenCalled();
    });
  });

  describe('when asyncCanceled is triggered on a dataset', function() {
    it('should propagate event', function() {
      var spy = jasmine.createSpy();

      this.dataset.onSync('asyncCanceled', spy);
      this.dataset.trigger('asyncCanceled');

      expect(spy).toHaveBeenCalled();
    });
  });

  describe('when asyncReceieved is triggered on a dataset', function() {
    it('should propagate event', function() {
      var spy = jasmine.createSpy();

      this.dataset.onSync('asyncReceived', spy);
      this.dataset.trigger('asyncReceived');

      expect(spy).toHaveBeenCalled();
    });
  });

  describe('#open', function() {
    it('should add open class to node', function() {
      this.$node.removeClass(www.classes.open);
      this.view.open();

      expect(this.$node).toHaveClass(www.classes.open);
    });
  });

  describe('#close', function() {
    it('should remove open class to node', function() {
      this.$node.addClass(www.classes.open);
      this.view.close();

      expect(this.$node).not.toHaveClass(www.classes.open);
    });

    it('should remove cursor', function() {
      var $selectable;

      $selectable = this.view._getSelectables().first();
      this.view.setCursor($selectable);

      expect($selectable).toHaveClass(www.classes.cursor);

      this.view.close();

      expect($selectable).not.toHaveClass(www.classes.cursor);
    });
  });

  describe('#setLanguageDirection', function() {
    it('should update css for given language direction', function() {
      this.view.setLanguageDirection('rtl');
      expect(this.$node).toHaveAttr('dir', 'rtl');

      this.view.setLanguageDirection('ltr');
      expect(this.$node).toHaveAttr('dir', 'ltr');
    });
  });

  describe('#selectableRelativeToCursor', function() {
    it('should return selectable delta spots away from cursor', function() {
      var $first, $second;

      $first = this.view._getSelectables().eq(0);
      $second = this.view._getSelectables().eq(1);

      this.view.setCursor($first);
      expect(this.view.selectableRelativeToCursor(+1)).toBe($second);
    });

    it('should support negative deltas', function() {
      var $first, $second;

      $first = this.view._getSelectables().eq(0);
      $second = this.view._getSelectables().eq(1);

      this.view.setCursor($second);
      expect(this.view.selectableRelativeToCursor(-1)).toBe($first);
    });

    it('should wrap', function() {
      var $expected, $actual;

      $expected = this.view._getSelectables().eq(-1);
      $actual = this.view.selectableRelativeToCursor(-1);

      expect($actual).toBe($expected);
    });

    it('should return null if delta lands on input', function() {
      var $first;

      $first = this.view._getSelectables().eq(0);

      this.view.setCursor($first);
      expect(this.view.selectableRelativeToCursor(-1)).toBeNull();
    });
  });

  describe('#setCursor', function() {
    it('should remove cursor if null is passed in', function() {
      var $selectable;

      $selectable = this.view._getSelectables().eq(0);
      this.view.setCursor($selectable);
      expect(this.view.getActiveSelectable()).toBe($selectable);

      this.view.setCursor(null);
      expect(this.view.getActiveSelectable()).toBeNull();
    });

    it('should move cursor to passed in selectable', function() {
      var $selectable;

      $selectable = this.view._getSelectables().eq(0);

      expect(this.view.getActiveSelectable()).toBeNull();
      this.view.setCursor($selectable);
      expect(this.view.getActiveSelectable()).toBe($selectable);
    });
  });

  describe('#getSelectableData', function() {
    it('should extract the data from the selectable element', function() {
      var $selectable, datum;

      $selectable = $('<div>').data({
        'tt-selectable-display': 'one',
        'tt-selectable-object': 'two'
      });

      data = this.view.getSelectableData($selectable);

      expect(data).toEqual({ val: 'one', obj: 'two' });
    });

    it('should return null if no element is given', function() {
      expect(this.view.getSelectableData($('notreal'))).toBeNull();
    });
  });

  describe('#getActiveSelectable', function() {
    it('should return the selectable the cursor is on', function() {
      var $first;

      $first = this.view._getSelectables().eq(0);
      this.view.setCursor($first);

      expect(this.view.getActiveSelectable()).toBe($first);
    });

    it('should return null if the cursor is off', function() {
      expect(this.view.getActiveSelectable()).toBeNull();
    });
  });

  describe('#getTopSelectable', function() {
    it('should return the selectable at the top of the menu', function() {
      var $first;

      $first = this.view._getSelectables().eq(0);
      expect(this.view.getTopSelectable()).toBe($first);
    });
  });

  describe('#update', function() {
    it('should invoke update on each dataset if valid update', function() {
      this.view.update('fiz');
      expect(this.dataset.update).toHaveBeenCalled();
    });

    it('should return true when valid update', function() {
      expect(this.view.update('fiz')).toBe(true);
    });

    it('should return false when invalid update', function() {
      this.view.update('fiz');
      expect(this.view.update('fiz')).toBe(false);
    });
  });

  describe('#empty', function() {
    it('should set query to null', function() {
      this.view.query = 'fiz';
      this.view.empty();

      expect(this.view.query).toBeNull();
    });

    it('should add empty class to node', function() {
      this.$node.removeClass(www.classes.empty);
      this.view.empty();

      expect(this.$node).toHaveClass(www.classes.empty);
    });

    it('should invoke clear on each dataset', function() {
      this.view.empty();
      expect(this.dataset.clear).toHaveBeenCalled();
    });
  });

  describe('#destroy', function() {
    it('should remove event handlers', function() {
      var $node = this.view.$node;

      spyOn($node, 'off');
      this.view.destroy();
      expect($node.off).toHaveBeenCalledWith('.tt');
    });

    it('should destroy its datasets', function() {
      this.view.destroy();
      expect(this.dataset.destroy).toHaveBeenCalled();
    });

    it('should set node element to dummy element', function() {
      var $node = this.view.$node;

      this.view.destroy();
      expect(this.view.$node).not.toBe($node);
    });
  });
});
