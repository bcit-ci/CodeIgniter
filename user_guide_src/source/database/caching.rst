######################
Database Caching Class
######################

The Database Caching Class permits you to cache your queries as text
files for reduced database load.

.. important:: This class is initialized automatically by the database
	driver when caching is enabled. Do NOT load this class manually.

.. important:: Not all query result functions are available when you
	use caching. Please read this page carefully.

Enabling Caching
================

Caching is enabled in three steps:

-  Create a writable directory on your server where the cache files can
   be stored.
-  Set the path to your cache folder in your
   application/config/database.php file.
-  Enable the caching feature, either globally by setting the preference
   in your application/config/database.php file, or manually as
   described below.

Once enabled, caching will happen automatically whenever a page is
loaded that contains database queries.

How Does Caching Work?
======================

CodeIgniter's query caching system happens dynamically when your pages
are viewed. When caching is enabled, the first time a web page is
loaded, the query result object will be serialized and stored in a text
file on your server. The next time the page is loaded the cache file
will be used instead of accessing your database. Your database usage can
effectively be reduced to zero for any pages that have been cached.

Only read-type (SELECT) queries can be cached, since these are the only
type of queries that produce a result. Write-type (INSERT, UPDATE, etc.)
queries, since they don't generate a result, will not be cached by the
system.

Cache files DO NOT expire. Any queries that have been cached will remain
cached until you delete them. The caching system permits you clear
caches associated with individual pages, or you can delete the entire
collection of cache files. Typically you'll want to use the housekeeping
functions described below to delete cache files after certain events
take place, like when you've added new information to your database.

Will Caching Improve Your Site's Performance?
=============================================

Getting a performance gain as a result of caching depends on many
factors. If you have a highly optimized database under very little load,
you probably won't see a performance boost. If your database is under
heavy use you probably will see an improved response, assuming your
file-system is not overly taxed. Remember that caching simply changes
how your information is retrieved, shifting it from being a database
operation to a file-system one.

In some clustered server environments, for example, caching may be
detrimental since file-system operations are so intense. On single
servers in shared environments, caching will probably be beneficial.
Unfortunately there is no single answer to the question of whether you
should cache your database. It really depends on your situation.

How are Cache Files Stored?
===========================

CodeIgniter places the result of EACH query into its own cache file.
Sets of cache files are further organized into sub-folders corresponding
to your controller functions. To be precise, the sub-folders are named
identically to the first two segments of your URI (the controller class
name and function name).

For example, let's say you have a controller called blog with a function
called comments that contains three queries. The caching system will
create a cache folder called blog+comments, into which it will write
three cache files.

If you use dynamic queries that change based on information in your URI
(when using pagination, for example), each instance of the query will
produce its own cache file. It's possible, therefore, to end up with
many times more cache files than you have queries.

Managing your Cache Files
=========================

Since cache files do not expire, you'll need to build deletion routines
into your application. For example, let's say you have a blog that
allows user commenting. Whenever a new comment is submitted you'll want
to delete the cache files associated with the controller function that
serves up your comments. You'll find two delete functions described
below that help you clear data.

Not All Database Functions Work with Caching
============================================

Lastly, we need to point out that the result object that is cached is a
simplified version of the full result object. For that reason, some of
the query result functions are not available for use.

The following functions ARE NOT available when using a cached result
object:

-  num_fields()
-  field_names()
-  field_data()
-  free_result()

Also, the two database resources (result_id and conn_id) are not
available when caching, since result resources only pertain to run-time
operations.

******************
Function Reference
******************

$this->db->cache_on() / $this->db->cache_off()
================================================

Manually enables/disables caching. This can be useful if you want to
keep certain queries from being cached. Example::

	// Turn caching on
	$this->db->cache_on();
	$query = $this->db->query("SELECT * FROM mytable");
	
	// Turn caching off for this one query
	$this->db->cache_off();
	$query = $this->db->query("SELECT * FROM members WHERE member_id = '$current_user'");
	
	// Turn caching back on
	$this->db->cache_on();
	$query = $this->db->query("SELECT * FROM another_table");

$this->db->cache_delete()
==========================

Deletes the cache files associated with a particular page. This is
useful if you need to clear caching after you update your database.

The caching system saves your cache files to folders that correspond to
the URI of the page you are viewing. For example, if you are viewing a
page at example.com/index.php/blog/comments, the caching system will put
all cache files associated with it in a folder called blog+comments. To
delete those particular cache files you will use::

	$this->db->cache_delete('blog', 'comments');

If you do not use any parameters the current URI will be used when
determining what should be cleared.

$this->db->cache_delete_all()
===============================

Clears all existing cache files. Example::

	$this->db->cache_delete_all();

