################
Directory Helper
################

The Directory Helper file contains functions that assist in working with
directories.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

Loading this Helper
===================

This helper is loaded using the following code:

::

	$this->load->helper('directory');

Available Functions
===================

The following functions are available:


.. php:function:: directory_map($source_dir[, $directory_depth = 0[, $hidden = FALSE]])

	:param	string	$source_dir: Path to the source directory
	:param	int	$directory_depth: Depth of directories to traverse (0 = fully recursive, 1 = current dir, etc)
	:param	bool	$hidden: Whether to include hidden directories
	:returns:	An array of files
	:rtype:	array

	Examples::

		$map = directory_map('./mydirectory/');

	.. note:: Paths are almost always relative to your main index.php file.


	Sub-folders contained within the directory will be mapped as well. If
	you wish to control the recursion depth, you can do so using the second
	parameter (integer). A depth of 1 will only map the top level directory::

		$map = directory_map('./mydirectory/', 1);

	By default, hidden files will not be included in the returned array. To
	override this behavior, you may set a third parameter to true (boolean)::

		$map = directory_map('./mydirectory/', FALSE, TRUE);

	Each folder name will be an array index, while its contained files will
	be numerically indexed. Here is an example of a typical array::

		Array (
			[libraries] => Array
				(
					[0] => benchmark.html
					[1] => config.html
					["database/"] => Array
						(
							[0] => query_builder.html
							[1] => binds.html
							[2] => configuration.html
							[3] => connecting.html
							[4] => examples.html
							[5] => fields.html
							[6] => index.html
							[7] => queries.html
						)
					[2] => email.html
					[3] => file_uploading.html
					[4] => image_lib.html
					[5] => input.html
					[6] => language.html
					[7] => loader.html
					[8] => pagination.html
					[9] => uri.html
				)