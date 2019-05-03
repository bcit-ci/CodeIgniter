describe('tokenizers', function() {

  it('.whitespace should tokenize on whitespace', function() {
    var tokens = tokenizers.whitespace('big-deal ok');
    expect(tokens).toEqual(['big-deal', 'ok']);
  });

  it('.whitespace should treat null as empty string', function() {
    var tokens = tokenizers.whitespace(null);
    expect(tokens).toEqual([]);
  });

  it('.whitespace should treat undefined as empty string', function() {
    var tokens = tokenizers.whitespace(undefined);
    expect(tokens).toEqual([]);
  });

  it('.nonword should tokenize on non-word characters', function() {
    var tokens = tokenizers.nonword('big-deal ok');
    expect(tokens).toEqual(['big', 'deal', 'ok']);
  });

  it('.nonword should treat null as empty string', function() {
    var tokens = tokenizers.nonword(null);
    expect(tokens).toEqual([]);
  });

  it('.nonword should treat undefined as empty string', function() {
    var tokens = tokenizers.nonword(undefined);
    expect(tokens).toEqual([]);
  });

  it('.obj.whitespace should tokenize on whitespace', function() {
    var t = tokenizers.obj.whitespace('val');
    var tokens = t({ val: 'big-deal ok' });

    expect(tokens).toEqual(['big-deal', 'ok']);
  });

  it('.obj.whitespace should accept multiple properties', function() {
    var t = tokenizers.obj.whitespace('one', 'two');
    var tokens = t({ one: 'big-deal ok', two: 'buzz' });

    expect(tokens).toEqual(['big-deal', 'ok', 'buzz']);
  });

  it('.obj.whitespace should accept array', function() {
    var t = tokenizers.obj.whitespace(['one', 'two']);
    var tokens = t({ one: 'big-deal ok', two: 'buzz' });

    expect(tokens).toEqual(['big-deal', 'ok', 'buzz']);
  });

  it('.obj.nonword should tokenize on non-word characters', function() {
    var t = tokenizers.obj.nonword('val');
    var tokens = t({ val: 'big-deal ok' });

    expect(tokens).toEqual(['big', 'deal', 'ok']);
  });

  it('.obj.nonword should accept multiple properties', function() {
    var t = tokenizers.obj.nonword('one', 'two');
    var tokens = t({ one: 'big-deal ok', two: 'buzz' });

    expect(tokens).toEqual(['big', 'deal', 'ok', 'buzz']);
  });

  it('.obj.nonword should accept array', function() {
    var t = tokenizers.obj.nonword(['one', 'two']);
    var tokens = t({ one: 'big-deal ok', two: 'buzz' });

    expect(tokens).toEqual(['big', 'deal', 'ok', 'buzz']);
  });
});
