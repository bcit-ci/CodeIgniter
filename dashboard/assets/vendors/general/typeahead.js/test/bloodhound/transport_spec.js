describe('Transport', function() {

  beforeEach(function() {
    jasmine.Ajax.useMock();
    jasmine.Clock.useMock();

    this.transport = new Transport({ transport: $.ajax });
  });

  afterEach(function() {
    // run twice to flush out  on-deck requests
    $.each(ajaxRequests, drop);
    $.each(ajaxRequests, drop);

    clearAjaxRequests();
    Transport.resetCache();

    function drop(i, req) {
      req.readyState !== 4 && req.response(fixtures.ajaxResps.ok);
    }
  });

  it('should use jQuery.ajax as the default transport mechanism', function() {
    var req, resp = fixtures.ajaxResps.ok, spy = jasmine.createSpy();

    this.transport.get('/test', spy);

    req = mostRecentAjaxRequest();
    req.response(resp);

    expect(req.url).toBe('/test');
    expect(spy).toHaveBeenCalledWith(null, resp.parsed);
  });

  it('should respect maxPendingRequests configuration', function() {
    for (var i = 0; i < 10; i++) {
      this.transport.get('/test' + i, $.noop);
    }

    expect(ajaxRequests.length).toBe(6);
  });

  it('should support rate limiting', function() {
    this.transport = new Transport({ transport: $.ajax, limiter: limiter });

    for (var i = 0; i < 5; i++) {
      this.transport.get('/test' + i, $.noop);
    }

    jasmine.Clock.tick(100);
    expect(ajaxRequests.length).toBe(1);

    function limiter(fn) { return _.debounce(fn, 20); }
  });

  it('should cache most recent requests', function() {
    var spy1 = jasmine.createSpy(), spy2 = jasmine.createSpy();

    this.transport.get('/test1', $.noop);
    mostRecentAjaxRequest().response(fixtures.ajaxResps.ok);

    this.transport.get('/test2', $.noop);
    mostRecentAjaxRequest().response(fixtures.ajaxResps.ok1);

    expect(ajaxRequests.length).toBe(2);

    this.transport.get('/test1', spy1);
    this.transport.get('/test2', spy2);

    jasmine.Clock.tick(0);

    // no ajax requests were made on subsequent requests
    expect(ajaxRequests.length).toBe(2);

    expect(spy1).toHaveBeenCalledWith(null, fixtures.ajaxResps.ok.parsed);
    expect(spy2).toHaveBeenCalledWith(null, fixtures.ajaxResps.ok1.parsed);
  });

  it('should not cache requests if cache option is false', function() {
    this.transport = new Transport({ transport: $.ajax, cache: false });

    this.transport.get('/test1', $.noop);
    mostRecentAjaxRequest().response(fixtures.ajaxResps.ok);
    this.transport.get('/test1', $.noop);
    mostRecentAjaxRequest().response(fixtures.ajaxResps.ok);

    expect(ajaxRequests.length).toBe(2);
  });

  it('should prevent dog pile', function() {
    var spy1 = jasmine.createSpy(), spy2 = jasmine.createSpy();

    this.transport.get('/test1', spy1);
    this.transport.get('/test1', spy2);

    mostRecentAjaxRequest().response(fixtures.ajaxResps.ok);

    expect(ajaxRequests.length).toBe(1);

    waitsFor(function() { return spy1.callCount && spy2.callCount; });

    runs(function() {
      expect(spy1).toHaveBeenCalledWith(null, fixtures.ajaxResps.ok.parsed);
      expect(spy2).toHaveBeenCalledWith(null, fixtures.ajaxResps.ok.parsed);
    });
  });

  it('should always make a request for the last call to #get', function() {
    var spy = jasmine.createSpy();

    for (var i = 0; i < 6; i++) {
      this.transport.get('/test' + i, $.noop);
    }

    this.transport.get('/test' + i, spy);
    expect(ajaxRequests.length).toBe(6);

    _.each(ajaxRequests, function(req) {
      req.response(fixtures.ajaxResps.ok);
    });

    expect(ajaxRequests.length).toBe(7);
    mostRecentAjaxRequest().response(fixtures.ajaxResps.ok);

    expect(spy).toHaveBeenCalled();
  });

  it('should invoke the callback with err set to true on failure', function() {
    var req, resp = fixtures.ajaxResps.err, spy = jasmine.createSpy();

    this.transport.get('/test', spy);

    req = mostRecentAjaxRequest();
    req.response(resp);

    expect(req.url).toBe('/test');
    expect(spy).toHaveBeenCalledWith(true);
  });

  it('should not send cancelled requests', function() {
    this.transport = new Transport({ transport: $.ajax, limiter: limiter });

    this.transport.get('/test', $.noop);
    this.transport.cancel();

    jasmine.Clock.tick(100);
    expect(ajaxRequests.length).toBe(0);

    function limiter(fn) { return _.debounce(fn, 20); }
  });

  it('should not send outdated requests', function() {
    this.transport = new Transport({ transport: $.ajax, limiter: limiter });

    // warm cache
    this.transport.get('/test1', $.noop);
    jasmine.Clock.tick(100);
    mostRecentAjaxRequest().response(fixtures.ajaxResps.ok);

    expect(mostRecentAjaxRequest().url).toBe('/test1');
    expect(ajaxRequests.length).toBe(1);

    // within the same rate-limit cycle, request test2 and test1. test2 becomes
    // outdated after test1 is requested and no request is sent for test1
    // because it's a cache hit
    this.transport.get('/test2', $.noop);
    this.transport.get('/test1', $.noop);

    jasmine.Clock.tick(100);

    expect(ajaxRequests.length).toBe(1);

    function limiter(fn) { return _.debounce(fn, 20); }
  });
});
