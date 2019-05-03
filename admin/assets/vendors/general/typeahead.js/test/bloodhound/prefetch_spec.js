describe('Prefetch', function() {

  function build(o) {
    return new Prefetch(_.mixin({
      url: '/prefetch',
      ttl: 3600,
      cache: true,
      thumbprint: '',
      cacheKey: 'cachekey',
      prepare: function(x) { return x; },
      transform: function(x) { return x; },
      transport: $.ajax
    }, o || {}));
  }

  beforeEach(function() {
    jasmine.PersistentStorage.useMock();

    this.prefetch = build();
    this.storage = this.prefetch.storage;
    this.thumbprint = this.prefetch.thumbprint;
  });

  describe('#clear', function() {
    it('should clear cache storage', function() {
      this.prefetch.clear();
      expect(this.storage.clear).toHaveBeenCalled();
    });
  });

  describe('#store', function() {
    it('should store data in the storage cache', function() {
      this.prefetch.store({ foo: 'bar' });

      expect(this.storage.set)
      .toHaveBeenCalledWith('data', { foo: 'bar' }, 3600);
    });

    it('should store thumbprint in the storage cache', function() {
      this.prefetch.store({ foo: 'bar' });

      expect(this.storage.set)
      .toHaveBeenCalledWith('thumbprint', jasmine.any(String), 3600);
    });

    it('should store protocol in the storage cache', function() {
      this.prefetch.store({ foo: 'bar' });

      expect(this.storage.set)
      .toHaveBeenCalledWith('protocol', location.protocol, 3600);
    });

    it('should be noop if cache option is false', function() {
      this.prefetch = build({ cache: false });

      this.prefetch.store({ foo: 'bar' });

      expect(this.storage.set).not.toHaveBeenCalled();
    });
  });

  describe('#fromCache', function() {
    it('should return data if available', function() {
      this.storage.get
      .andCallFake(fakeStorageGet({ foo: 'bar' }, this.thumbprint));

      expect(this.prefetch.fromCache()).toEqual({ foo: 'bar' });
    });

    it('should return null if data is expired', function() {
      this.storage.get
      .andCallFake(fakeStorageGet({ foo: 'bar' }, 'foo'));

      expect(this.prefetch.fromCache()).toBeNull();
    });

    it('should return null if data does not exist', function() {
      this.storage.get
      .andCallFake(fakeStorageGet(null, this.thumbprint));

      expect(this.prefetch.fromCache()).toBeNull();
    });

    it('should return null if cache option is false', function() {
      this.prefetch = build({ cache: false });

      this.storage.get
      .andCallFake(fakeStorageGet({ foo: 'bar' }, this.thumbprint));

      expect(this.prefetch.fromCache()).toBeNull();
      expect(this.storage.get).not.toHaveBeenCalled();
    });
  });

  describe('#fromNetwork', function() {
    it('should have sensible default request settings', function() {
      var spy;

      spy = jasmine.createSpy();
      spyOn(this.prefetch, 'transport').andReturn($.Deferred());

      this.prefetch.fromNetwork(spy);

      expect(this.prefetch.transport).toHaveBeenCalledWith({
        url: '/prefetch',
        type: 'GET',
        dataType: 'json'
      });
    });

    it('should transform request settings with prepare', function() {
      var spy;

      spy = jasmine.createSpy();
      spyOn(this.prefetch, 'prepare').andReturn({ foo: 'bar' });
      spyOn(this.prefetch, 'transport').andReturn($.Deferred());

      this.prefetch.fromNetwork(spy);

      expect(this.prefetch.transport).toHaveBeenCalledWith({ foo: 'bar' });
    });

    it('should transform the response using transform', function() {
      var spy;

      this.prefetch = build({
        transform: function() { return { bar: 'foo' }; }
      });

      spy = jasmine.createSpy();
      spyOn(this.prefetch, 'transport')
      .andReturn($.Deferred().resolve({ foo: 'bar' }));

      this.prefetch.fromNetwork(spy);

      expect(spy).toHaveBeenCalledWith(null, { bar: 'foo' });
    });

    it('should invoke callback with data if success', function() {
      var spy;

      spy = jasmine.createSpy();
      spyOn(this.prefetch, 'transport')
      .andReturn($.Deferred().resolve({ foo: 'bar' }));

      this.prefetch.fromNetwork(spy);

      expect(spy).toHaveBeenCalledWith(null, { foo: 'bar' });
    });

    it('should invoke callback with err argument true if failure', function() {
      var spy;

      spy = jasmine.createSpy();
      spyOn(this.prefetch, 'transport').andReturn($.Deferred().reject());

      this.prefetch.fromNetwork(spy);

      expect(spy).toHaveBeenCalledWith(true);
    });
  });

  function fakeStorageGet(data, thumbprint, protocol) {
    return function(key) {
      var val;

      switch (key) {
        case 'data':
          val = data;
          break;
        case 'protocol':
          val = protocol || location.protocol;
          break;
        case 'thumbprint':
          val = thumbprint;
          break;
      }

      return val;
    };
  }
});
