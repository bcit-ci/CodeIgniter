Bloodhound
==========

Bloodhound is the typeahead.js suggestion engine. Bloodhound is robust, 
flexible, and offers advanced functionalities such as prefetching, intelligent
caching, fast lookups, and backfilling with remote data.

Table of Contents
-----------------

* [Features](#features)
* [Usage](#usage)
  * [API](#api)
  * [Options](#options)
  * [Prefetch](#prefetch)
  * [Remote](#remote)

Features
--------

* Works with hardcoded data
* Prefetches data on initialization to reduce suggestion latency
* Uses local storage intelligently to cut down on network requests
* Backfills suggestions from a remote source
* Rate-limits and caches network requests to remote sources to lighten the load

Usage
-----

### API

* [`new Bloodhound(options)`](#new-bloodhoundoptions)
* [`Bloodhound.noConflict()`](#bloodhoundnoconflict)
* [`Bloodhound#initialize(reinitialize)`](#bloodhoundinitializereinitialize)
* [`Bloodhound#add(data)`](#bloodhoundadddata)
* [`Bloodhound#get(ids)`](#bloodhoundgetids)
* [`Bloodhound#search(query, sync, async)`](#bloodhoundsearchquery-sync-async)
* [`Bloodhound#clear()`](#bloodhoundclear)

#### new Bloodhound(options)

The constructor function. It takes an [options hash](#options) as its only 
argument.

```javascript
var engine = new Bloodhound({
  local: ['dog', 'pig', 'moose'],
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  datumTokenizer: Bloodhound.tokenizers.whitespace
});
```

#### Bloodhound.noConflict()

Returns a reference to `Bloodhound` and reverts `window.Bloodhound` to its 
previous value. Can be used to avoid naming collisions. 

```javascript
var Dachshund = Bloodhound.noConflict();
```

#### Bloodhound#initialize(reinitialize) 

Kicks off the initialization of the suggestion engine. Initialization entails
adding the data provided by `local` and `prefetch` to the internal search 
index as well as setting up transport mechanism used by `remote`. Before 
`#initialize` is called, the `#get` and `#search` methods will effectively be
no-ops.

Note, unless the `initialize` option is `false`, this method is implicitly
called by the constructor.

```javascript
var engine = new Bloodhound({
  initialize: false,
  local: ['dog', 'pig', 'moose'],
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  datumTokenizer: Bloodhound.tokenizers.whitespace
});

var promise = engine.initialize();

promise
.done(function() { console.log('ready to go!'); })
.fail(function() { console.log('err, something went wrong :('); });
```

After initialization, how subsequent invocations of `#initialize` behave 
depends on the `reinitialize` argument. If `reinitialize` is falsy, the
method will not execute the initialization logic and will just return the same 
jQuery promise returned by the initial invocation. If `reinitialize` is truthy,
the method will behave as if it were being called for the first time.

```javascript
var promise1 = engine.initialize();
var promise2 = engine.initialize();
var promise3 = engine.initialize(true);

assert(promise1 === promise2);
assert(promise3 !== promise1 && promise3 !== promise2);
```

<!-- section links -->

[jQuery promise]: http://api.jquery.com/Types/#Promise

#### Bloodhound#add(data)

Takes one argument, `data`, which is expected to be an array. The data passed
in will get added to the internal search index.

```javascript
engine.add([{ val: 'one' }, { val: 'two' }]);
```

#### Bloodhound#get(ids)

Returns the data in the local search index corresponding to `ids`.

```javascript
  var engine = new Bloodhound({
    local: [{ id: 1, name: 'dog' }, { id: 2, name: 'pig' }],
    identify: function(obj) { return obj.id; },
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    datumTokenizer: Bloodhound.tokenizers.whitespace
  });

  engine.get([1, 3]); // [{ id: 1, name: 'dog' }, null]
```

#### Bloodhound#search(query, sync, async)

Returns the data that matches `query`. Matches found in the local search index
will be passed to the `sync` callback. If the data passed to `sync` doesn't 
contain at least `sufficient` number of datums, `remote` data will be requested 
and then passed to the `async` callback.

```javascript
bloodhound.get(myQuery, sync, async);

function sync(datums) {
  console.log('datums from `local`, `prefetch`, and `#add`');
  console.log(datums);
}

function async(datums) {
  console.log('datums from `remote`');
  console.log(datums);
}
```

#### Bloodhound#clear()

Clears the internal search index that's powered by `local`, `prefetch`, and 
`#add`.

```javascript
engine.clear();
```

### Options

When instantiating a Bloodhound suggestion engine, there are a number of 
options you can configure.

* `datumTokenizer` – A function with the signature `(datum)` that transforms a
  datum into an array of string tokens. **Required**.

* `queryTokenizer` – A function with the signature `(query)` that transforms a
  query into an array of string tokens. **Required**.

* `initialize` – If set to `false`, the Bloodhound instance will not be 
  implicitly initialized by the constructor function. Defaults to `true`.

* `identify` – Given a datum, this function is expected to return a unique id
  for it. Defaults to `JSON.stringify`. Note that it is **highly recommended**
  to override this option.

* `sufficient` – If the number of datums provided from the internal search 
  index is less than `sufficient`, `remote` will be used to backfill search
  requests triggered by calling `#search`. Defaults to `5`.

* `sorter` – A [compare function] used to sort data returned from the internal
  search index.

* `local` – An array of data or a function that returns an array of data. The 
  data will be added to the internal search index when `#initialize` is called.

* `prefetch` – Can be a URL to a JSON file containing an array of data or, if 
  more configurability is needed, a [prefetch options hash](#prefetch).

* `remote` – Can be a URL to fetch data from when the data provided by 
  the internal search index is insufficient or, if more configurability is 
  needed, a [remote options hash](#remote).

<!-- section links -->

[compare function]: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/sort

### Prefetch

Prefetched data is fetched and processed on initialization. If the browser 
supports local storage, the processed data will be cached there to 
prevent additional network requests on subsequent page loads.

**WARNING:** While it's possible to get away with it for smaller data sets, 
prefetched data isn't meant to contain entire sets of data. Rather, it should 
act as a first-level cache. Ignoring this warning means you'll run the risk of 
hitting [local storage limits].

When configuring `prefetch`, the following options are available.

* `url` – The URL prefetch data should be loaded from. **Required.**

* `cache` – If `false`, will not attempt to read or write to local storage and
  will always load prefetch data from `url` on initialization.  Defaults to 
  `true`.

* `ttl` – The time (in milliseconds) the prefetched data should be cached in 
  local storage. Defaults to `86400000` (1 day).

* `cacheKey` – The key that data will be stored in local storage under. 
  Defaults to value of `url`.

* `thumbprint` – A string used for thumbprinting prefetched data. If this
  doesn't match what's stored in local storage, the data will be refetched.

* `prepare` – A function that provides a hook to allow you to prepare the 
  settings object passed to `transport` when a request is about to be made. 
  The function signature should be `prepare(settings)` where `settings` is the 
  default settings object created internally by the Bloodhound instance. The 
  `prepare` function should return a settings object. Defaults to the 
  [identity function].

* `transform` – A function with the signature `transform(response)` that allows
  you to transform the prefetch response before the Bloodhound instance operates 
  on it. Defaults to the [identity function].

<!-- section links -->

[local storage limits]: http://stackoverflow.com/a/2989317
[identity function]: http://en.wikipedia.org/wiki/Identity_function

### Remote

Bloodhound only goes to the network when the internal search engine cannot 
provide a sufficient number of results. In order to prevent an obscene number 
of requests being made to the remote endpoint, requests are rate-limited.

When configuring `remote`, the following options are available.

* `url` – The URL remote data should be loaded from. **Required.**

* `prepare` – A function that provides a hook to allow you to prepare the 
  settings object passed to `transport` when a request is about to be made. 
  The function signature should be `prepare(query, settings)`, where `query` is
  the query `#search` was called with and `settings` is the default settings
  object created internally by the Bloodhound instance. The `prepare` function
  should return a settings object. Defaults to the [identity function].

* `wildcard` – A convenience option for `prepare`. If set, `prepare` will be a
  function that replaces the value of this option in `url` with the URI encoded
  query.

* `rateLimitBy` – The method used to rate-limit network requests. Can be either 
  `debounce` or `throttle`. Defaults to `debounce`.

* `rateLimitWait` – The time interval in milliseconds that will be used by 
  `rateLimitBy`. Defaults to `300`.

* `transform` – A function with the signature `transform(response)` that allows
  you to transform the remote response before the Bloodhound instance operates 
  on it. Defaults to the [identity function].

<!-- section links -->

[identity function]: http://en.wikipedia.org/wiki/Identity_function
