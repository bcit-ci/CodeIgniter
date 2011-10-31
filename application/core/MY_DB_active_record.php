<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * THIS SOFTWARE AND DOCUMENTATION IS PROVIDED "AS IS," AND COPYRIGHT
 * HOLDERS MAKE NO REPRESENTATIONS OR WARRANTIES, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO, WARRANTIES OF MERCHANTABILITY OR
 * FITNESS FOR ANY PARTICULAR PURPOSE OR THAT THE USE OF THE SOFTWARE
 * OR DOCUMENTATION WILL NOT INFRINGE ANY THIRD PARTY PATENTS,
 * COPYRIGHTS, TRADEMARKS OR OTHER RIGHTS.COPYRIGHT HOLDERS WILL NOT
 * BE LIABLE FOR ANY DIRECT, INDIRECT, SPECIAL OR CONSEQUENTIAL
 * DAMAGES ARISING OUT OF ANY USE OF THE SOFTWARE OR DOCUMENTATION.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://gnu.org/licenses/>.
*/
class MY_DB_active_record extends CI_DB_active_record {
	
	public function _max_min_avg_sum($select = '', $alias = '', $type = 'MAX')
	{
		return parent::_max_min_avg_sum($select, $alias, $type);
	}
	
	public function _create_alias_from_table($item)
	{
		return parent::_create_alias_from_table($item);
	}
	
	public function _where($key, $value = NULL, $type = 'AND ', $escape = NULL)
	{
		return parent::_where($key, $value, $type, $escape);
	}

	public function _where_in($key = NULL, $values = NULL, $not = FALSE, $type = 'AND ')
	{
		return parent::_where_in($key, $values, $not, $type);
	}
	
	public function _like($field, $match = '', $type = 'AND ', $side = 'both', $not = '')
	{
		return parent::_like($field, $match, $type, $side, $not);
	}
	
	public function _having($key, $value = '', $type = 'AND ', $escape = TRUE)
	{
		return parent::_having($key, $value, $type, $escape);
	}
	
	public function _track_aliases($table)
	{
		return parent::_track_aliases($table);
	}
	
	public function _compile_select($select_override = FALSE)
	{
		return parent::_compile_select($select_override);
	}
	
	public function _merge_cache()
	{
		return parent::_merge_cache();
	}
	
	public function _reset_run($ar_reset_items)
	{
		return parent::_reset_run($ar_reset_items);
	}
	
	public function _reset_select()
	{
		return parent::_reset_select();
	}
	
	public function _reset_write()
	{
		return parent::_reset_write();
	}
}

/* End of file MY_DB_active_record.php */
/* Location: ./application/core/MY_DB_active_record.php */