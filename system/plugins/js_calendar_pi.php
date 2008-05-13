<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/*
Instructions:

Load the plugin using:

 	$this->load->plugin('js_calendar');

Once loaded you'll add the calendar script to the <head> of your page like this:

<?php echo js_calendar_script('my_form');  ?>

The above function will be passed the name of your form.

Then to show the actual calendar you'll do this:

<?php echo js_calendar_write('entry_date', time(), true);?>
<form name="my_form">
<input type="text" name="entry_date" value="" onblur="update_calendar(this.name, this.value);" />
<p><a href="javascript:void(0);" onClick="set_to_time('entry_date', '<?php echo time();?>')" >Today</a></p>
</form>


Note:  The first parameter is the name of the field containing your date, the second parameter contains the "now" time,
and the third tells the calendar whether to highlight the current day or not.

Lastly, you'll need some CSS for your calendar:

.calendar {
	border: 1px #6975A3 solid;
	background-color: transparent;
}
.calheading {
	background-color: #7C8BC0;
	color: #fff;
	font-family: Lucida Grande, Verdana, Geneva, Sans-serif;
	font-size: 11px;
	font-weight: bold;
	text-align: center;
}
.calnavleft {
	background-color: #7C8BC0;
	font-family: Lucida Grande, Verdana, Geneva, Sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: #fff;
	padding: 4px;
	cursor: pointer;
}
.calnavright {
	background-color: #7C8BC0;
	font-family: Lucida Grande, Verdana, Geneva, Sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: #fff;
	text-align:  right;
	padding: 4px;
	cursor: pointer;
}
.caldayheading {
	background-color: #000;
	color: #fff;
	font-family: Lucida Grande, Verdana, Geneva, Sans-serif;
	font-size: 10px;
	text-align: center;
	padding: 6px 2px 6px 2px;
}
.caldaycells{
	color: #000;
	background-color: #D1D7E6;
	font-family: Lucida Grande, Verdana, Geneva, Sans-serif;
	font-size: 11px;
	text-align: center;
	padding: 4px;
	border: 1px #E0E5F1 solid;
	cursor: pointer;
}
.caldaycellhover{
	color: #fff;
	background-color: #B3BCD4;
	font-family: Lucida Grande, Verdana, Geneva, Sans-serif;
	font-size: 11px;
	text-align: center;
	padding: 4px;
	border: 1px #B3BCD4 solid;
	cursor: pointer;
}
.caldayselected{
	background-color: #737FAC;
	color:	#fff;
	font-family: Lucida Grande, Verdana, Geneva, Sans-serif;
	font-size: 11px;
	font-weight: bold;
	text-align: center;
	border: 1px #566188 solid;
	padding: 3px;
	cursor: pointer;
}
.calblanktop {
	background-color: #fff;
	padding: 4px;
}
.calblankbot {
	background-color: #fff;
	padding: 4px;
}


*/

function js_calendar_script($form_name = 'entryform')
{		
$CI =& get_instance();
$CI->load->language('calendar');
ob_start();
?>
<script type="text/javascript">
<!--
var form_name	= "<?php echo $form_name; ?>";
var format		= 'us'; // eu or us
var days		= new Array(
					'<?php echo $CI->lang->line('cal_su');?>', // Sunday, short name
					'<?php echo $CI->lang->line('cal_mo');?>', // Monday, short name
					'<?php echo $CI->lang->line('cal_tu');?>', // Tuesday, short name
					'<?php echo $CI->lang->line('cal_wed');?>', // Wednesday, short name
					'<?php echo $CI->lang->line('cal_thu');?>', // Thursday, short name
					'<?php echo $CI->lang->line('cal_fri');?>', // Friday, short name
					'<?php echo $CI->lang->line('cal_sat');?>' // Saturday, short name
				);
var months		= new Array(
					'<?php echo $CI->lang->line('cal_january');?>',
					'<?php echo $CI->lang->line('cal_february');?>',
					'<?php echo $CI->lang->line('cal_march');?>',
					'<?php echo $CI->lang->line('cal_april');?>',
					'<?php echo $CI->lang->line('cal_mayl');?>',
					'<?php echo $CI->lang->line('cal_june');?>',
					'<?php echo $CI->lang->line('cal_july');?>',
					'<?php echo $CI->lang->line('cal_august');?>',
					'<?php echo $CI->lang->line('cal_september');?>',
					'<?php echo $CI->lang->line('cal_october');?>',
					'<?php echo $CI->lang->line('cal_november');?>',
					'<?php echo $CI->lang->line('cal_december');?>'
				);
var last_click	= new Array();
var current_month  = '';
var current_year   = '';
var last_date  = '';
	
function calendar(id, d, highlight, adjusted)
{		
	if (adjusted == undefined)
	{	
		var d = new Date(d * 1000);
	}

	this.id			= id;
	this.highlight	= highlight;
	this.date_obj	= d;
	this.write		= build_calendar;
	this.total_days	= total_days;
	this.month		= d.getMonth();
	this.date		= d.getDate();
	this.day		= d.getDay();
	this.year		= d.getFullYear();
	this.hours		= d.getHours();
	this.minutes	= d.getMinutes();
	this.seconds	= d.getSeconds();
	this.date_str	= date_str;
				
	if (highlight == false)
	{
		this.selected_date = '';
	}
	else
	{
		this.selected_date = this.year + '' + this.month + '' + this.date;
	}
			
	//	Set the "selected date"
	d.setDate(1);
	this.firstDay = d.getDay();
	
	//then reset the date object to the correct date
	d.setDate(this.date);
}
		
//	Build the body of the calendar
function build_calendar()
{
	var str = '';
	
	//	Calendar Heading
	
	str += '<div id="cal' + this.id + '">';
	str += '<table class="calendar" cellspacing="0" cellpadding="0" border="0" >';
	str += '<tr>';
	str += '<td class="calnavleft" onClick="change_month(-1, \'' + this.id + '\')">&lt;&lt;<\/td>';
	str += '<td colspan="5" class="calheading">' + months[this.month] + ' ' + this.year + '<\/td>';
	str += '<td class="calnavright" onClick="change_month(1, \'' + this.id + '\')">&gt;&gt;<\/td>';
	str += '<\/tr>';
	
	//	Day Names
	
	str += '<tr>';
	
	for (i = 0; i < 7; i++)
	{
		str += '<td class="caldayheading">' + days[i] + '<\/td>';
	}
	
	str += '<\/tr>';
	
	//	Day Cells
		
	str += '<tr>';
	
	selDate = (last_date != '') ? last_date : this.date;
	
	for (j = 0; j < 42; j++)
	{
		var displayNum = (j - this.firstDay + 1);
		
		if (j < this.firstDay) // leading empty cells
		{
			str += '<td class="calblanktop">&nbsp;<\/td>';
		}
		else if (displayNum == selDate && this.highlight == true) // Selected date
		{
			str += '<td id="' + this.id +'selected" class="caldayselected" onClick="set_date(this,\'' + this.id + '\')">' + displayNum + '<\/td>';
		}
		else if (displayNum > this.total_days())
		{
			str += '<td class="calblankbot">&nbsp;<\/td>'; // trailing empty cells
		}
		else  // Unselected days
		{
			str += '<td id="" class="caldaycells" onClick="set_date(this,\'' + this.id + '\'); return false;"  onMouseOver="javascript:cell_highlight(this,\'' + displayNum + '\',\'' + this.id + '\');" onMouseOut="javascript:cell_reset(this,\'' + displayNum + '\',\'' + this.id + '\');" >' + displayNum + '<\/td>';
		}
		
		if (j % 7 == 6)
		{
			str += '<\/tr><tr>';
		}
	}

	str += '<\/tr>';	
	str += '<\/table>';
	str += '<\/div>';
	
	return str;
}

//	Total number of days in a month
function total_days()
{	
	switch(this.month)
	{
		case 1: // Check for leap year
			if ((  this.date_obj.getFullYear() % 4 == 0
				&& this.date_obj.getFullYear() % 100 != 0)
				|| this.date_obj.getFullYear() % 400 == 0)
				return 29;
			else
				return 28;
		case 3:
			return 30;
		case 5:
			return 30;
		case 8:
			return 30;
		case 10:
			return 30
		default:
			return 31;
	}
}

//	Highlight Cell on Mouseover
function cell_highlight(td, num, cal)
{
	cal = eval(cal);

	if (last_click[cal.id]  != num)
	{
		td.className = "caldaycellhover";
	}
}		

//	Reset Cell on MouseOut
function cell_reset(td, num, cal)
{	
	cal = eval(cal);

	if (last_click[cal.id] == num)
	{
		td.className = "caldayselected";
	}
	else
	{
		td.className = "caldaycells";
	}
}		

//	Clear Field
function clear_field(id)
{				
	eval("document." + form_name + "." + id + ".value = ''");
	
	document.getElementById(id + "selected").className = "caldaycells";
	document.getElementById(id + "selected").id = "";	
	
	cal = eval(id);
	cal.selected_date = '';		
}		


//	Set date to specified time
function set_to_time(id, raw)
{			
	if (document.getElementById(id + "selected"))
	{			
		document.getElementById(id + "selected").className = "caldaycells";
		document.getElementById(id + "selected").id = "";	
	}
	
	document.getElementById('cal' + id).innerHTML = '<div id="tempcal'+id+'">&nbsp;<'+'/div>';				
		
	var nowDate = new Date();
	nowDate.setTime = raw * 1000;
	
	current_month	= nowDate.getMonth();
	current_year	= nowDate.getFullYear();
	current_date	= nowDate.getDate();
	
	oldcal = eval(id);
	oldcal.selected_date = current_year + '' + current_month + '' + current_date;				

	cal = new calendar(id, nowDate, true, true);		
	cal.selected_date = current_year + '' + current_month + '' + current_date;	
	
	last_date = cal.date;
	
	document.getElementById('tempcal'+id).innerHTML = cal.write();	
	
	insert_date(cal);
}

//	Set date to what is in the field
var lastDates = new Array();

function update_calendar(id, dateValue)
{
	if (lastDates[id] == dateValue) return;
	
	lastDates[id] = dateValue;
	
	var fieldString = dateValue.replace(/\s+/g, ' ');
	
	while (fieldString.substring(0,1) == ' ')
	{
		fieldString = fieldString.substring(1, fieldString.length);
	}
	
	var dateString = fieldString.split(' ');
	var dateParts = dateString[0].split('-')

	if (dateParts.length < 3) return;
	var newYear  = dateParts[0];
	var newMonth = dateParts[1];
	var newDay   = dateParts[2];
	
	if (isNaN(newDay)  || newDay < 1 || (newDay.length != 1 && newDay.length != 2)) return;
	if (isNaN(newYear) || newYear < 1 || newYear.length != 4) return;
	if (isNaN(newMonth) || newMonth < 1 || (newMonth.length != 1 && newMonth.length != 2)) return;
	
	if (newMonth > 12) newMonth = 12;
	
	if (newDay > 28)
	{
		switch(newMonth - 1)
		{
			case 1: // Check for leap year
				if ((newYear % 4 == 0 && newYear % 100 != 0) || newYear % 400 == 0)
				{
					if (newDay > 29) newDay = 29;
				}
				else
				{
					if (newDay > 28) newDay = 28;
				}
			case 3:
				if (newDay > 30) newDay = 30;
			case 5:
				if (newDay > 30) newDay = 30;
			case 8:
				if (newDay > 30) newDay = 30;
			case 10:
				if (newDay > 30) newDay = 30;
			default:
				if (newDay > 31) newDay = 31;
		}
	}
	
	if (document.getElementById(id + "selected"))
	{			
		document.getElementById(id + "selected").className = "caldaycells";
		document.getElementById(id + "selected").id = "";	
	}
	
	document.getElementById('cal' + id).innerHTML = '<div id="tempcal'+id+'">&nbsp;<'+'/div>';				
		
	var nowDate = new Date();
	nowDate.setDate(newDay);
	nowDate.setMonth(newMonth - 1);
	nowDate.setYear(newYear);
	nowDate.setHours(12);
	
	current_month	= nowDate.getMonth();
	current_year	= nowDate.getFullYear();

	cal = new calendar(id, nowDate, true, true);						
	document.getElementById('tempcal'+id).innerHTML = cal.write();	
}

//	Set the date
function set_date(td, cal)
{					

	cal = eval(cal);
	
	// If the user is clicking a cell that is already
	// selected we'll de-select it and clear the form field
	
	if (last_click[cal.id] == td.firstChild.nodeValue)
	{
		td.className = "caldaycells";
		last_click[cal.id] = '';
		remove_date(cal);
		cal.selected_date =  '';
		return;
	}
				
	// Onward!
	if (document.getElementById(cal.id + "selected"))
	{
		document.getElementById(cal.id + "selected").className = "caldaycells";
		document.getElementById(cal.id + "selected").id = "";
	}
									
	td.className = "caldayselected";
	td.id = cal.id + "selected";

	cal.selected_date = cal.date_obj.getFullYear() + '' + cal.date_obj.getMonth() + '' + cal.date;			
	cal.date_obj.setDate(td.firstChild.nodeValue);
	cal = new calendar(cal.id, cal.date_obj, true, true);
	cal.selected_date = cal.date_obj.getFullYear() + '' + cal.date_obj.getMonth() + '' + cal.date;			
	
	last_date = cal.date;

	//cal.date
	last_click[cal.id] = cal.date;
				
	// Insert the date into the form
	insert_date(cal);
}
/*
//	Insert the date into the form field
function insert_date(cal)
{
	cal = eval(cal);
	fval = eval("document." + form_name + "." + cal.id);	
	
	if (fval.value == '')
	{
		fval.value = cal.date_str('y');
	}
	else
	{
		time = fval.value.substring(10);
		new_date = cal.date_str('n') + time;
		fval.value = new_date;
	}	
}
*/		
//	Remove the date from the form field
function remove_date(cal)
{
	cal = eval(cal);
	fval = eval("document." + form_name + "." + cal.id);	
	fval.value = '';
}

//	Change to a new month
function change_month(mo, cal)
{		
	cal = eval(cal);

	if (current_month != '')
	{
		cal.date_obj.setMonth(current_month);
		cal.date_obj.setYear(current_year);
	
		current_month	= '';
		current_year	= '';
	}
				
	var newMonth = cal.date_obj.getMonth() + mo;
	var newDate  = cal.date_obj.getDate();
	
	if (newMonth == 12)
	{
		cal.date_obj.setYear(cal.date_obj.getFullYear() + 1)
		newMonth = 0;
	}
	else if (newMonth == -1)
	{
		cal.date_obj.setYear(cal.date_obj.getFullYear() - 1)
		newMonth = 11;
	}
	
	if (newDate > 28)
	{
		var newYear = cal.date_obj.getFullYear();
		
		switch(newMonth)
		{
			case 1: // Check for leap year
				if ((newYear % 4 == 0 && newYear % 100 != 0) || newYear % 400 == 0)
				{
					if (newDate > 29) newDate = 29;
				}
				else
				{
					if (newDate > 28) newDate = 28;
				}
			case 3:
				if (newDate > 30) newDate = 30;
			case 5:
				if (newDate > 30) newDate = 30;
			case 8:
				if (newDate > 30) newDate = 30;
			case 10:
				if (newDate > 30) newDate = 30;
			default:
				if (newDate > 31) newDate = 31;
		}
	}
	
	cal.date_obj.setDate(newDate);
	cal.date_obj.setMonth(newMonth);
	new_mdy	= cal.date_obj.getFullYear() + '' + cal.date_obj.getMonth() + '' + cal.date;
	
	highlight = (cal.selected_date == new_mdy) ? true : false;			
	cal = new calendar(cal.id, cal.date_obj, highlight, true); 			
	document.getElementById('cal' + cal.id).innerHTML = cal.write();	
}

//	Finalize the date string
function date_str(time)
{
	var month = this.month + 1;
	if (month < 10)
		month = '0' + month;
		
	var day		= (this.date  < 10) 	?  '0' + this.date		: this.date;
	var minutes	= (this.minutes  < 10)	?  '0' + this.minutes	: this.minutes;
		
	if (format == 'us')
	{
		var hours	= (this.hours > 12) ? this.hours - 12 : this.hours;
		var ampm	= (this.hours > 11) ? 'PM' : 'AM'
	}
	else
	{
		var hours	= this.hours;
		var ampm	= '';
	}
	
	if (time == 'y')
	{
		return this.year + '-' + month + '-' + day + '  ' + hours + ':' + minutes + ' ' + ampm;		
	}
	else
	{
		return this.year + '-' + month + '-' + day;
	}
}

//-->
</script>
<?php

$r = ob_get_contents();
ob_end_clean();
return $r;
}


function js_calendar_write($field_id, $time = '', $highlight = TRUE)
{
	if ($time == '')
		$time = time();

	return
	'<script type="text/javascript">
		var '.$field_id.' = new calendar("'.$field_id.'", '.$time.', '.(($highlight == TRUE) ? 'true' : 'false').');
		document.write('.$field_id.'.write());
	</script>';	
}	


/* End of file js_calendar_pi.php */
/* Location: ./system/plugins/js_calendar_pi.php */