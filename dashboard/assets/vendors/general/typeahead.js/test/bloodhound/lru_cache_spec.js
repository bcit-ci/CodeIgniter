describe('LruCache', function() {

  beforeEach(function() {
    this.cache = new LruCache(3);
  });

  it('should make entries retrievable by their keys', function() {
    var key = 'key', val = 42;

    this.cache.set(key, val);
    expect(this.cache.get(key)).toBe(val);
  });

  it('should return undefined if key has not been set', function() {
    expect(this.cache.get('wat?')).toBeUndefined();
  });

  it('should hold up to maxSize entries', function() {
    this.cache.set('one', 1);
    this.cache.set('two', 2);
    this.cache.set('three', 3);
    this.cache.set('four', 4);

    expect(this.cache.get('one')).toBeUndefined();
    expect(this.cache.get('two')).toBe(2);
    expect(this.cache.get('three')).toBe(3);
    expect(this.cache.get('four')).toBe(4);
  });

  it('should evict lru entry if cache is full', function() {
    this.cache.set('one', 1);
    this.cache.set('two', 2);
    this.cache.set('three', 3);
    this.cache.get('one');
    this.cache.set('four', 4);

    expect(this.cache.get('one')).toBe(1);
    expect(this.cache.get('two')).toBeUndefined();
    expect(this.cache.get('three')).toBe(3);
    expect(this.cache.get('four')).toBe(4);
    expect(this.cache.size).toBe(3);
  });
});
