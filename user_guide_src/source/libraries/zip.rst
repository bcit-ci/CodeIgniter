##################
Zip Encoding Class
##################

CodeIgniter's Zip Encoding Class permits you to create Zip archives.
Archives can be downloaded to your desktop or saved to a directory.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

****************************
Using the Zip Encoding Class
****************************

Initializing the Class
======================

Like most other classes in CodeIgniter, the Zip class is initialized in
your controller using the $this->load->library function::

	$this->load->library('zip');

Once loaded, the Zip library object will be available using::

	$this->zip

Usage Example
=============

This example demonstrates how to compress a file, save it to a folder on
your server, and download it to your desktop.

::

	$name = 'mydata1.txt';
	$data = 'A Data String!';

	$this->zip->add_data($name, $data);

	// Write the zip file to a folder on your server. Name it "my_backup.zip"
	$this->zip->archive('/path/to/directory/my_backup.zip');

	// Download the file to your desktop. Name it "my_backup.zip"
	$this->zip->download('my_backup.zip');

***************
Class Reference
***************

.. php:class:: CI_Zip

	.. attribute:: $compression_level = 2

		The compression level to use.

		It can range from 0 to 9, with 9 being the highest and 0 effectively disabling compression::

			$this->zip->compression_level = 0;

	.. php:method:: add_data($filepath[, $data = NULL])

		:param	mixed	$filepath: A single file path or an array of file => data pairs
		:param	array	$data: File contents (ignored if $filepath is an array)
		:rtype:	void

		Adds data to the Zip archive. Can work both in single and multiple files mode.

		When adding a single file, the first parameter must contain the name you would
		like given to the file and the second must contain the file contents::

			$name = 'mydata1.txt';
			$data = 'A Data String!';
			$this->zip->add_data($name, $data);

			$name = 'mydata2.txt';
			$data = 'Another Data String!';
			$this->zip->add_data($name, $data);

		When adding multiple files, the first parameter must contain *file => contents* pairs
		and the second parameter is ignored::

			$data = array(
				'mydata1.txt' => 'A Data String!',
				'mydata2.txt' => 'Another Data String!'
			);

			$this->zip->add_data($data);

		If you would like your compressed data organized into sub-directories, simply include
		the path as part of the filename(s)::

			$name = 'personal/my_bio.txt';
			$data = 'I was born in an elevator...';

			$this->zip->add_data($name, $data);

		The above example will place my_bio.txt inside a folder called personal.

	.. php:method:: add_dir($directory)

		:param	mixed	$directory: Directory name string or an array of multiple directories
		:rtype:	void

		Permits you to add a directory. Usually this method is unnecessary since you can place
		your data into directories when using ``$this->zip->add_data()``, but if you would like
		to create an empty directory you can do so::

			$this->zip->add_dir('myfolder'); // Creates a directory called "myfolder"

	.. php:method:: read_file($path[, $archive_filepath = FALSE])

		:param	string	$path: Path to file
		:param	mixed	$archive_filepath: New file name/path (string) or (boolean) whether to maintain the original filepath
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Permits you to compress a file that already exists somewhere on your server.
		Supply a file path and the zip class will read it and add it to the archive::

			$path = '/path/to/photo.jpg';

			$this->zip->read_file($path);

			// Download the file to your desktop. Name it "my_backup.zip"
			$this->zip->download('my_backup.zip');

		If you would like the Zip archive to maintain the directory structure of
		the file in it, pass TRUE (boolean) in the second parameter. Example::

			$path = '/path/to/photo.jpg';

			$this->zip->read_file($path, TRUE);

			// Download the file to your desktop. Name it "my_backup.zip"
			$this->zip->download('my_backup.zip');

		In the above example, photo.jpg will be placed into the *path/to/* directory.

		You can also specify a new name (path included) for the added file on the fly::

			$path = '/path/to/photo.jpg';
			$new_path = '/new/path/some_photo.jpg';

			$this->zip->read_file($path, $new_path);

			// Download ZIP archive containing /new/path/some_photo.jpg
			$this->zip->download('my_archive.zip');

	.. php:method:: read_dir($path[, $preserve_filepath = TRUE[, $root_path = NULL]])

		:param	string	$path: Path to directory
		:param	bool	$preserve_filepath: Whether to maintain the original path
		:param	string	$root_path: Part of the path to exclude from the archive directory
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Permits you to compress a directory (and its contents) that already exists somewhere on your server.
		Supply a path to the directory and the zip class will recursively read and recreate it as a Zip archive.
		All files contained within the supplied path will be encoded, as will any sub-directories contained within it. Example::

			$path = '/path/to/your/directory/';

			$this->zip->read_dir($path);

			// Download the file to your desktop. Name it "my_backup.zip"
			$this->zip->download('my_backup.zip');

		By default the Zip archive will place all directories listed in the first parameter
		inside the zip. If you want the tree preceding the target directory to be ignored,
		you can pass FALSE (boolean) in the second parameter. Example::

			$path = '/path/to/your/directory/';

			$this->zip->read_dir($path, FALSE);

		This will create a ZIP with a directory named "directory" inside, then all sub-directories
		stored correctly inside that, but will not include the */path/to/your* part of the path.

	.. php:method:: archive($filepath)

		:param	string	$filepath: Path to target zip archive
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Writes the Zip-encoded file to a directory on your server. Submit a valid server path
		ending in the file name. Make sure the directory is writable (755 is usually OK).
		Example::

			$this->zip->archive('/path/to/folder/myarchive.zip'); // Creates a file named myarchive.zip

	.. php:method:: download($filename = 'backup.zip')

		:param	string	$filename: Archive file name
		:rtype:	void

		Causes the Zip file to be downloaded from your server.
		You must pass the name you would like the zip file called. Example::

			$this->zip->download('latest_stuff.zip'); // File will be named "latest_stuff.zip"

		.. note:: Do not display any data in the controller in which you call
			this method since it sends various server headers that cause the
			download to happen and the file to be treated as binary.

	.. php:method:: get_zip()

		:returns:	Zip file content
		:rtype:	string

		Returns the Zip-compressed file data. Generally you will not need this method unless you
		want to do something unique with the data. Example::

			$name = 'my_bio.txt';
			$data = 'I was born in an elevator...';

			$this->zip->add_data($name, $data);

			$zip_file = $this->zip->get_zip();

	.. php:method:: clear_data()

		:rtype:	void

		The Zip class caches your zip data so that it doesn't need to recompile the Zip archive
		for each method you use above. If, however, you need to create multiple Zip archives,
		each with different data, you can clear the cache between calls. Example::

			$name = 'my_bio.txt';
			$data = 'I was born in an elevator...';

			$this->zip->add_data($name, $data);
			$zip_file = $this->zip->get_zip();

			$this->zip->clear_data();

			$name = 'photo.jpg';
			$this->zip->read_file("/path/to/photo.jpg"); // Read the file's contents

			$this->zip->download('myphotos.zip');
