<?php  $this->load->view('header');  ?>

<p><?php echo $scaff_no_data; ?></p>
<p><?php echo anchor(array($base_uri, 'add'), $scaff_create_record); ?></p>

<?php $this->load->view('footer'); 
/* End of file no_data.php */
/* Location: ./system/scaffolding/views/no_data.php */