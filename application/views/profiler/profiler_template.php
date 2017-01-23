
<div id="codeigniter_profiler" style="clear:both;background-color:#fff;padding:10px;">

<?php if ($fields_displayed > 0): ?>
	<fieldset id="ci_profiler_benchmarks" style="border:1px solid #900;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">
	<legend style="color:#900;">
		&nbsp;&nbsp;<?php echo $this->CI->lang->line('profiler_benchmarks'); ?>&nbsp;&nbsp;
	</legend>

	<table style="width:100%;">
	<?php	foreach ($data['benchmarks'] as $key => $val): ?>
		<?php $key = ucwords(str_replace(array('_', '-'), ' ', $key)) ?>
		<tr>
			<td style="padding:5px;width:50%;color:#000;font-weight:bold;background-color:#ddd;">
				<?php echo $key; ?>&nbsp;&nbsp;
			</td>
			<td style="padding:5px;width:50%;color:#900;font-weight:normal;background-color:#ddd;">
				<?php echo $val; ?>
			</td>
		</tr>
	<?php	endforeach ?>
	</table>
	</fieldset>

	<fieldset id="ci_profiler_get" style="border:1px solid #cd6e00;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">
	<legend style="color:#cd6e00;">
		&nbsp;&nbsp;<?php echo $this->CI->lang->line('profiler_get_data'); ?>&nbsp;&nbsp;
	</legend>
	<?php if (count($data['get']) === 0): ?> 
	<div style="color:#cd6e00;font-weight:normal;padding:4px 0 4px 0;">
		<?php echo $this->CI->lang->line('profiler_no_get'); ?>
	</div>
	<?php else: ?>
		<?php foreach ($data['get'] as $key => $val): ?>
			<?php is_int($key) OR $key = "'".htmlspecialchars($key, ENT_QUOTES, config_item('charset'))."'"; 
					$val = (is_array($val) OR is_object($val))
					? '<pre>'.htmlspecialchars(print_r($val, TRUE), ENT_QUOTES, config_item('charset'))
					: htmlspecialchars($val, ENT_QUOTES, config_item('charset'));
			?>
		<tr>
			<td style="width:50%;color:#000;background-color:#ddd;padding:5px;">
				&#36;_GET['<?php echo $key; ?>']&nbsp;&nbsp; 
			</td>
			<td style="width:50%;padding:5px;color:#cd6e00;font-weight:normal;background-color:#ddd;">
				<?php echo $val; ?>
			</td>
		</tr>
		<?php endforeach ?>
	<?php endif ?>
	</fieldset>

	<fieldset id="ci_profiler_memory_usage" style="border:1px solid #5a0099;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">
	<legend style="color:#5a0099;">
		&nbsp;&nbsp;<?php echo $this->CI->lang->line('profiler_memory_usage'); ?>&nbsp;&nbsp;
	</legend>
	<div style="color:#5a0099;font-weight:normal;padding:4px 0 4px 0;">
	<?php if ($data['memory_usage'] > 0): ?>
		<?php echo $data['memory_usage']; ?>
	<?php else: ?>
		<?php echo $this->CI->lang->line('profiler_no_memory'); ?>
	<?php endif ?>
	</div>
	</fieldset>

	<fieldset id="ci_profiler_post" style="border:1px solid #009900;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">
	<legend style="color:#009900;">
		&nbsp;&nbsp;<?php echo $this->CI->lang->line('profiler_post_data'); ?>&nbsp;&nbsp;
	</legend>
	<?php if (count($data['post']) === 0 && count($data['files']) === 0): ?> 
	<div style="color:#009900;font-weight:normal;padding:4px 0 4px 0;">
		<?php echo $this->CI->lang->line('profiler_no_get'); ?>
	</div>
	<?php else: ?>
		<?php foreach ($data['post'] as $key => $val): ?>
			<?php is_int($key) OR $key = "'".htmlspecialchars($key, ENT_QUOTES, config_item('charset'))."'";
					$val = (is_array($val) OR is_object($val))
					? '<pre>'.htmlspecialchars(print_r($val, TRUE), ENT_QUOTES, config_item('charset'))
					: htmlspecialchars($val, ENT_QUOTES, config_item('charset'));
			?>
		<tr>
			<td style="width:50%;padding:5px;color:#000;background-color:#ddd;">
				&#36;_POST['<?php echo $key; ?>']&nbsp;&nbsp; 
			</td>
			<td style="width:50%;padding:5px;color:#009900;font-weight:normal;background-color:#ddd;">
				<?php echo $val; ?>
			</td>
		</tr>
		<?php endforeach ?>
		<?php foreach ($data['files'] as $key => $val): ?>
			<?php is_int($key) OR $key = "'".htmlspecialchars($key, ENT_QUOTES, config_item('charset'))."'";
					$val = (is_array($val) OR is_object($val))
					? '<pre>'.htmlspecialchars(print_r($val, TRUE), ENT_QUOTES, config_item('charset'))
					: htmlspecialchars($val, ENT_QUOTES, config_item('charset'));
			?>
		<tr>
			<td style="width:50%;padding:5px;color:#000;background-color:#ddd;">
				&#36;_FILES['<?php echo $key; ?>']&nbsp;&nbsp; 
			</td>
			<td style="width:50%;padding:5px;color:#009900;font-weight:normal;background-color:#ddd;">
				<?php echo $val; ?>
			</td>
		</tr>
		<?php endforeach ?>
	<?php endif ?>
	</fieldset>

	<fieldset id="ci_profiler_uri_string" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">
	<legend style="color:#000;">
		&nbsp;&nbsp;<?php echo $this->CI->lang->line('profiler_uri_string'); ?>&nbsp;&nbsp;
	</legend>
	<div style="color:#000;font-weight:normal;padding:4px 0 4px 0;">
	<?php if ($this->CI->uri->uri_string === ''): ?>
		<?php echo $this->CI->lang->line('profiler_no_uri'); ?>
	<?php else: ?>
		<?php echo $this->CI->uri->uri_string; ?>
	<?php endif ?>
	</div>
	</fieldset>

	<fieldset id="ci_profiler_controller_info" style="border:1px solid #995300;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">
	<legend style="color:#995300;">
		&nbsp;&nbsp;<?php echo $this->CI->lang->line('profiler_controller_info'); ?>&nbsp;&nbsp;
	</legend>
	<div style="color:#995300;font-weight:normal;padding:4px 0 4px 0;">
		<?php echo $data['controller_info'] ?>
	</div>
	</fieldset>

	<?php if (count($data['queries']) === 0): ?>
	<fieldset id="ci_profiler_queries" style="border:1px solid #0000FF;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">
	<legend style="color:#0000FF;">
		&nbsp;&nbsp;<?php echo $this->CI->lang->line('profiler_queries'); ?>&nbsp;&nbsp;
	</legend>
	<table style="border:none; width:100%;">
	<tr>
		<td style="width:100%;color:#0000FF;font-weight:normal;background-color:#eee;padding:5px;">
			<?php echo $this->CI->lang->line('profiler_no_db'); ?>
		</td>
	</tr>
	</table>
	</fieldset>
	<?php else: ?>
		<?php foreach ($data['queries'] as $name => $row): ?>
		<fieldset style="border:1px solid #0000FF;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">
			<legend style="color:#0000FF;">&nbsp;&nbsp;
				<?php echo $this->CI->lang->line('profiler_database'); ?>:&nbsp; 
				<?php echo $row['db']->database; ?> (<?php echo $name; ?>)&nbsp;&nbsp;&nbsp;
				<?php echo $this->CI->lang->line('profiler_queries'); ?>: 
				<?php echo count($row['db']->queries); ?> (<?php echo $row['total_time']; ?> <?php echo $this->CI->lang->line('profiler_seconds'); ?>)&nbsp;&nbsp;
				<?php 
				$current_query_state;
				$toggled_query_state;
				if ($row['hide_queries']): 
					$current_query_state = $this->CI->lang->line('profiler_section_hide'); 
					$toggled_query_state = $this->CI->lang->line('profiler_section_show'); 
				else: 
					$current_query_state = $this->CI->lang->line('profiler_section_show'); 
					$toggled_query_state = $this->CI->lang->line('profiler_section_hide'); 
				endif
				?>
				(<span style="cursor: pointer;" onclick="var s=document.getElementById('ci_profiler_queries_db_<?php echo $row['count']; ?>').style;s.display=s.display=='none'?'':'none';this.innerHTML=this.innerHTML=='<?php echo $toggled_query_state; ?>'?'<?php echo $current_query_state; ?>':'<?php echo $toggled_query_state; ?>';"><?php echo $toggled_query_state; ?></span>)
			</legend>
			<table style="width:100%;<?php if ($row['hide_queries']): echo "display:none"; endif ?>" id="ci_profiler_queries_db_<?php echo $row['count']; ?>">
			<?php if (count($row['db']->queries) === 0): ?>
				<tr>
					<td style="width:100%;color:#0000FF;font-weight:normal;background-color:#eee;padding:5px;">
						<?php echo $this->CI->lang->line('profiler_no_queries'); ?>
					</td>
				</tr>
			<?php else: ?>
				<?php foreach ($row['queries'] as $query): ?>
				<tr>
					<td style="padding:5px;vertical-align:top;width:1%;color:#900;font-weight:normal;background-color:#ddd;">
						<?php echo $query['time']; ?>
					</td>
					<td style="padding:5px;color:#000;font-weight:normal;background-color:#ddd;">
					<?php 
						$query['val'] = highlight_code($query['val']);
						$highlight = array('SELECT', 'DISTINCT', 'FROM', 'WHERE', 'AND', 'LEFT&nbsp;JOIN', 'ORDER&nbsp;BY', 'GROUP&nbsp;BY', 'LIMIT', 'INSERT', 'INTO', 'VALUES', 'UPDATE', 'OR&nbsp;', 'HAVING', 'OFFSET', 'NOT&nbsp;IN', 'IN', 'LIKE', 'NOT&nbsp;LIKE', 'COUNT', 'MAX', 'MIN', 'ON', 'AS', 'AVG', 'SUM', '(', ')');  
						foreach ($highlight as $bold): 
							$query['val'] = str_replace($bold, '<strong>'.$bold.'</strong>', $query['val']);
						 endforeach 
					?>
						<?php echo $query['val']; ?>
					</td>
				</tr>
				<?php endforeach ?>
			<?php endif ?>
			</table>
		</fieldset>
		<?php endforeach ?>

	<?php endif ?>

	<fieldset id="ci_profiler_http_headers" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">
	<legend style="color:#000;">
		&nbsp;&nbsp;<?php echo $this->CI->lang->line('profiler_headers'); ?>&nbsp;&nbsp;
		(<span style="cursor: pointer;" onclick="var s=document.getElementById('ci_profiler_httpheaders_table').style;s.display=s.display=='none'?'':'none';this.innerHTML=this.innerHTML=='<?php echo $this->CI->lang->line('profiler_section_show'); ?>'?'<?php echo $this->CI->lang->line('profiler_section_hide'); ?>':'<?php echo $this->CI->lang->line('profiler_section_show'); ?>';">
			<?php echo $this->CI->lang->line('profiler_section_show'); ?>
		</span>)
	</legend>

	<table style="width:100%;display:none;" id="ci_profiler_httpheaders_table">
	<?php foreach ($data['http_headers'] as $key => $val): ?>
		<tr>
			<td style="vertical-align:top;width:50%;padding:5px;color:#900;background-color:#ddd;">
				<?php echo $key; ?>&nbsp;&nbsp;
			</td>
			<td style="width:50%;padding:5px;color:#000;background-color:#ddd;">
				<?php echo htmlspecialchars($val, ENT_QUOTES, config_item('charset')); ?>
			</td>
		</tr>
	<?php endforeach ?>
	</table>
	</fieldset>

	<?php if ( count($data['session_data']) > 0): ?>
	<fieldset id="ci_profiler_csession" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">
	<legend style="color:#000;">&nbsp;&nbsp;<?php echo $this->CI->lang->line('profiler_session_data'); ?>&nbsp;&nbsp;
		(<span style="cursor: pointer;" onclick="var s=document.getElementById('ci_profiler_session_data').style;s.display=s.display=='none'?'':'none';this.innerHTML=this.innerHTML=='<?php echo $this->CI->lang->line('profiler_section_show'); ?>'?'<?php echo $this->CI->lang->line('profiler_section_hide'); ?>':'<?php echo $this->CI->lang->line('profiler_section_show'); ?>';"><?php echo $this->CI->lang->line('profiler_section_show'); ?></span>)
	</legend>
	<table style="width:100%;display:none;" id="ci_profiler_session_data">
	<?php foreach ($data['session_data'] as $key => $val): ?>
		<tr>
			<td style="padding:5px;vertical-align:top;color:#900;background-color:#ddd;">
				<?php echo $key; ?>
				&nbsp;&nbsp;</td><td style="padding:5px;color:#000;background-color:#ddd;">
				<?php echo htmlspecialchars($val); ?>
			</td>
		</tr>
	<?php endforeach ?>
	</table>
	</fieldset>
	<?php endif ?>

	<fieldset id="ci_profiler_config" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">
	<legend style="color:#000;">
		&nbsp;&nbsp;<?php echo $this->CI->lang->line('profiler_config'); ?>&nbsp;&nbsp;
		(<span style="cursor: pointer;" onclick="var s=document.getElementById('ci_profiler_config_table').style;s.display=s.display=='none'?'':'none';this.innerHTML=this.innerHTML=='<?php echo $this->CI->lang->line('profiler_section_show'); ?>'?'<?php echo $this->CI->lang->line('profiler_section_hide'); ?>':'<?php echo $this->CI->lang->line('profiler_section_show'); ?>';">
			<?php echo $this->CI->lang->line('profiler_section_show'); ?>
		</span>)
	</legend>

	<table style="width:100%;display:none;" id="ci_profiler_config_table">
	<?php foreach ($data['config'] as $key => $val): ?>
		<tr>
			<td style="padding:5px;vertical-align:top;color:#900;background-color:#ddd;">
				<?php echo $key; ?>&nbsp;&nbsp;
			</td>
			<td style="padding:5px;color:#000;background-color:#ddd;">
				<?php echo $val; ?>
			</td>
		</tr>
	<?php endforeach ?>
	</table>
	</fieldset></div>

<?php else: ?>
	<p style="border:1px solid #5a0099;padding:10px;margin:20px 0;background-color:#eee;">
		<?php echo $this->CI->lang->line('profiler_no_profiles'); ?>
	</p>
<?php endif ?>

