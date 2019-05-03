var fixtures = fixtures || {};

fixtures.ajaxResps = {
  ok: {
    status: 200,
    responseText: '[{ "value": "big" }, { "value": "bigger" }, { "value": "biggest" }, { "value": "small" }, { "value": "smaller" }, { "value": "smallest" }]'
  },
  ok1: {
    status: 200,
    responseText: '["dog", "cat", "moose"]'
  },
  err: {
    status: 500
  }
};

$.each(fixtures.ajaxResps, function(i, resp) {
  resp.responseText && (resp.parsed = $.parseJSON(resp.responseText));
});
