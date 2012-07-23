##################
Zip Encoding Class
##################

CodeIgniter's Zip Encoding Class classes permit you to create Zip
archives. Archives can be downloaded to your desktop or saved to a
directory.

Initializing the Class
======================

Like most other classes in CodeIgniter, the Zip class is initialized in
your controller using the $this->load->library function::

	$this->load->library('zip');

Once loaded, the Zip library object will be available using: $this->zip

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

******************
Function Reference
******************

$this->zip->add_data()
=======================

Permits you to add data to the Zip archive. The first parameter must
contain the name you would like given to the file, the second parameter
must contain the file data as a string::

	$name = 'my_bio.txt';
	$data = 'I was born in an elevator...';

	$this->zip->add_data($name, $data);

You are allowed multiple calls to this function in order to add several
files to your archive. Example::

	$name = 'mydata1.txt';
	$data = 'A Data String!';
	$this->zip->add_data($name, $data);

	$name = 'mydata2.txt';
	$data = 'Another Data String!';
	$this->zip->add_data($name, $data);

Or you can pass multiple files using an array::

	$data = array(
	                'mydata1.txt' => 'A Data String!',
	                'mydata2.txt' => 'Another Data String!'
	            );

	$this->zip->add_data($data);

	$this->zip->download('my_backup.zip');

If you would like your compressed data organized into sub-folders,
include the path as part of the filename::

	$name = 'personal/my_bio.txt';
	$data = 'I was born in an elevator...';

	$this->zip->add_data($name, $data);

The above example will place my_bio.txt inside a folder called
personal.

$this->zip->add_dir()
======================

Permits you to add a directory. Usually this function is unnecessary
since you can place your data into folders when using
$this->zip->add_data(), but if you would like to create an empty folder
you can do so. Example::

	$this->zip->add_dir('myfolder'); // Creates a folder called "myfolder"

$this->zip->read_file()
========================

Permits you to compress a file that already exists somewhere on your
server. Supply a file path and the zip class will read it and add it to
the archive::

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

In the above example, photo.jpg will be placed inside two folders:
path/to/

$this->zip->read_dir()
=======================

Permits you to compress a folder (and its contents) that already exists
somewhere on your server. Supply a file path to the directory and the
zip class will recursively read it and recreate it as a Zip archive. All
files contained within the supplied path will be encoded, as will any
sub-folders contained within it. Example::

	$path = '/path/to/your/directory/';

	$this->zip->read_dir($path); 

	// Download the file to your desktop. Name it "my_backup.zip"
	$this->zip->download('my_backup.zip');

By default the Zip archive will place all directories listed in the
first parameter inside the zip. If you want the tree preceding the
target folder to be ignored you can pass FALSE (boolean) in the second
parameter. Example::

	$path = '/path/to/your/directory/';

	$this->zip->read_dir($path, FALSE);

This will create a ZIP with the folder "directory" inside, then all
sub-folders stored correctly inside that, but will not include the
folders /path/to/your.

$this->zip->archive()
=====================

Writes the Zip-encoded file to a directory on your server. Submit a
valid server path ending in the file name. Make sure the directory is
writable (666 or 777 is usually OK). Example::

	$this->zip->archive('/path/to/folder/myarchive.zip'); // Creates a file named myarchive.zip

$this->zip->download()
======================

Causes the Zip file to be downloaded from your server. The function must
be passed the name you would like the zip file called. Example::

	$this->zip->download('latest_stuff.zip'); // File will be named "latest_stuff.zip"

.. note:: Do not display any data in the controller in which you call
	this function since it sends various server headers that cause the
	download to happen and the file to be treated as binary.

$this->zip->get_zip()
======================

Returns the Zip-compressed file data. Generally you will not need this
function unless you want to do something unique with the data. Example::

	$name = 'my_bio.txt';
	$data = 'I was born in an elevator...';

	$this->zip->add_data($name, $data);

	$zip_file = $this->zip->get_zip();

$this->zip->clear_data()
=========================

The Zip class caches your zip data so that it doesn't need to recompile
the Zip archive for each function you use above. If, however, you need
to create multiple Zips, each with different data, you can clear the
cache between calls. Example::

	$name = 'my_bio.txt';
	$data = 'I was born in an elevator...';

	$this->zip->add_data($name, $data);
	$zip_file = $this->zip->get_zip();

	$this->zip->clear_data(); 

	$name = 'photo.jpg';
	$this->zip->read_file("/path/to/photo.jpg"); // Read the file's contents


	$this->zip->download('myphotos.zip');

