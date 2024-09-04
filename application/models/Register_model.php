<?php
class Register_model extends CI_Model
{
 function insert($data)
 {
  // Assuming $data['password'] is already encrypted using OpenSSL before being passed to this function
  $this->db->insert('codeigniter_register', $data);
  return $this->db->insert_id();
 }

 function verify_email($key)
 {
  $this->db->where('verification_key', $key);
  $this->db->where('is_email_verified', 'no');
  $query = $this->db->get('codeigniter_register');
  
  if($query->num_rows() > 0)
  {
   $data = array(
    'is_email_verified'  => 'yes'
   );
   $this->db->where('verification_key', $key);
   $this->db->update('codeigniter_register', $data);
   return true;
  }
  else
  {
   return false;
  }
 }
}
?>
