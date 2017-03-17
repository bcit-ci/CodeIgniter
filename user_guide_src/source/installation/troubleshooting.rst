###############
Troubleshooting
###############

If you find that no matter what you put in your URL only your default
page is loading, it might be that your server does not support the
REQUEST_URI variable needed to serve search-engine friendly URLs. As a
first step, open your *application/config/config.php* file and look for
the URI Protocol information. It will recommend that you try a couple
alternate settings. If it still doesn't work after you've tried this
you'll need to force CodeIgniter to add a question mark to your URLs. To
do this open your *application/config/config.php* file and change this::

	$config['index_page'] = "index.php";

To this::

	$config['index_page'] = "index.php?";
