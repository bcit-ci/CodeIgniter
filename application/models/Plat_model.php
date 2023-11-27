<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
                        
class Plat_model extends CI_Model 
{
    public function get_plat_from_day($id_day)
    {
        $this->db->join('day', 'plat.id_day = day.id');
        $this->db->where('plat.id_day', $id_day);

        $query = $this->db->get('plat');

        return $query->row();
    }                        
                        
}


/* End of file Plat_model.php and path /application/models/Plat_model.php */
