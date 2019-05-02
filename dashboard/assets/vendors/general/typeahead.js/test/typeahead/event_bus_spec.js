describe('EventBus', function() {

  beforeEach(function() {
    var $fixture;

    setFixtures(fixtures.html.input);

    $fixture = $('#jasmine-fixtures');
    this.$el = $fixture.find('.tt-input');

    this.eventBus = new EventBus({ el: this.$el });
  });

  it('#trigger should trigger event', function() {
    var spy = jasmine.createSpy();

    this.$el.on('typeahead:fiz', spy);

    this.eventBus.trigger('fiz');
    expect(spy).toHaveBeenCalled();
  });

  it('#before should return false if default was not prevented', function() {
    var spy = jasmine.createSpy();

    this.$el.on('typeahead:beforefiz', spy);

    expect(this.eventBus.before('fiz')).toBe(false);
    expect(spy).toHaveBeenCalled();
  });

  it('#before should return true if default was prevented', function() {
    var spy = jasmine.createSpy().andCallFake(prevent);

    this.$el.on('typeahead:beforefiz', spy);

    expect(this.eventBus.before('fiz')).toBe(true);
    expect(spy).toHaveBeenCalled();

    function prevent($e) { $e.preventDefault(); }
  });
});
