describe('Input', function() {
  var KEYS, www;

   KEYS = {
    enter: 13,
    esc: 27,
    tab: 9,
    left: 37,
    right: 39,
    up: 38,
    down: 40,
    normal: 65 // "A" key
  };

  www = WWW();

  beforeEach(function() {
    var $fixture;

    setFixtures(fixtures.html.input + fixtures.html.hint);

    $fixture = $('#jasmine-fixtures');
    this.$input = $fixture.find('.tt-input');
    this.$hint = $fixture.find('.tt-hint');

    this.view = new Input({ input: this.$input, hint: this.$hint }, www).bind();
  });

  it('should throw an error if no input is provided', function() {
    expect(noInput).toThrow();

    function noInput() { new Input({}, www); }
  });

  describe('when the blur DOM event is triggered', function() {
    it('should reset the input value', function() {
      this.view.setQuery('wine');
      this.view.setInputValue('cheese');

      this.$input.blur();

      expect(this.$input.val()).toBe('wine');
    });

    it('should trigger blurred', function() {
      var spy;

      this.view.onSync('blurred', spy = jasmine.createSpy());
      this.$input.blur();

      expect(spy).toHaveBeenCalled();
    });
  });

  describe('when the focus DOM event is triggered', function() {
    it('should update queryWhenFocused', function() {
      this.view.setQuery('hi');
      this.$input.focus();
      expect(this.view.hasQueryChangedSinceLastFocus()).toBe(false);
      this.view.setQuery('bye');
      expect(this.view.hasQueryChangedSinceLastFocus()).toBe(true);
    });

    it('should trigger focused', function() {
      var spy;

      this.view.onSync('focused', spy = jasmine.createSpy());
      this.$input.focus();

      expect(spy).toHaveBeenCalled();
    });
  });

  describe('when the keydown DOM event is triggered by tab', function() {
    it('should trigger tabKeyed if no modifiers were pressed', function() {
      var spy;

      this.view.onSync('tabKeyed', spy = jasmine.createSpy());
      simulateKeyEvent(this.$input, 'keydown', KEYS.tab);

      expect(spy).toHaveBeenCalled();
    });

    it('should not trigger tabKeyed if modifiers were pressed', function() {
      var spy;

      this.view.onSync('tabKeyed', spy = jasmine.createSpy());
      simulateKeyEvent(this.$input, 'keydown', KEYS.tab, true);

      expect(spy).not.toHaveBeenCalled();
    });
  });

  describe('when the keydown DOM event is triggered by esc', function() {
    it('should trigger escKeyed', function() {
      var spy;

      this.view.onSync('escKeyed', spy = jasmine.createSpy());
      simulateKeyEvent(this.$input, 'keydown', KEYS.esc);

      expect(spy).toHaveBeenCalled();
    });
  });

  describe('when the keydown DOM event is triggered by left', function() {
    it('should trigger leftKeyed', function() {
      var spy;

      this.view.onSync('leftKeyed', spy = jasmine.createSpy());
      simulateKeyEvent(this.$input, 'keydown', KEYS.left);

      expect(spy).toHaveBeenCalled();
    });
  });

  describe('when the keydown DOM event is triggered by right', function() {
    it('should trigger rightKeyed', function() {
      var spy;

      this.view.onSync('rightKeyed', spy = jasmine.createSpy());
      simulateKeyEvent(this.$input, 'keydown', KEYS.right);

      expect(spy).toHaveBeenCalled();
    });
  });

  describe('when the keydown DOM event is triggered by enter', function() {
    it('should trigger enterKeyed', function() {
      var spy;

      this.view.onSync('enterKeyed', spy = jasmine.createSpy());
      simulateKeyEvent(this.$input, 'keydown', KEYS.enter);

      expect(spy).toHaveBeenCalled();
    });
  });

  describe('when the keydown DOM event is triggered by up', function() {
    it('should trigger upKeyed', function() {
      var spy;

      this.view.onSync('upKeyed', spy = jasmine.createSpy());
      simulateKeyEvent(this.$input, 'keydown', KEYS.up);

      expect(spy).toHaveBeenCalled();
    });

    it('should prevent default if no modifers were pressed', function() {
      var $e = simulateKeyEvent(this.$input, 'keydown', KEYS.up);

      expect($e.preventDefault).toHaveBeenCalled();
    });

    it('should not prevent default if modifers were pressed', function() {
      var $e = simulateKeyEvent(this.$input, 'keydown', KEYS.up, true);

      expect($e.preventDefault).not.toHaveBeenCalled();
    });
  });

  describe('when the keydown DOM event is triggered by down', function() {
    it('should trigger downKeyed', function() {
      var spy;

      this.view.onSync('downKeyed', spy = jasmine.createSpy());
      simulateKeyEvent(this.$input, 'keydown', KEYS.down);

      expect(spy).toHaveBeenCalled();
    });

    it('should prevent default if no modifers were pressed', function() {
      var $e = simulateKeyEvent(this.$input, 'keydown', KEYS.down);

      expect($e.preventDefault).toHaveBeenCalled();
    });

    it('should not prevent default if modifers were pressed', function() {
      var $e = simulateKeyEvent(this.$input, 'keydown', KEYS.down, true);

      expect($e.preventDefault).not.toHaveBeenCalled();
    });
  });

  // NOTE: have to treat these as async because the ie polyfill acts
  // in a async manner
  describe('when the input DOM event is triggered', function() {
    it('should update query', function() {
      this.view.setQuery('wine');
      this.view.setInputValue('cheese');

      simulateInputEvent(this.$input);

      waitsFor(function() { return this.view.getQuery() === 'cheese'; });
    });

    it('should trigger queryChanged if the query changed', function() {
      var spy;

      this.view.setQuery('wine');
      this.view.setInputValue('cheese');
      this.view.onSync('queryChanged', spy = jasmine.createSpy());

      simulateInputEvent(this.$input);

      expect(spy).toHaveBeenCalled();
    });

    it('should trigger whitespaceChanged if whitespace changed', function() {
      var spy;

      this.view.setQuery('wine  bar');
      this.view.setInputValue('wine bar');
      this.view.onSync('whitespaceChanged', spy = jasmine.createSpy());

      simulateInputEvent(this.$input);

      expect(spy).toHaveBeenCalled();
    });

    it('should clear hint if invalid', function() {
      spyOn(this.view, 'clearHintIfInvalid');
      simulateInputEvent(this.$input);
      expect(this.view.clearHintIfInvalid).toHaveBeenCalled();
    });

    it('should check lang direction', function() {
      var spy;

      this.$input.css('direction', 'rtl');
      this.view.onSync('langDirChanged', spy = jasmine.createSpy());

      simulateInputEvent(this.$input);

      expect(this.view.dir).toBe('rtl');
      expect(this.$hint).toHaveAttr('dir', 'rtl');
      expect(spy).toHaveBeenCalled();
    });
  });

  describe('.normalizeQuery', function() {
    it('should strip leading whitespace', function() {
      expect(Input.normalizeQuery('  foo')).toBe('foo');
    });

    it('should condense whitespace', function() {
      expect(Input.normalizeQuery('foo   bar')).toBe('foo bar');
    });

    it('should play nice with non-string values', function() {
      expect(Input.normalizeQuery(2)).toBe('2');
      expect(Input.normalizeQuery([])).toBe('');
      expect(Input.normalizeQuery(null)).toBe('');
      expect(Input.normalizeQuery(undefined)).toBe('');
      expect(Input.normalizeQuery(false)).toBe('false');
    });
  });

  describe('#focus', function() {
    it('should focus the input', function() {
      this.$input.blur();
      this.view.focus();

      expect(this.$input).toBeFocused();
    });
  });

  describe('#blur', function() {
    it('should blur the input', function() {
      this.$input.focus();
      this.view.blur();

      expect(this.$input).not.toBeFocused();
    });
  });

  describe('#getQuery', function() {
    it('should act as getter to the query property', function() {
      this.view.setQuery('mouse');
      expect(this.view.getQuery()).toBe('mouse');
    });
  });

  describe('#setQuery', function() {
    it('should act as setter to the query property', function() {
      this.view.setQuery('mouse');
      expect(this.view.getQuery()).toBe('mouse');
    });

    it('should update input value', function() {
      this.view.setQuery('mouse');
      expect(this.view.getInputValue()).toBe('mouse');
    });

    it('should trigger queryChanged if the query changed', function() {
      var spy;

      this.view.setQuery('wine');
      this.view.onSync('queryChanged', spy = jasmine.createSpy());
      this.view.setQuery('cheese');

      expect(spy).toHaveBeenCalled();
    });

    it('should trigger whitespaceChanged if whitespace changed', function() {
      var spy;

      this.view.setQuery('wine   bar');
      this.view.onSync('whitespaceChanged', spy = jasmine.createSpy());
      this.view.setQuery('wine bar');

      expect(spy).toHaveBeenCalled();
    });

    it('should clear hint if invalid', function() {
      spyOn(this.view, 'clearHintIfInvalid');
      simulateInputEvent(this.$input);
      expect(this.view.clearHintIfInvalid).toHaveBeenCalled();
    });
  });

  describe('#hasQueryChangedSinceLastFocus', function() {
    it('should return true if the query has changed since focus', function() {
      this.view.setQuery('hi');
      this.$input.focus();
      this.view.setQuery('bye');
      expect(this.view.hasQueryChangedSinceLastFocus()).toBe(true);
    });

    it('should return false if the query has not changed since focus', function() {
      this.view.setQuery('hi');
      this.$input.focus();
      expect(this.view.hasQueryChangedSinceLastFocus()).toBe(false);
    });
  });

  describe('#getInputValue', function() {
    it('should act as getter to the input value', function() {
      this.$input.val('cheese');
      expect(this.view.getInputValue()).toBe('cheese');
    });
  });

  describe('#setInputValue', function() {
    it('should act as setter to the input value', function() {
      this.view.setInputValue('cheese');
      expect(this.view.getInputValue()).toBe('cheese');
    });

    it('should clear hint if invalid', function() {
      spyOn(this.view, 'clearHintIfInvalid');
      this.view.setInputValue('cheese head');
      expect(this.view.clearHintIfInvalid).toHaveBeenCalled();
    });

    it('should check lang direction', function() {
      var spy;

      this.$input.css('direction', 'rtl');
      this.view.onSync('langDirChanged', spy = jasmine.createSpy());

      simulateInputEvent(this.$input);

      expect(this.view.dir).toBe('rtl');
      expect(this.$hint).toHaveAttr('dir', 'rtl');
      expect(spy).toHaveBeenCalled();
    });
  });

  describe('#getHint/#setHint', function() {
    it('should act as getter/setter to value of hint', function() {
      this.view.setHint('mountain');
      expect(this.view.getHint()).toBe('mountain');
    });
  });

  describe('#resetInputValue', function() {
    it('should reset input value to last query', function() {
      this.view.setQuery('cheese');
      this.view.setInputValue('wine');

      this.view.resetInputValue();
      expect(this.view.getInputValue()).toBe('cheese');
    });
  });

  describe('#clearHint', function() {
    it('should set the hint value to the empty string', function() {
      this.view.setHint('cheese');
      this.view.clearHint();

      expect(this.view.getHint()).toBe('');
    });
  });

  describe('#clearHintIfInvalid', function() {
    it('should clear hint if input value is empty string', function() {
      this.view.setInputValue('');
      this.view.setHint('cheese');
      this.view.clearHintIfInvalid();

      expect(this.view.getHint()).toBe('');
    });

    it('should clear hint if input value is not prefix of input', function() {
      this.view.setInputValue('milk');
      this.view.setHint('cheese');
      this.view.clearHintIfInvalid();

      expect(this.view.getHint()).toBe('');
    });

    it('should clear hint if overflow exists', function() {
      spyOn(this.view, 'hasOverflow').andReturn(true);
      this.view.setInputValue('che');
      this.view.setHint('cheese');
      this.view.clearHintIfInvalid();

      expect(this.view.getHint()).toBe('');
    });

    it('should not clear hint if input value is prefix of input', function() {
      this.view.setInputValue('che');
      this.view.setHint('cheese');
      this.view.clearHintIfInvalid();

      expect(this.view.getHint()).toBe('cheese');
    });
  });

  describe('#hasOverflow', function() {
    it('should return true if the input has overflow text', function() {
      var longStr = new Array(1000).join('a');

      this.view.setInputValue(longStr);
      expect(this.view.hasOverflow()).toBe(true);
    });

    it('should return false if the input has no overflow text', function() {
      var shortStr = 'aah';

      this.view.setInputValue(shortStr);
      expect(this.view.hasOverflow()).toBe(false);
    });
  });

  describe('#isCursorAtEnd', function() {
    it('should return true if the text cursor is at the end', function() {
      this.view.setInputValue('boo');

      setCursorPosition(this.$input, 3);
      expect(this.view.isCursorAtEnd()).toBe(true);
    });

    it('should return false if the text cursor is not at the end', function() {
      this.view.setInputValue('boo');

      setCursorPosition(this.$input, 1);
      expect(this.view.isCursorAtEnd()).toBe(false);
    });
  });

  describe('#destroy', function() {
    it('should remove event handlers', function() {
      var $input, $hint;

      $hint = this.view.$hint;
      $input = this.view.$input;

      spyOn($hint, 'off');
      spyOn($input, 'off');

      this.view.destroy();

      expect($hint.off).toHaveBeenCalledWith('.tt');
      expect($input.off).toHaveBeenCalledWith('.tt');
    });

    it('should set references to DOM elements to dummy element', function() {
      var $hint, $input, $overflowHelper;

      $hint = this.view.$hint;
      $input = this.view.$input;
      $overflowHelper = this.view.$overflowHelper;

      this.view.destroy();

      expect(this.view.$hint).not.toBe($hint);
      expect(this.view.$input).not.toBe($input);
      expect(this.view.$overflowHelper).not.toBe($overflowHelper);
    });
  });

  // helper functions
  // ----------------

  function simulateInputEvent($node) {
    var $e, type;

    type = _.isMsie() ? 'keypress' : 'input';
    $e = $.Event(type);

    $node.trigger($e);
  }

  function simulateKeyEvent($node, type, key, withModifier) {
    var $e;

    $e = $.Event(type, {
      keyCode: key,
      altKey: !!withModifier,
      ctrlKey: !!withModifier,
      metaKey: !!withModifier,
      shiftKey: !!withModifier
    });

    spyOn($e, 'preventDefault');
    $node.trigger($e);

    return $e;
  }

  function setCursorPosition($input, pos) {
    var input = $input[0], range;

    if (input.setSelectionRange) {
      input.focus();
      input.setSelectionRange(pos, pos);
    }

    else if (input.createTextRange) {
      range = input.createTextRange();
      range.collapse(true);
      range.moveEnd('character', pos);
      range.moveStart('character', pos);
      range.select();
    }
  }
});
