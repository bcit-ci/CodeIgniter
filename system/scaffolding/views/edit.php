<?php $this->load->view('header');  ?>


<p><?php echo anchor(array($base_uri, 'view'), '&lt; '.$scaff_view_all);?></p>


<?php echo form_open($action); ?>

<table border="0" cellpadding="3" cellspacing="1">
<?php foreach($fields as $field): ?>

<?php if ($field->primary_key == 1) continue; ?>

<tr>
	<td><?php echo  $field->name; ?></td>
	
	<?php if ($field->type == 'blob'): ?>
	<td><textarea class="textarea" name="<?php echo $field->name;?>" cols="60" rows="10" ><?php $f = $field->name; echo form_prep($query->$f); ?></textarea></td>
	<?php else : ?>
	<td><input class="input" value="<?php $f = $field->name; echo form_prep($query->$f); ?>" name="<?php echo $field->name; ?>" size="60" /></td>
	<?php endif; ?>
	
</tr>
<?php endforeach; ?>
</table>

<input type="submit" class="submit" value="Update" />

</form>

<?php $this->load->view('footer'); 
/* End of file edit.php */
/* Location: ./system/scaffolding/views/edit.php */