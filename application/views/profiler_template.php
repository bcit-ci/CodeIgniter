<?php

get_instance()->load->helper('language');

$html = '<div id="codeigniter_profiler" style="clear:both;background-color:#fff;padding:10px;">';

if (count($sections) === 0) {

	$html .= '<p style="border:1px solid #5a0099;padding:10px;margin:20px 0;background-color:#eee;">'.lang('profiler_no_profiles').'</p>';

}

if (isset($sections['benchmarks'])) {

	$html .= "\n\n"
		.'<fieldset id="ci_profiler_benchmarks" style="border:1px solid #900;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
		."\n"
		.'<legend style="color:#900;">&nbsp;&nbsp;'.lang('profiler_benchmarks')."&nbsp;&nbsp;</legend>"
		."\n\n\n<table style=\"width:100%;\">\n";
	foreach ($sections['benchmarks'] as $key => $val) {
		$html .= '<tr><td style="padding:5px;width:50%;color:#000;font-weight:bold;background-color:#ddd;">'
			.$key.'&nbsp;&nbsp;</td><td style="padding:5px;width:50%;color:#900;font-weight:normal;background-color:#ddd;">'
			.$val."</td></tr>\n";
	}
	$html .= "</table>\n</fieldset>";

}

if (isset($sections['get'])) {

	$html .= "\n\n"
		.'<fieldset id="ci_profiler_get" style="border:1px solid #cd6e00;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
		."\n"
		.'<legend style="color:#cd6e00;">&nbsp;&nbsp;'.lang('profiler_get_data')."&nbsp;&nbsp;</legend>\n";
	if (count($sections['get']) === 0) {
		$html .= '<div style="color:#cd6e00;font-weight:normal;padding:4px 0 4px 0;">'.lang('profiler_no_get').'</div>';
	} else {
		$html .= "\n\n<table style=\"width:100%;border:none;\">\n";
		foreach ($sections['get'] as $key => $val) {
			$html .= '<tr><td style="width:50%;color:#000;background-color:#ddd;padding:5px;">&#36;_GET['
				.$key.']&nbsp;&nbsp; </td><td style="width:50%;padding:5px;color:#cd6e00;font-weight:normal;background-color:#ddd;">'
				.$val."</td></tr>\n";
		}
		$html .= "</table>\n";
	}
	$html .= '</fieldset>';

}

if (isset($sections['memory_usage'])) {

	$html .= "\n\n"
		.'<fieldset id="ci_profiler_memory_usage" style="border:1px solid #5a0099;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
		."\n"
		.'<legend style="color:#5a0099;">&nbsp;&nbsp;'.lang('profiler_memory_usage')."&nbsp;&nbsp;</legend>\n"
		.'<div style="color:#5a0099;font-weight:normal;padding:4px 0 4px 0;">'
		.($sections['memory_usage'] != '' ? number_format($sections['memory_usage']).' bytes' : lang('profiler_no_memory'))
		.'</div></fieldset>';

}

if (isset($sections['post'])) {

	$html .= "\n\n"
		.'<fieldset id="ci_profiler_post" style="border:1px solid #009900;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
		."\n"
		.'<legend style="color:#009900;">&nbsp;&nbsp;'.lang('profiler_post_data')."&nbsp;&nbsp;</legend>\n";
	if (count($sections['post']) === 0) {
		$html .= '<div style="color:#009900;font-weight:normal;padding:4px 0 4px 0;">'.lang('profiler_no_post').'</div>';
	} else {
		$html .= "\n\n<table style=\"width:100%;\">\n";
		if (isset($sections['post']['vars'])) {
			foreach ($sections['post']['vars'] as $key => $val) {
				$html .= '<tr><td style="width:50%;padding:5px;color:#000;background-color:#ddd;">&#36;_POST['
					.$key.']&nbsp;&nbsp; </td><td style="width:50%;padding:5px;color:#009900;font-weight:normal;background-color:#ddd;">'
					.$val."</td></tr>\n";
			}
		}
		if (isset($sections['post']['files'])) {
			foreach ($sections['post']['files'] as $key => $val) {
				$html .= '<tr><td style="width:50%;padding:5px;color:#000;background-color:#ddd;">&#36;_FILES['
					.$key.']&nbsp;&nbsp; </td><td style="width:50%;padding:5px;color:#009900;font-weight:normal;background-color:#ddd;">'
					.$val."</td></tr>\n";
			}
		}
		$html .= "</table>\n";
	}
	$html .= '</fieldset>';

}

if (isset($sections['uri_string'])) {

	$html .= "\n\n"
		.'<fieldset id="ci_profiler_uri_string" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
		."\n"
		.'<legend style="color:#000;">&nbsp;&nbsp;'.lang('profiler_uri_string')."&nbsp;&nbsp;</legend>\n"
		.'<div style="color:#000;font-weight:normal;padding:4px 0 4px 0;">'
		.($sections['uri_string'] === '' ? lang('profiler_no_uri') : $sections['uri_string'])
		.'</div></fieldset>';

}

if (isset($sections['controller_info'])) {

	$html .= "\n\n"
		.'<fieldset id="ci_profiler_controller_info" style="border:1px solid #995300;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
		."\n"
		.'<legend style="color:#995300;">&nbsp;&nbsp;'.lang('profiler_controller_info')."&nbsp;&nbsp;</legend>\n"
		.'<div style="color:#995300;font-weight:normal;padding:4px 0 4px 0;">'.$sections['controller_info']
		.'</div></fieldset>';

}

if (isset($sections['queries'])) {

	if (count($sections['queries']) === 0) {
		$html .= "\n\n"
			.'<fieldset id="ci_profiler_queries" style="border:1px solid #0000FF;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
			."\n"
			.'<legend style="color:#0000FF;">&nbsp;&nbsp;'.lang('profiler_queries').'&nbsp;&nbsp;</legend>'
			."\n\n\n<table style=\"border:none; width:100%;\">\n"
			.'<tr><td style="width:100%;color:#0000FF;font-weight:normal;background-color:#eee;padding:5px;">'
			.lang('profiler_no_db')
			."</td></tr>\n</table>\n</fieldset>";
	} else {
		get_instance()->load->helper('text');
		$highlight = array('SELECT', 'DISTINCT', 'FROM', 'WHERE', 'AND', 'LEFT&nbsp;JOIN', 'ORDER&nbsp;BY', 'GROUP&nbsp;BY', 'LIMIT', 'INSERT', 'INTO', 'VALUES', 'UPDATE', 'OR&nbsp;', 'HAVING', 'OFFSET', 'NOT&nbsp;IN', 'IN', 'LIKE', 'NOT&nbsp;LIKE', 'COUNT', 'MAX', 'MIN', 'ON', 'AS', 'AVG', 'SUM', '(', ')');
		$html .= "\n\n";
		$count = 0;
		foreach ($sections['queries'] as $database => $queries) {
			$name = array_shift($queries);
			$total_time = number_format(array_shift($queries), 4).' '.lang('profiler_seconds');
			$hide_queries = (count($queries) > $toggle) ? ' display:none' : '';
			if ($hide_queries !== '') {
				$show_hide_js = '(<span style="cursor: pointer;" onclick="var s=document.getElementById(\'ci_profiler_queries_db_'.$count.'\').style;s.display=s.display==\'none\'?\'\':\'none\';this.innerHTML=this.innerHTML==\''.lang('profiler_section_show').'\'?\''.lang('profiler_section_hide').'\':\''.lang('profiler_section_show').'\';">'.lang('profiler_section_show').'</span>)';
			} else {
				$show_hide_js = '(<span style="cursor: pointer;" onclick="var s=document.getElementById(\'ci_profiler_queries_db_'.$count.'\').style;s.display=s.display==\'none\'?\'\':\'none\';this.innerHTML=this.innerHTML==\''.lang('profiler_section_hide').'\'?\''.lang('profiler_section_show').'\':\''.lang('profiler_section_hide').'\';">'.lang('profiler_section_hide').'</span>)';
			}
			$html .= '<fieldset style="border:1px solid #0000FF;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
				."\n"
				.'<legend style="color:#0000FF;">&nbsp;&nbsp;'.lang('profiler_database')
				.':&nbsp; '.$database.' ('.$name.')&nbsp;&nbsp;&nbsp;'.lang('profiler_queries')
				.': '.count($queries).' ('.$total_time.')&nbsp;&nbsp;'.$show_hide_js."</legend>\n\n\n"
				.'<table style="width:100%;'.$hide_queries.'" id="ci_profiler_queries_db_'.$count."\">\n";
			if (count($queries) === 0) {
				$html .= '<tr><td style="width:100%;color:#0000FF;font-weight:normal;background-color:#eee;padding:5px;">'
					.lang('profiler_no_queries')."</td></tr>\n";
			} else {
				foreach ($queries as $query) {
					$val = highlight_code(array_shift($query));
					foreach ($highlight as $bold) {
						$val = str_replace($bold, '<strong>'.$bold.'</strong>', $val);
					}
					$time = number_format(array_shift($query), 4);
					$html .= '<tr><td style="padding:5px;vertical-align:top;width:1%;color:#900;font-weight:normal;background-color:#ddd;">'
						.$time.'&nbsp;&nbsp;</td><td style="padding:5px;color:#000;font-weight:normal;background-color:#ddd;">'
						.$val."</td></tr>\n";
				}
			}
			$html .= "</table>\n</fieldset>";
			$count++;
		}
	}
	
}

if (isset($sections['http_headers'])) {

	$html .= "\n\n"
		.'<fieldset id="ci_profiler_http_headers" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
		."\n"
		.'<legend style="color:#000;">&nbsp;&nbsp;'.lang('profiler_headers')
		.'&nbsp;&nbsp;(<span style="cursor: pointer;" onclick="var s=document.getElementById(\'ci_profiler_httpheaders_table\').style;s.display=s.display==\'none\'?\'\':\'none\';this.innerHTML=this.innerHTML==\''.lang('profiler_section_show').'\'?\''.lang('profiler_section_hide').'\':\''.lang('profiler_section_show').'\';">'.lang('profiler_section_show')."</span>)</legend>\n\n\n"
		.'<table style="width:100%;display:none;" id="ci_profiler_httpheaders_table">'."\n";
	foreach ($sections['http_headers'] as $header => $val) {
		$html .= '<tr><td style="vertical-align:top;width:50%;padding:5px;color:#900;background-color:#ddd;">'
			.$header.'&nbsp;&nbsp;</td><td style="width:50%;padding:5px;color:#000;background-color:#ddd;">'.$val."</td></tr>\n";
	}
	$html .= "</table>\n</fieldset>";

}

if (isset($sections['session_data']) && count($sections['session_data']) > 0) {

	$html .= '<fieldset id="ci_profiler_csession" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
		.'<legend style="color:#000;">&nbsp;&nbsp;'.lang('profiler_session_data').'&nbsp;&nbsp;(<span style="cursor: pointer;" onclick="var s=document.getElementById(\'ci_profiler_session_data\').style;s.display=s.display==\'none\'?\'\':\'none\';this.innerHTML=this.innerHTML==\''.lang('profiler_section_show').'\'?\''.lang('profiler_section_hide').'\':\''.lang('profiler_section_show').'\';">'.lang('profiler_section_show').'</span>)</legend>'
		.'<table style="width:100%;display:none;" id="ci_profiler_session_data">';
	foreach ($sections['session_data'] as $key => $val) {
		$html .= '<tr><td style="padding:5px;vertical-align:top;color:#900;background-color:#ddd;">'
			.$key.'&nbsp;&nbsp;</td><td style="padding:5px;color:#000;background-color:#ddd;">'.$val."</td></tr>\n";
	}
	$html .= "</table>\n</fieldset>";

}

if (isset($sections['config'])) {

	$html .= "\n\n"
		.'<fieldset id="ci_profiler_config" style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee;">'
		."\n"
		.'<legend style="color:#000;">&nbsp;&nbsp;'.lang('profiler_config').'&nbsp;&nbsp;(<span style="cursor: pointer;" onclick="var s=document.getElementById(\'ci_profiler_config_table\').style;s.display=s.display==\'none\'?\'\':\'none\';this.innerHTML=this.innerHTML==\''.lang('profiler_section_show').'\'?\''.lang('profiler_section_hide').'\':\''.lang('profiler_section_show').'\';">'.lang('profiler_section_show')."</span>)</legend>\n\n\n"
		.'<table style="width:100%;display:none;" id="ci_profiler_config_table">'."\n";
	foreach ($sections['config'] as $config => $val) {
		$html .= '<tr><td style="padding:5px;vertical-align:top;color:#900;background-color:#ddd;">'
			.$config.'&nbsp;&nbsp;</td><td style="padding:5px;color:#000;background-color:#ddd;">'.$val."</td></tr>\n";
	}
	$html .= "</table>\n</fieldset>";

}

$html .= '</div>';

echo $html;
