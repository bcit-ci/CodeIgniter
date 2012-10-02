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