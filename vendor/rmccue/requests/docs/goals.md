Goals
=====

1. **Simple interface**

   Requests is designed to provide a simple, unified interface to making
   requests, regardless of what is available on the system. This means not worrying.

2. **Fully tested code**

   Requests strives to have 90%+ code coverage from the unit tests, aiming for
   the ideal 100%. Introducing new features always means introducing new tests

   (Note: some parts of the code are not covered by design. These sections are
   marked with `@codeCoverageIgnore` tags)

3. **Maximum compatibility**

   No matter what you have installed on your system, you should be able to run
   Requests. We use cURL if it's available, and fallback to sockets otherwise.
   We require only a baseline of PHP 5.2, leaving the choice of PHP minimum
   requirement fully in your hands, and giving you the ability to support many
   more hosts.

4. **No dependencies**

   Requests is designed to be entirely self-contained and doesn't require
   anything else at all. You can run Requests on an entirely stock PHP build
   without any additional extensions outside the standard library.
