<?php
class Login_model extends CI_Model
{
    public function can_login($email, $password)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('codeigniter_register');

        if ($query->num_rows() > 0) {
            $row = $query->row();
            $stored_password = $row->password;

            if (password_verify($password, $stored_password)) {
                $this->session->set_userdata('id', $row->id);
                redirect('note'); 
            } else {
                return 'Wrong Password';
            }
        } else {
            return 'Wrong Email Address';
        }
    }
}
?>
