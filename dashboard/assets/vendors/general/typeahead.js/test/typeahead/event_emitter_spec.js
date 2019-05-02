describe('EventEmitter', function() {

  beforeEach(function() {
    this.spy = jasmine.createSpy();
    this.target = _.mixin({}, EventEmitter);
  });

  it('methods should be chainable', function() {
    expect(this.target.onSync()).toEqual(this.target);
    expect(this.target.onAsync()).toEqual(this.target);
    expect(this.target.off()).toEqual(this.target);
    expect(this.target.trigger()).toEqual(this.target);
  });

  it('#on should take the context a callback should be called in', function() {
    var context = { val: 3 }, cbContext;

    this.target.onSync('xevent', setCbContext, context).trigger('xevent');

    waitsFor(assertCbContext, 'callback was called in the wrong context');

    function setCbContext() { cbContext = this; }
    function assertCbContext() { return cbContext === context; }
  });

  it('#onAsync callbacks should be invoked asynchronously', function() {
    this.target.onAsync('event', this.spy).trigger('event');

    expect(this.spy.callCount).toBe(0);
    waitsFor(assertCallCount(this.spy, 1), 'the callback was not invoked');
  });

  it('#onSync callbacks should be invoked synchronously', function() {
    this.target.onSync('event', this.spy).trigger('event');

    expect(this.spy.callCount).toBe(1);
  });

  it('#off should remove callbacks', function() {
    this.target
    .onSync('event1 event2', this.spy)
    .onAsync('event1 event2', this.spy)
    .off('event1 event2')
    .trigger('event1 event2');

    waits(100);
    runs(assertCallCount(this.spy, 0));
  });

  it('methods should accept multiple event types', function() {
    this.target
    .onSync('event1 event2', this.spy)
    .onAsync('event1 event2', this.spy)
    .trigger('event1 event2');

    expect(this.spy.callCount).toBe(2);
    waitsFor(assertCallCount(this.spy, 4), 'the callback was not invoked');
  });

  it('the event type should be passed to the callback', function() {
    this.target
    .onSync('sync', this.spy)
    .onAsync('async', this.spy)
    .trigger('sync async');

    waitsFor(assertArgs(this.spy, 0, ['sync']), 'bad args');
    waitsFor(assertArgs(this.spy, 1, ['async']), 'bad args');
  });

  it('arbitrary args should be passed to the callback', function() {
    this.target
    .onSync('event', this.spy)
    .onAsync('event', this.spy)
    .trigger('event', 1, 2);

    waitsFor(assertArgs(this.spy, 0, ['event', 1, 2]), 'bad args');
    waitsFor(assertArgs(this.spy, 1, ['event', 1, 2]), 'bad args');
  });

  it('callback execution should be cancellable', function() {
    var cancelSpy = jasmine.createSpy().andCallFake(cancel);

    this.target
    .onSync('one', cancelSpy)
    .onSync('one', this.spy)
    .onAsync('two', cancelSpy)
    .onAsync('two', this.spy)
    .onSync('three', cancelSpy)
    .onAsync('three', this.spy)
    .trigger('one two three');

    waitsFor(assertCallCount(cancelSpy, 3));
    waitsFor(assertCallCount(this.spy, 0));

    function cancel() { return false; }
  });

  function assertCallCount(spy, expected) {
    return function() { return spy.callCount === expected; };
  }

  function assertArgs(spy, call, expected) {
    return function() {
      var env = jasmine.getEnv(),
          actual = spy.calls[call] ? spy.calls[call].args : undefined;

      return env.equals_(actual, expected);
    };
  }

});
