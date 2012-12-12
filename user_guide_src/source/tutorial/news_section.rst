############
News section
############

In the last section, we went over some basic concepts of the framework
by writing a class that includes static pages. We cleaned up the URI by
adding custom routing rules. Now it's time to introduce dynamic content
and start using a database.

Setting up your model
---------------------

Instead of writing database operations right in the controller, queries
should be placed in a model, so they can easily be reused later. Models
are the place where you retrieve, insert, and update information in your
database or other data stores. They represent your data.

Open up the application/models directory and create a new file called
news_model.php and add the following code. Make sure you've configured
your database properly as described
`here <../database/configuration.html>`_.

::

	<?php
	class News_model extends CI_Model {

		public function __construct()
		{
			$this->load->database();
		}
	}

This code looks similar to the controller code that was used earlier. It
creates a new model by extending ``CI_Model`` and loads the database
library. This will make the database class available through the
``$this->db`` object.

Before querying the database, a database schema has to be created.
Connect to your database and run the SQL command below. Also add some
seed records.

::

	CREATE TABLE news (
		id int(11) NOT NULL AUTO_INCREMENT,
		title varchar(128) NOT NULL,
		slug varchar(128) NOT NULL,
		text text NOT NULL,
		PRIMARY KEY (id),
		KEY slug (slug)
	);

Now that the database and a model have been set up, you'll need a method
to get all of our posts from our database. To do this, the database
abstraction layer that is included with CodeIgniter — `Active
Record <../database/query_builder.html>`_ — is used. This makes it
possible to write your 'queries' once and make them work on `all
supported database systems <../general/requirements.html>`_. Add the
following code to your model.

::

	public function get_news($slug = FALSE)
	{
		if ($slug === FALSE)
		{
			$query = $this->db->get('news');
			return $query->result_array();
		}
        
		$query = $this->db->get_where('news', array('slug' => $slug));
		return $query->row_array();
	}

With this code you can perform two different queries. You can get all
news records, or get a news item by its `slug <#>`_. You might have
noticed that the $slug variable wasn't sanitized before running the
query; :doc:`Query Builder <../database/query_builder>` does this for you.

Display the news
----------------

Now that the queries are written, the model should be tied to the views
that are going to display the news items to the user. This could be done
in our pages controller created earlier, but for the sake of clarity, a
new "news" controller is defined. Create the new controller at
application/controllers/news.php.

::

	<?php
	class News extends CI_Controller {

		public function __construct()
		{
			parent::__construct();
			$this->load->model('news_model');
		}

		public function index()
		{
			$data['news'] = $this->news_model->get_news();
		}

		public function view($slug = NULL)
		{
			$data['news_item'] = $this->news_model->get_news($slug);
		}
	}

Looking at the code, you may see some similarity with the files we
created earlier. First, the ``__construct()`` method: it calls the
constructor of its parent class (``CI_Controller``) and loads the model,
so it can be used in all other methods in this controller.

Next, there are two methods to view all news items and one for a
specific news item. You can see that the $slug variable is passed to the
model's method in the second method. The model is using this slug to
identify the news item to be returned.

Now the data is retrieved by the controller through our model, but
nothing is displayed yet. The next thing to do is passing this data to
the views.

::

	public function index()
	{
		data['news'] = $this->news_model->get_news();
		$data['title'] = 'News archive';

		$this->load->view('templates/header', $data);
		$this->load->view('news/index', $data);
		$this->load->view('templates/footer');
	}

The code above gets all news records from the model and assigns it to a
variable. The value for the title is also assigned to the $data['title']
element and all data is passed to the views. You now need to create a
view to render the news items. Create application/views/news/index.php
and add the next piece of code.

::

	<?php foreach ($news as $news_item): ?>

		<h2><?php echo $news_item['title'] ?></h2>
		<div id="main">
			<?php echo $news_item['text'] ?>
		</div>
		<p><a href="<?php echo $news_item['slug'] ?>">View article</a></p>

	<?php endforeach ?>

Here, each news item is looped and displayed to the user. You can see we
wrote our template in PHP mixed with HTML. If you prefer to use a
template language, you can use CodeIgniter's `Template
Parser <../libraries/parser>`_ class or a third party parser.

The news overview page is now done, but a page to display individual
news items is still absent. The model created earlier is made in such
way that it can easily be used for this functionality. You only need to
add some code to the controller and create a new view. Go back to the
news controller and add the following lines to the file.

::

	public function view($slug = NULL)
	{
		$data['news_item'] = $this->news_model->get_news($slug);

		if (empty($data['news_item']))
		{
			show_404();
		}

		$data['title'] = $data['news_item']['title'];

		$this->load->view('templates/header', $data);
		$this->load->view('news/view', $data);
		$this->load->view('templates/footer');
	}

Instead of calling the ``get_news()`` method without a parameter, the
``$slug`` variable is passed, so it will return the specific news item.
The only things left to do is create the corresponding view at
*application/views/news/view.php*. Put the following code in this file.

::

	<?php
	echo '<h2>'.$news_item['title'].'</h2>';
	echo $news_item['text'];

Routing
-------

Because of the wildcard routing rule created earlier, you need need an
extra route to view the controller that you just made. Modify your
routing file (application/config/routes.php) so it looks as follows.
This makes sure the requests reaches the news controller instead of
going directly to the pages controller. The first line routes URI's with
a slug to the view method in the news controller.

::

	$route['news/(:any)'] = 'news/view/$1';
	$route['news'] = 'news';
	$route['(:any)'] = 'pages/view/$1';
	$route['default_controller'] = 'pages/view';

Point your browser to your document root, followed by index.php/news and
watch your news page.