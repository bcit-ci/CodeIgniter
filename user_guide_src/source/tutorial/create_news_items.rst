#################
Create news items
#################

You now know how you can read data from a database using CodeIgniter, but
you haven't written any information to the database yet. In this section
you'll expand your news controller and model created earlier to include
this functionality.

Create a form
-------------

To input data into the database you need to create a form where you can
input the information to be stored. This means you'll be needing a form
with two fields, one for the title and one for the text. You'll derive
the slug from our title in the model. Create the new view at
*application/views/news/create.php*.

::

    <h2><?php echo $title; ?></h2>

    <?php echo validation_errors(); ?>

    <?php echo form_open('news/create'); ?>

        <label for="title">Title</label> 
        <input type="text" name="title" /><br />

        <label for="text">Text</label>
        <textarea name="text"></textarea><br />

        <input type="submit" name="submit" value="Create news item" /> 

    </form>

There are only two things here that probably look unfamiliar to you: the
``form_open()`` function and the ``validation_errors()`` function.

The first function is provided by the :doc:`form
helper <../helpers/form_helper>` and renders the form element and
adds extra functionality, like adding a hidden :doc:`CSRF prevention
field <../libraries/security>`. The latter is used to report
errors related to form validation.

Go back to your news controller. You're going to do two things here,
check whether the form was submitted and whether the submitted data
passed the validation rules. You'll use the :doc:`form
validation <../libraries/form_validation>` library to do this.

::

    public function create()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');
        
        $data['title'] = 'Create a news item';
        
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('text', 'Text', 'required');
        
        if ($this->form_validation->run() === FALSE)
        {
            $this->load->view('templates/header', $data);   
            $this->load->view('news/create');
            $this->load->view('templates/footer');
            
        }
        else
        {
            $this->news_model->set_news();
            $this->load->view('news/success');
        }
    }

The code above adds a lot of functionality. The first few lines load the
form helper and the form validation library. After that, rules for the
form validation are set. The ``set_rules()`` method takes three arguments;
the name of the input field, the name to be used in error messages, and
the rule. In this case the title and text fields are required.

CodeIgniter has a powerful form validation library as demonstrated
above. You can read :doc:`more about this library
here <../libraries/form_validation>`.

Continuing down, you can see a condition that checks whether the form
validation ran successfully. If it did not, the form is displayed, if it
was submitted **and** passed all the rules, the model is called. After
this, a view is loaded to display a success message. Create a view at
*application/views/news/success.php* and write a success message.

Model
-----

The only thing that remains is writing a method that writes the data to
the database. You'll use the Query Builder class to insert the
information and use the input library to get the posted data. Open up
the model created earlier and add the following:

::

    public function set_news()
    {
        $this->load->helper('url');
        
        $slug = url_title($this->input->post('title'), 'dash', TRUE);
        
        $data = array(
            'title' => $this->input->post('title'),
            'slug' => $slug,
            'text' => $this->input->post('text')
        );
        
        return $this->db->insert('news', $data);
    }

This new method takes care of inserting the news item into the database.
The third line contains a new function, url\_title(). This function -
provided by the :doc:`URL helper <../helpers/url_helper>` - strips down
the string you pass it, replacing all spaces by dashes (-) and makes
sure everything is in lowercase characters. This leaves you with a nice
slug, perfect for creating URIs.

Let's continue with preparing the record that is going to be inserted
later, inside the ``$data`` array. Each element corresponds with a column in
the database table created earlier. You might notice a new method here,
namely the ``post()`` method from the :doc:`input
library <../libraries/input>`. This method makes sure the data is
sanitized, protecting you from nasty attacks from others. The input
library is loaded by default. At last, you insert our ``$data`` array into
our database.

Routing
-------

Before you can start adding news items into your CodeIgniter application
you have to add an extra rule to *config/routes.php* file. Make sure your
file contains the following. This makes sure CodeIgniter sees 'create'
as a method instead of a news item's slug.

::

    $route['news/create'] = 'news/create';
    $route['news/(:any)'] = 'news/view/$1';
    $route['news'] = 'news';
    $route['(:any)'] = 'pages/view/$1';
    $route['default_controller'] = 'pages/view';

Now point your browser to your local development environment where you
installed CodeIgniter and add index.php/news/create to the URL.
Congratulations, you just created your first CodeIgniter application!
Add some news and check out the different pages you made.
