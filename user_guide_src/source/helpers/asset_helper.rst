##########
Asset Helper
##########

The URL Helper file contains functions that assist in working with URLs.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

Loading this Helper
===================

This helper is loaded using the following code::

	$this->load->helper('asset');

Available Functions
===================

The following functions are available:

.. php:function:: asset_version([$uri = ''[, $protocol = NULL]])

	:param	string	$uri: Asset URI string
	:param	string	$protocol: Protocol, e.g. 'http' or 'https'
	:returns:	Site URL with version identifier
	:rtype:	string

  Returns an asset URL with the addition of a version identifier. Version
  identifiers are especially useful when assets are cached in the users
  browser.

  You are encourage to use this function whenever you want to generate an asset
  url because it enables the browser to force the refresh of assets when a
  change is made.

  Segments can be passed as strings just like the ``base_url()`` helper. Here is
  an example::

    echo asset_version('assets/css/bootstrap.css');

  The above example would return something like this:
  *http://mysite.com/assets/css/bootstrap.css?v=1*
