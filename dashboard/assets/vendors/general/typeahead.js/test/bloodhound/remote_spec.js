describe('Remote', function() {

  beforeEach(function() {
    jasmine.Transport.useMock();

    this.remote = new Remote({
      url: '/test?q=%QUERY',
      prepare: function(x) { return x; },
      transform: function(x) { return x; }
    });

    this.transport = this.remote.transport;
  });

  describe('#cancelLastRequest', function() {
    it('should cancel last request', function() {
      this.remote.cancelLastRequest();
      expect(this.transport.cancel).toHaveBeenCalled();
    });
  });

  describe('#get', function() {
    it('should have sensible default request settings', function() {
      var spy;

      spy = jasmine.createSpy();
      spyOn(this.remote, 'prepare');

      this.remote.get('foo', spy);

      expect(this.remote.prepare).toHaveBeenCalledWith('foo', {
        url: '/test?q=%QUERY',
        type: 'GET',
        dataType: 'json'
      });
    });

    it('should transform request settings with prepare', function() {
      var spy;

      spy = jasmine.createSpy();
      spyOn(this.remote, 'prepare').andReturn([{ foo: 'bar' }]);

      this.remote.get('foo', spy);

      expect(this.transport.get)
      .toHaveBeenCalledWith([{ foo: 'bar' }], jasmine.any(Function));
    });

    it('should transform response with transform', function() {
      var spy;

      spy = jasmine.createSpy();
      spyOn(this.remote, 'transform').andReturn([{ foo: 'bar' }]);
      this.transport.get.andCallFake(function(_, cb) { cb(null, {}); });

      this.remote.get('foo', spy);

      expect(spy).toHaveBeenCalledWith([{ foo: 'bar' }]);
    });

    it('should return empty array on error', function() {
      var spy;

      spy = jasmine.createSpy();
      this.transport.get.andCallFake(function(_, cb) { cb(true); });

      this.remote.get('foo', spy);

      expect(spy).toHaveBeenCalledWith([]);
    });
  });
});
