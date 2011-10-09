CodeIgniter 2 Tutorial
======================

Our header doesn't do anything exciting. It contains the basic HTML code
that we will want to display before loading the main view. You can also
see that we echo the $title variable, which we didn't define. We will
set this variable in the Pages controller a bit later. Let's go ahead
and create a footer at application/views/templates/footer.php that
includes the following code.

**© 2011**

Adding logic to the controller
------------------------------

Now we've set up the basics so we can finally do some real programming.
Earlier we set up our controller with a view method. Because we don't
want to write a separate method for every page, we made the view method
accept one parameter, the name of the page. These hard coded pages will
be located in application/views/pages/. Create two files in this
directory named home.php and about.php and put in some HTML content.

In order to load these pages we'll have to check whether these page
actually exists. When the page does exist, we load the view for that
pages, including the header and footer and display it to the user. If it
doesn't, we show a "404 Page not found" error.

public function view($page = 'home') { if ( !
file\_exists('application/views/pages/' . $page . EXT)) { show\_404(); }
$data['title'] = ucfirst($page); $this->load->view('templates/header',
$data); $this->load->view('pages/'.$page);
$this->load->view('templates/footer'); }

The first thing we do is checking whether the page we're looking for
does actually exist. We use PHP's native file\_exists() to do this check
and pass the path where the file is supposed to be. Next is the function
show\_404(), a CodeIgniter function that renders the default error page
and sets the appropriate HTTP headers.

In the header template you saw we were using the $title variable to
customize our page title. This is where we define the title, but instead
of assigning the value to a variable, we assign it to the title element
in the $data array. The last thing we need to do is loading the views in
the order we want them to be displayed. We also pass the $data array to
the header view to make its elements available in the header view file.

Routing
-------

Actually, our controller is already functioning. Point your browser to
index.php/pages/view to see your homepage. When you visit
index.php/pages/view/about you will see the about page, again including
your header and footer. Now we're going to get rid of the pages/view
part in our URI. As you may have seen, CodeIgniter does its routing by
the class, method and parameter, separated by slashes.

Open the routing file located at application/config/routes.php and add
the following two lines. Remove all other code that sets any element in
the $route array.

$route['(:any)'] = 'pages/view/$1'; $route['default\_controller'] =
'pages/view';

CodeIgniter reads its routing rules from top to bottom and routes the
request to the first matching rule. These routes are stored in the
$route array where the keys represent the incoming request and the value
the path to the method, as described above.

The first rule in our $routes array matches every request - using the
wildcard operator (:any) - and passes the value to the view method of
the pages class we created earlier. The default controller route makes
sure every request to the root goes to the view method as well, which
has the first parameter set to 'home' by default.
