describe('options parser', function() {

  function build(o) {
    return oParser(_.mixin({
      datumTokenizer: $.noop,
      queryTokenizer: $.noop
    }, o || {}));
  }

  function prefetch(o) {
    return oParser({
      datumTokenizer: $.noop,
      queryTokenizer: $.noop,
      prefetch: _.mixin({
        url: '/example'
      }, o || {})
    });
  }

  function remote(o) {
    return oParser({
      datumTokenizer: $.noop,
      queryTokenizer: $.noop,
      remote: _.mixin({
        url: '/example'
      }, o || {})
    });
  }

  it('should throw exception if datumTokenizer is not set', function() {
    expect(parse).toThrow();
    function parse() { build({ datumTokenizer: null }); }
  });

  it('should throw exception if queryTokenizer is not set', function() {
    expect(parse).toThrow();
    function parse() { build({ queryTokenizer: null }); }
  });

  it('should wrap sorter', function() {
    var o = build({ sorter: function(a, b) {  return a -b; } });
    expect(o.sorter([2, 1, 3])).toEqual([1, 2, 3]);
  });

  it('should default sorter to identity function', function() {
    var o = build();
    expect(o.sorter([2, 1, 3])).toEqual([2, 1, 3]);
  });

  describe('local', function() {
    it('should default to empty array', function() {
      var o = build();
      expect(o.local).toEqual([]);
    });

    it('should support function', function() {
      var o = build({ local: function() { return [1]; } });
      expect(o.local).toEqual([1]);
    });

    it('should support arrays', function() {
      var o = build({ local: [1] });
      expect(o.local).toEqual([1]);
    });
  });

  describe('prefetch', function() {
    it('should throw exception if url is not set', function() {
      expect(parse).toThrow();
      function parse() { prefetch({ url: null }); }
    });

    it('should support simple string format', function() {
      expect(build({ prefetch: '/prefetch' }).prefetch).toBeDefined();
    });

    it('should default ttl to 1 day', function() {
      var o = prefetch();
      expect(o.prefetch.ttl).toBe(86400000);
    });

    it('should default cache to true', function() {
      var o = prefetch();
      expect(o.prefetch.cache).toBe(true);
    });

    it('should default transform to identiy function', function() {
      var o = prefetch();
      expect(o.prefetch.transform('foo')).toBe('foo');
    });

    it('should default cacheKey to url', function() {
      var o = prefetch();
      expect(o.prefetch.cacheKey).toBe(o.prefetch.url);
    });

    it('should default transport to jQuery.ajax', function() {
      var o = prefetch();
      expect(o.prefetch.transport).toBe($.ajax);
    });

    it('should prepend verison to thumbprint', function() {
      var o = prefetch();
      expect(o.prefetch.thumbprint).toBe('%VERSION%');

      o = prefetch({ thumbprint: 'foo' });
      expect(o.prefetch.thumbprint).toBe('%VERSION%foo');
    });

    it('should wrap custom transport to be deferred compatible', function() {
      var o, errDeferred, successDeferred;

      o = prefetch({ transport: errTransport });
      errDeferred = o.prefetch.transport('q');

      o = prefetch({ transport: successTransport });
      successDeferred = o.prefetch.transport('q');

      waits(0);
      runs(function() {
        expect(errDeferred.isRejected()).toBe(true);
        expect(successDeferred.isResolved()).toBe(true);
      });

      function errTransport(q, success, error) { error(); }
      function successTransport(q, success, error) { success(); }
    });
  });

  describe('remote', function() {
    it('should throw exception if url is not set', function() {
      expect(parse).toThrow();
      function parse() { remote({ url: null }); }
    });

    it('should support simple string format', function() {
      expect(build({ remote: '/remote' }).remote).toBeDefined();
    });

    it('should default transform to identiy function', function() {
      var o = remote();
      expect(o.remote.transform('foo')).toBe('foo');
    });

    it('should default transport to jQuery.ajax', function() {
      var o = remote();
      expect(o.remote.transport).toBe($.ajax);
    });

    it('should default limiter to debouce', function() {
      var o = remote();
      expect(o.remote.limiter.name).toBe('debounce');
    });

    it('should default prepare to identity function', function() {
      var o = remote();
      expect(o.remote.prepare('q', { url: '/foo' })).toEqual({ url: '/foo' });
    });

    it('should support wildcard for prepare', function() {
      var o = remote({ wildcard: '%FOO' });
      expect(o.remote.prepare('=', { url: '/%FOO' })).toEqual({ url: '/%3D' });
    });

    it('should support replace for prepare', function() {
      var o = remote({ replace: function() { return '/bar'; } });
      expect(o.remote.prepare('q', { url: '/foo' })).toEqual({ url: '/bar' });
    });

    it('should should rateLimitBy for limiter', function() {
      var o = remote({ rateLimitBy: 'throttle' });
      expect(o.remote.limiter.name).toBe('throttle');
    });

    it('should wrap custom transport to be deferred compatible', function() {
      var o, errDeferred, successDeferred;

      o = remote({ transport: errTransport });
      errDeferred = o.remote.transport('q');

      o = remote({ transport: successTransport });
      successDeferred = o.remote.transport('q');

      waits(0);
      runs(function() {
        expect(errDeferred.isRejected()).toBe(true);
        expect(successDeferred.isResolved()).toBe(true);
      });

      function errTransport(q, success, error) { error(); }
      function successTransport(q, success, error) { success(); }
    });
  });
});
