##################################
Hierarchical Model-View-Controller
##################################

CodeIgniter is based on the Hierarchical Model-View-Controller development
pattern. HMVC is a software approach that separates application logic from
presentation and supports modular subdivisions of application code.
In practice, it permits your web pages to contain minimal
scripting since the presentation is separate from the PHP scripting.

-  The **Hierarchy** is a collection of one or more Controllers, each
   potentially having its own Model(s) and/or View(s). Program flow may
   be passed from the initial Controller down to other Controllers in
   any kind of hierarchy or sequence you wish.
-  The **Model** represents your data structures. Typically your model
   classes will contain functions that help you retrieve, insert, and
   update information in your database.
-  The **View** is the information that is being presented to a user. A
   View will normally be a web page, but in CodeIgniter, a view can also
   be a page fragment like a header or footer. It can also be an RSS
   page, or any other type of "page".
-  The **Controller** serves as an *intermediary* between the Model, the
   View, and any other resources needed to process the HTTP request and
   generate a web page.

CodeIgniter has a fairly loose approach to MVC since Models are not
required. If you don't need the added separation, or find that
maintaining models requires more complexity than you want, you can
ignore them and build your application minimally using Controllers and
Views. CodeIgniter also enables you to incorporate your own existing
scripts, or even develop core libraries for the system, enabling you to
work in a way that makes the most sense to you.

The Hierarchical aspect of CodeIgniter is similarly loose. Your application
may consist of a single Controller, as is seen in plain MVC, or any
number of Controllers organized any way you see fit. Models and Views
will likely be associated with specific Controllers, but each is accessible
to all parts of your application, thanks to the central CodeIgniter object
where every resource is registered.

.. _hmvc-modules:

Modules
=======

A key feature of HMVC is support for modules, or groups of related MVC
components. If your application runs any sub-Controllers, it may be convenient
to keep their associated resources together with them in a subdirectory.
As common Models, Views, and Controllers are located in the application's
models/, views/, and controllers/ directories (with optional subdirectories
under those), another scheme is necessary to group functionally related
components together. The solution lies in the module path, which may contain
any hierarchy of module directories. Each module directory in the module path
is like a mini application folder - containing its own models/, views/, and
controllers/ directories.

The default module path is application/modules, but you may configure a
different path or add more directories to the list in
application/config/config.php::

	$config['module_path'] = array('modules');

Path directoriess may be absolute, relative to the PHP includes path, or
relative to your application directory (as is the default). Empty path
directories will not be searched, but subdirectories may be recursed with URI
segments in the request URL or with leading subdirectories when loading a
component directly. For example, if the module path is application/modules/,
then "foo/bar/baz" may map to any of these Controllers:

* application/modules/controllers/foo.php
* application/modules/foo/controllers/bar.php
* application/modules/foo/bar/controllers/baz.php
* application/modules/foo/bar/baz/controllers/[default].php

If you created a "foo" module in your module path (as in the second example
above), its directory structure might look like:

* foo/

  * controllers/

    * bar.php

  * models/

    * foobar.php

  * views/

    * foo_menu.php
    * bar.php

You could run the Controller directly from the URL like this::

	example.com/index.php/foo/bar

Or, perhaps more likely, you would call the index() function of your module
Controller from another Controller as follows::

	$this->load->controller('foo/bar');

From within your Controller (or from any other part of your application, if
you wish), you would load your module Model like so::

	$this->load->model('foo/foobar');

And then you could load your Views in the same manner::

	$this->load->view('foo/foo_menu');
	$this->load->view('foo/bar');

When loading MVC components from the module path, the search stops at the first
match found, so beware of naming conflicts. The main application directory
and any configured package paths are always searched first for Modules, Views,
and Controllers. (Remember that subdirectories apply _inside_ the component
directories here.) After that, module directories are searched in the order
specified in the module path. In the case of views, the $view_folder defined
in index.php (which becomes VIEWPATH) is always searched before even the
application directory.
