###################
Shopping Cart Class
###################

The Cart Class permits items to be added to a session that stays active
while a user is browsing your site. These items can be retrieved and
displayed in a standard "shopping cart" format, allowing the user to
update the quantity or remove items from the cart.

.. important:: The Cart library is DEPRECATED and should not be used. 
	It is currently only kept for backwards compatibility.

Please note that the Cart Class ONLY provides the core "cart"
functionality. It does not provide shipping, credit card authorization,
or other processing components.

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

********************
Using the Cart Class
********************

Initializing the Shopping Cart Class
====================================

.. important:: The Cart class utilizes CodeIgniter's :doc:`Session
	Class <sessions>` to save the cart information to a database, so
	before using the Cart class you must set up a database table as
	indicated in the :doc:`Session Documentation <sessions>`, and set the
	session preferences in your application/config/config.php file to
	utilize a database.

To initialize the Shopping Cart Class in your controller constructor,
use the ``$this->load->library()`` method::

	$this->load->library('cart');

Once loaded, the Cart object will be available using::

	$this->cart

.. note:: The Cart Class will load and initialize the Session Class
	automatically, so unless you are using sessions elsewhere in your
	application, you do not need to load the Session class.

Adding an Item to The Cart
==========================

To add an item to the shopping cart, simply pass an array with the
product information to the ``$this->cart->insert()`` method, as shown
below::

	$data = array(
		'id'      => 'sku_123ABC',
		'qty'     => 1,
		'price'   => 39.95,
		'name'    => 'T-Shirt',
		'options' => array('Size' => 'L', 'Color' => 'Red')
	);

	$this->cart->insert($data);

.. important:: The first four array indexes above (id, qty, price, and
	name) are **required**. If you omit any of them the data will not be
	saved to the cart. The fifth index (options) is optional. It is intended
	to be used in cases where your product has options associated with it.
	Use an array for options, as shown above.

The five reserved indexes are:

-  **id** - Each product in your store must have a unique identifier.
   Typically this will be an "sku" or other such identifier.
-  **qty** - The quantity being purchased.
-  **price** - The price of the item.
-  **name** - The name of the item.
-  **options** - Any additional attributes that are needed to identify
   the product. These must be passed via an array.

In addition to the five indexes above, there are two reserved words:
rowid and subtotal. These are used internally by the Cart class, so
please do NOT use those words as index names when inserting data into
the cart.

Your array may contain additional data. Anything you include in your
array will be stored in the session. However, it is best to standardize
your data among all your products in order to make displaying the
information in a table easier.

::

	$data = array(
		'id'      => 'sku_123ABC',
		'qty'     => 1,
		'price'   => 39.95,
		'name'    => 'T-Shirt',
		'coupon'	 => 'XMAS-50OFF'
	);

	$this->cart->insert($data);

The ``insert()`` method will return the $rowid if you successfully insert a
single item.

Adding Multiple Items to The Cart
=================================

By using a multi-dimensional array, as shown below, it is possible to
add multiple products to the cart in one action. This is useful in cases
where you wish to allow people to select from among several items on the
same page.

::

	$data = array(
		array(
			'id'      => 'sku_123ABC',
			'qty'     => 1,
			'price'   => 39.95,
			'name'    => 'T-Shirt',
			'options' => array('Size' => 'L', 'Color' => 'Red')
		),
		array(
			'id'      => 'sku_567ZYX',
			'qty'     => 1,
			'price'   => 9.95,
			'name'    => 'Coffee Mug'
		),
		array(
			'id'      => 'sku_965QRS',
			'qty'     => 1,
			'price'   => 29.95,
			'name'    => 'Shot Glass'
		)
	);

	$this->cart->insert($data);

Displaying the Cart
===================

To display the cart you will create a :doc:`view
file </general/views>` with code similar to the one shown below.

Please note that this example uses the :doc:`form
helper </helpers/form_helper>`.

::

	<?php echo form_open('path/to/controller/update/method'); ?>

	<table cellpadding="6" cellspacing="1" style="width:100%" border="0">

	<tr>
		<th>QTY</th>
		<th>Item Description</th>
		<th style="text-align:right">Item Price</th>
		<th style="text-align:right">Sub-Total</th>
	</tr>

	<?php $i = 1; ?>

	<?php foreach ($this->cart->contents() as $items): ?>

		<?php echo form_hidden($i.'[rowid]', $items['rowid']); ?>

		<tr>
			<td><?php echo form_input(array('name' => $i.'[qty]', 'value' => $items['qty'], 'maxlength' => '3', 'size' => '5')); ?></td>
			<td>
				<?php echo $items['name']; ?>

				<?php if ($this->cart->has_options($items['rowid']) == TRUE): ?>

					<p>
						<?php foreach ($this->cart->product_options($items['rowid']) as $option_name => $option_value): ?>

							<strong><?php echo $option_name; ?>:</strong> <?php echo $option_value; ?><br />

						<?php endforeach; ?>
					</p>

				<?php endif; ?>

			</td>
			<td style="text-align:right"><?php echo $this->cart->format_number($items['price']); ?></td>
			<td style="text-align:right">$<?php echo $this->cart->format_number($items['subtotal']); ?></td>
		</tr>

	<?php $i++; ?>

	<?php endforeach; ?>

	<tr>
		<td colspan="2"> </td>
		<td class="right"><strong>Total</strong></td>
		<td class="right">$<?php echo $this->cart->format_number($this->cart->total()); ?></td>
	</tr>

	</table>

	<p><?php echo form_submit('', 'Update your Cart'); ?></p>

Updating The Cart
=================

To update the information in your cart, you must pass an array
containing the Row ID and one or more pre-defined properties to the 
``$this->cart->update()`` method.

.. note:: If the quantity is set to zero, the item will be removed from
	the cart.

::

	$data = array(
		'rowid' => 'b99ccdf16028f015540f341130b6d8ec',
		'qty'   => 3
	);

	$this->cart->update($data);

	// Or a multi-dimensional array

	$data = array(
		array(
			'rowid'   => 'b99ccdf16028f015540f341130b6d8ec',
			'qty'     => 3
		),
		array(
			'rowid'   => 'xw82g9q3r495893iajdh473990rikw23',
			'qty'     => 4
		),
		array(
			'rowid'   => 'fh4kdkkkaoe30njgoe92rkdkkobec333',
			'qty'     => 2
		)
	);

	$this->cart->update($data);

You may also update any property you have previously defined when
inserting the item such as options, price or other custom fields.

::

	$data = array(
		'rowid'  => 'b99ccdf16028f015540f341130b6d8ec',
		'qty'    => 1,
		'price'	 => 49.95,
		'coupon' => NULL
	);

	$this->cart->update($data);

What is a Row ID?
*****************

The row ID is a unique identifier that is generated by the cart code
when an item is added to the cart. The reason a unique ID is created
is so that identical products with different options can be managed
by the cart.

For example, let's say someone buys two identical t-shirts (same product
ID), but in different sizes. The product ID (and other attributes) will
be identical for both sizes because it's the same shirt. The only
difference will be the size. The cart must therefore have a means of
identifying this difference so that the two sizes of shirts can be
managed independently. It does so by creating a unique "row ID" based on
the product ID and any options associated with it.

In nearly all cases, updating the cart will be something the user does
via the "view cart" page, so as a developer, it is unlikely that you
will ever have to concern yourself with the "row ID", other than making
sure your "view cart" page contains this information in a hidden form
field, and making sure it gets passed to the ``update()`` method when
the update form is submitted. Please examine the construction of the
"view cart" page above for more information.


***************
Class Reference
***************

.. php:class:: CI_Cart

	.. attribute:: $product_id_rules = '\.a-z0-9_-'

		These are the regular expression rules that we use to validate the product
		ID - alpha-numeric, dashes, underscores, or periods by default

	.. attribute:: $product_name_rules	= '\w \-\.\:'

		These are the regular expression rules that we use to validate the product ID and product name - alpha-numeric, dashes, underscores, colons or periods by
		default

	.. attribute:: $product_name_safe = TRUE

		Whether or not to only allow safe product names. Default TRUE.


	.. php:method:: insert([$items = array()])

		:param	array	$items: Items to insert into the cart
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Insert items into the cart and save it to the session table. Returns TRUE
		on success and FALSE on failure.


	.. php:method:: update([$items = array()])

		:param	array	$items: Items to update in the cart
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		This method permits changing the properties of a given item.
		Typically it is called from the "view cart" page if a user makes changes
		to the quantity before checkout. That array must contain the rowid
		for each item.

	.. php:method:: remove($rowid)

		:param	int	$rowid: ID of the item to remove from the cart
		:returns:	TRUE on success, FALSE on failure
		:rtype:	bool

		Allows you to remove an item from the shopping cart by passing it the
		``$rowid``.

	.. php:method:: total()

		:returns:	Total amount
		:rtype:	int

		Displays the total amount in the cart.


	.. php:method:: total_items()

		:returns:	Total amount of items in the cart
		:rtype:	int

		Displays the total number of items in the cart.


	.. php:method:: contents([$newest_first = FALSE])

		:param	bool	$newest_first: Whether to order the array with newest items first
		:returns:	An array of cart contents
		:rtype:	array

		Returns an array containing everything in the cart. You can sort the
		order by which the array is returned by passing it TRUE where the contents
		will be sorted from newest to oldest, otherwise it is sorted from oldest
		to newest.

	.. php:method:: get_item($row_id)

		:param	int	$row_id: Row ID to retrieve
		:returns:	Array of item data
		:rtype:	array

		Returns an array containing data for the item matching the specified row
		ID, or FALSE if no such item exists.

	.. php:method:: has_options($row_id = '')

		:param	int	$row_id: Row ID to inspect
		:returns:	TRUE if options exist, FALSE otherwise
		:rtype:	bool

		Returns TRUE (boolean) if a particular row in the cart contains options.
		This method is designed to be used in a loop with ``contents()``, since
		you must pass the rowid to this method, as shown in the Displaying
		the Cart example above.

	.. php:method:: product_options([$row_id = ''])

		:param	int	$row_id: Row ID
		:returns:	Array of product options
		:rtype:	array

		Returns an array of options for a particular product. This method is
		designed to be used in a loop with ``contents()``, since you
		must pass the rowid to this method, as shown in the Displaying the
		Cart example above.

	.. php:method:: destroy()

		:rtype: void

		Permits you to destroy the cart. This method will likely be called
		when you are finished processing the customer's order.