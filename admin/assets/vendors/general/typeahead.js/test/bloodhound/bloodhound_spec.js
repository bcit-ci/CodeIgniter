describe('Bloodhound', function() {

  function build(o) {
    return new Bloodhound(_.mixin({
      datumTokenizer: datumTokenizer,
      queryTokenizer: queryTokenizer
    }, o || {}));
  }

  beforeEach(function() {
    jasmine.Remote.useMock();
    jasmine.Prefetch.useMock();
    jasmine.Transport.useMock();
    jasmine.PersistentStorage.useMock();
  });

  afterEach(function() {
    clearAjaxRequests();
  });

  describe('#initialize', function() {
    beforeEach(function() {
      this.bloodhound = build({ initialize: false });
      spyOn(this.bloodhound, '_initialize').andCallThrough();
    });

    it('should not initialize if intialize option is false', function() {
      expect(this.bloodhound._initialize).not.toHaveBeenCalled();
    });

    it('should not support reinitialization by default', function() {
      var p1, p2;

      p1 = this.bloodhound.initialize();
      p2 = this.bloodhound.initialize();

      expect(p1).toBe(p2);
      expect(this.bloodhound._initialize.callCount).toBe(1);
    });

    it('should reinitialize if reintialize flag is true', function() {
      var p1, p2;

      p1 = this.bloodhound.initialize();
      p2 = this.bloodhound.initialize(true);

      expect(p1).not.toBe(p2);
      expect(this.bloodhound._initialize.callCount).toBe(2);
    });

    it('should clear the index', function() {
      this.bloodhound = build({ initialize: false, prefetch: '/prefetch' });
      spyOn(this.bloodhound, 'clear');
      this.bloodhound.initialize();

      expect(this.bloodhound.clear).toHaveBeenCalled();
    });

    it('should load data from prefetch cache if available', function() {
      this.bloodhound = build({ initialize: false, prefetch: '/prefetch' });
      this.bloodhound.prefetch.fromCache.andReturn(fixtures.serialized.simple);
      this.bloodhound.initialize();

      expect(this.bloodhound.all()).toEqual(fixtures.data.simple);
      expect(this.bloodhound.prefetch.fromNetwork).not.toHaveBeenCalled();
    });

    it('should load data from prefetch network as fallback', function() {
      this.bloodhound = build({ initialize: false, prefetch: '/prefetch' });
      this.bloodhound.prefetch.fromCache.andReturn(null);
      this.bloodhound.prefetch.fromNetwork.andCallFake(fakeFromNetwork);
      this.bloodhound.initialize();

      expect(this.bloodhound.all()).toEqual(fixtures.data.simple);

      function fakeFromNetwork(cb) { cb(null, fixtures.data.simple); }
    });

    it('should store prefetch network data in the prefetch cache', function() {
      this.bloodhound = build({ initialize: false, prefetch: '/prefetch' });
      this.bloodhound.prefetch.fromCache.andReturn(null);
      this.bloodhound.prefetch.fromNetwork.andCallFake(fakeFromNetwork);
      this.bloodhound.initialize();

      expect(this.bloodhound.prefetch.store)
      .toHaveBeenCalledWith(fixtures.serialized.simple);

      function fakeFromNetwork(cb) { cb(null, fixtures.data.simple); }
    });

    it('should add local after prefetch is loaded', function() {
      this.bloodhound = build({
        initialize: false,
        local: [{ foo: 'bar' }],
        prefetch: '/prefetch'
      });
      this.bloodhound.prefetch.fromNetwork.andCallFake(fakeFromNetwork);

      expect(this.bloodhound.all()).toEqual([]);
      this.bloodhound.initialize();
      expect(this.bloodhound.all()).toEqual([{ foo: 'bar' }]);

      function fakeFromNetwork(cb) { cb(null, []); }
    });
  });

  describe('#add', function() {
    it('should add datums to search index', function() {
      var spy = jasmine.createSpy();

      this.bloodhound = build().add(fixtures.data.simple);

      this.bloodhound.search('big', spy);

      expect(spy).toHaveBeenCalledWith([
        { value: 'big' },
        { value: 'bigger' },
        { value: 'biggest' }
      ]);
    });
  });

  describe('#get', function() {
    beforeEach(function() {
      this.bloodhound = build({
        identify: function(d) { return d.value; },
        local: fixtures.data.simple
      });
    });

    it('should support array signature', function() {
      expect(this.bloodhound.get(['big', 'bigger'])).toEqual([
        { value: 'big' },
        { value: 'bigger' }
      ]);
    });

    it('should support splat signature', function() {
      expect(this.bloodhound.get('big', 'bigger')).toEqual([
        { value: 'big' },
        { value: 'bigger' }
      ]);
    });

    it('should return nothing for unknown ids', function() {
      expect(this.bloodhound.get('big', 'foo', 'bigger')).toEqual([
        { value: 'big' },
        { value: 'bigger' }
      ]);
    });
  });

  describe('#clear', function() {
    it('should remove all datums to search index', function() {
      var spy = jasmine.createSpy();

      this.bloodhound = build({ local: fixtures.data.simple }).clear();

      this.bloodhound.search('big', spy);

      expect(spy).toHaveBeenCalledWith([]);
    });
  });

  describe('#clearPrefetchCache', function() {
    it('should clear persistent storage', function() {
      this.bloodhound = build({ prefetch: '/prefetch' }).clearPrefetchCache();
      expect(this.bloodhound.prefetch.clear).toHaveBeenCalled();
    });
  });

  describe('#clearRemoteCache', function() {
    it('should clear remote request cache', function() {
      spyOn(Transport, 'resetCache');
      this.bloodhound = build({ remote: '/remote' }).clearRemoteCache();
      expect(Transport.resetCache).toHaveBeenCalled();
    });
  });

  describe('#all', function() {
    it('should return all local results', function() {
      this.bloodhound = build({ local: fixtures.data.simple });
      expect(this.bloodhound.all()).toEqual(fixtures.data.simple);
    });
  });

  describe('#search – local', function() {
    it('should return sync matches', function() {
      var spy = jasmine.createSpy();

      this.bloodhound = build({ local: fixtures.data.simple });

      this.bloodhound.search('big', spy);

      expect(spy).toHaveBeenCalledWith([
        { value: 'big' },
        { value: 'bigger' },
        { value: 'biggest' }
      ]);
    });
  });

  describe('#search – prefetch', function() {
    it('should return sync matches', function() {
      var spy = jasmine.createSpy();

      this.bloodhound = build({ initialize: false, prefetch: '/prefetch' });
      this.bloodhound.prefetch.fromCache.andReturn(fixtures.serialized.simple);
      this.bloodhound.initialize();

      this.bloodhound.search('big', spy);

      expect(spy).toHaveBeenCalledWith([
        { value: 'big' },
        { value: 'bigger' },
        { value: 'biggest' }
      ]);
    });
  });

  describe('#search – remote', function() {
    it('should return async matches', function() {
      var spy = jasmine.createSpy();

      this.bloodhound = build({ remote: '/remote' });
      this.bloodhound.remote.get.andCallFake(fakeGet);
      this.bloodhound.search('dog', $.noop, spy);

      expect(spy.callCount).toBe(1);

      function fakeGet(o, cb) { cb(fixtures.data.animals); }
    });
  });

  describe('#search – integration', function() {
    it('should backfill when local/prefetch is not sufficient', function() {
      var syncSpy, asyncSpy;

      syncSpy = jasmine.createSpy();
      asyncSpy = jasmine.createSpy();

      this.bloodhound = build({
        sufficient: 3,
        local: fixtures.data.simple,
        remote: '/remote'
      });
      this.bloodhound.remote.get.andCallFake(fakeGet);

      this.bloodhound.search('big', syncSpy, asyncSpy);

      expect(syncSpy).toHaveBeenCalledWith([
        { value: 'big' },
        { value: 'bigger' },
        { value: 'biggest' }
      ]);
      expect(asyncSpy).not.toHaveBeenCalled();

      this.bloodhound.search('bigg', syncSpy, asyncSpy);

      expect(syncSpy).toHaveBeenCalledWith([
        { value: 'bigger' },
        { value: 'biggest' }
      ]);
      expect(asyncSpy).toHaveBeenCalledWith(fixtures.data.animals);

      function fakeGet(o, cb) { cb(fixtures.data.animals); }
    });

    it('should remove duplicates from backfill', function() {
      var syncSpy, asyncSpy;

      syncSpy = jasmine.createSpy();
      asyncSpy = jasmine.createSpy();

      this.bloodhound = build({
        identify: function(d) { return d.value; },
        local: fixtures.data.animals,
        remote: '/remote'
      });
      this.bloodhound.remote.get.andCallFake(fakeGet);

      this.bloodhound.search('dog', syncSpy, asyncSpy);

      expect(syncSpy).toHaveBeenCalledWith([{ value: 'dog' }]);
      expect(asyncSpy).toHaveBeenCalledWith([
        { value: 'cat' },
        { value: 'moose' }
      ]);

      function fakeGet(o, cb) { cb(fixtures.data.animals); }
    });
  });

  // helper functions
  // ----------------

  function datumTokenizer(d) { return $.trim(d.value).split(/\s+/); }
  function queryTokenizer(s) { return $.trim(s).split(/\s+/); }
});
