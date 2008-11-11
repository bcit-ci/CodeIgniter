<?php  $this->load->view('header');  ?>

<table border="0" cellpadding="0" cellspacing="1" style="width:100%">
 <tr>
	<th>Edit</th>
	<th>Delete</th>
	<?php foreach($fields as $field): ?>
	<th><?php echo $field; ?></th>
	<?php endforeach; ?>
</tr>

<?php foreach($query->result() as $row): ?>
 <tr>
	<td>&nbsp;<?php echo anchor(array($base_uri, 'edit', $row->$primary), $scaff_edit); ?>&nbsp;</td>
 	<td><?php echo anchor(array($base_uri, 'delete', $row->$primary), $scaff_delete); ?></td>
 	<?php foreach($fields as $field): ?>	
	<td><?php echo form_prep($row->$field);?></td>
	<?php endforeach; ?>
 </tr>
<?php endforeach; ?>
</table>

<?php echo $paginate; ?>

<?php $this->load->view('footer'); 
/* End of file view.php */
/* Location: ./system/scaffolding/views/view.php */