Changelog
=========

1.6.1
-----
- [Fix compatibility with HHVM][#121] - Using HHVM with Requests would
  previously cause either exceptions with SSL or segfaults with the cURL
  handler. Props Ozh for his work here.

[#121]: https://github.com/rmccue/Requests/issues/121

1.6.0
-----
- [Add multiple request support][#23] - Send multiple HTTP requests with both
  fsockopen and cURL, transparently falling back to synchronous when
  not supported.

- [Add proxy support][#70] - HTTP proxies are now natively supported via a
  [high-level API][docs/proxy]. Major props to Ozh for his fantastic work
  on this.

- [Verify host name for SSL requests][#63] - Requests is now the first and only
  standalone HTTP library to fully verify SSL hostnames even with socket
  connections. Thanks to Michael Adams, Dion Hulse, Jon Cave, and Pádraic Brady
  for reviewing the crucial code behind this.

- [Add cookie support][#64] - Adds built-in support for cookies (built entirely
  as a high-level API)

- [Add sessions][#62] - To compliment cookies, [sessions][docs/usage-advanced]
  can be created with a base URL and default options, plus a shared cookie jar.

- Add [PUT][#1], [DELETE][#3], and [PATCH][#2] request support

- [Add Composer support][#6] - You can now install Requests via the
  `rmccue/requests` package on Composer

[docs/proxy]: http://requests.ryanmccue.info/docs/proxy.html
[docs/usage-advanced]: http://requests.ryanmccue.info/docs/usage-advanced.html

[#1]: https://github.com/rmccue/Requests/issues/1
[#2]: https://github.com/rmccue/Requests/issues/2
[#3]: https://github.com/rmccue/Requests/issues/3
[#6]: https://github.com/rmccue/Requests/issues/6
[#9]: https://github.com/rmccue/Requests/issues/9
[#23]: https://github.com/rmccue/Requests/issues/23
[#62]: https://github.com/rmccue/Requests/issues/62
[#63]: https://github.com/rmccue/Requests/issues/63
[#64]: https://github.com/rmccue/Requests/issues/64
[#70]: https://github.com/rmccue/Requests/issues/70

[View all changes][https://github.com/rmccue/Requests/compare/v1.5.0...v1.6.0]

1.5.0
-----
Initial release!