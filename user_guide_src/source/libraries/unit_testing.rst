##################
Unit Testing Class
##################

Unit testing is an approach to software development in which tests are
written for each function in your application. If you are not familiar
with the concept you might do a little googling on the subject.

CodeIgniter's Unit Test class is quite simple, consisting of an
evaluation function and two result functions. It's not intended to be a
full-blown test suite but rather a simple mechanism to evaluate your
code to determine if it is producing the correct data type and result.

Initializing the Class
======================

Like most other classes in CodeIgniter, the Unit Test class is
initialized in your controller using the $this->load->library function::

	$this->load->library('unit_test');

Once loaded, the Unit Test object will be available using: $this->unit

Running Tests
=============

Running a test involves supplying a test and an expected result to the
following function:

$this->unit->run( test, expected result, 'test name', 'notes');
===============================================================

Where test is the result of the code you wish to test, expected result
is the data type you expect, test name is an optional name you can give
your test, and notes are optional notes. Example::

	$test = 1 + 1;

	$expected_result = 2;

	$test_name = 'Adds one plus one';

	$this->unit->run($test, $expected_result, $test_name);

The expected result you supply can either be a literal match, or a data
type match. Here's an example of a literal::

	$this->unit->run('Foo', 'Foo');

Here is an example of a data type match::

	$this->unit->run('Foo', 'is_string');

Notice the use of "is_string" in the second parameter? This tells the
function to evaluate whether your test is producing a string as the
result. Here is a list of allowed comparison types:

-  is_object
-  is_string
-  is_bool
-  is_true
-  is_false
-  is_int
-  is_numeric
-  is_float
-  is_double
-  is_array
-  is_null

Generating Reports
==================

You can either display results after each test, or your can run several
tests and generate a report at the end. To show a report directly simply
echo or return the run function::

	echo $this->unit->run($test, $expected_result);

To run a full report of all tests, use this::

	echo $this->unit->report();

The report will be formatted in an HTML table for viewing. If you prefer
the raw data you can retrieve an array using::

	echo $this->unit->result();

Strict Mode
===========

By default the unit test class evaluates literal matches loosely.
Consider this example::

	$this->unit->run(1, TRUE);

The test is evaluating an integer, but the expected result is a boolean.
PHP, however, due to it's loose data-typing will evaluate the above code
as TRUE using a normal equality test::

	if (1 == TRUE) echo 'This evaluates as true';

If you prefer, you can put the unit test class in to strict mode, which
will compare the data type as well as the value::

	if (1 === TRUE) echo 'This evaluates as FALSE';

To enable strict mode use this::

	$this->unit->use_strict(TRUE);

Enabling/Disabling Unit Testing
===============================

If you would like to leave some testing in place in your scripts, but
not have it run unless you need it, you can disable unit testing using::

	$this->unit->active(FALSE)

Unit Test Display
=================

When your unit test results display, the following items show by
default:

-  Test Name (test_name)
-  Test Datatype (test_datatype)
-  Expected Datatype (res_datatype)
-  Result (result)
-  File Name (file)
-  Line Number (line)
-  Any notes you entered for the test (notes)

You can customize which of these items get displayed by using
$this->unit->set_test_items(). For example, if you only wanted the test name
and the result displayed:

Customizing displayed tests
---------------------------

::

	$this->unit->set_test_items(array('test_name', 'result'));

Creating a Template
-------------------

If you would like your test results formatted differently then the
default you can set your own template. Here is an example of a simple
template. Note the required pseudo-variables::

	$str = '
	<table border="0" cellpadding="4" cellspacing="1">
	    {rows}
	        <tr>
	        <td>{item}</td>
	        <td>{result}</td>
	        </tr>
	    {/rows}
	</table>';

	$this->unit->set_template($str);

.. note:: Your template must be declared **before** running the unit
	test process.
