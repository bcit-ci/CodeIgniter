###################
Server Requirements
###################

`PHP <http://www.php.net/>`_ version 5.4 or newer is recommended.

It should work on 5.2.4 as well, but we strongly advise you NOT to run
such old versions of PHP, because of potential security and performance
issues, as well as missing features.

A database is required for most web application programming.
Currently supported databases are:

  - MySQL (5.1+) via the *mysql* (deprecated), *mysqli* and *pdo* drivers
  - Oracle via the *oci8* and *pdo* drivers
  - PostgreSQL via the *postgre* and *pdo* drivers
  - MS SQL via the *mssql*, *sqlsrv* (version 2005 and above only) and *pdo* drivers
  - SQLite via the *sqlite* (version 2), *sqlite3* (version 3) and *pdo* drivers
  - CUBRID via the *cubrid* and *pdo* drivers
  - Interbase/Firebird via the *ibase* and *pdo* drivers
  - ODBC via the *odbc* and *pdo* drivers (you should know that ODBC is actually an abstraction layer)