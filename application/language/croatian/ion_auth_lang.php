<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Croatian
*
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
*
* Translation: primjeri
*		info@primjeri.com
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.14.2010
*
* Description:  Croatian language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful'] 	  	 = 'Račun je uspješno kreiran';
$lang['account_creation_unsuccessful'] 	 	 = 'Račun nije kreiran';
$lang['account_creation_duplicate_email'] 	 = 'Email je već iskorišten ili krivi';
$lang['account_creation_duplicate_identity'] = 'Korisničko ime je već iskorišteno ili krivo';

// TODO Please Translate
$lang['account_creation_missing_default_group'] = 'Default group is not set';
$lang['account_creation_invalid_default_group'] = 'Invalid default group name set';

// Password
$lang['password_change_successful'] 	 	 = 'Lozinka uspješno promjenjena';
$lang['password_change_unsuccessful'] 	  	 = 'Lozinka nije promjenjena';
$lang['forgot_password_successful'] 	 	 = 'Email za poništenje lozinke je poslan';
$lang['forgot_password_unsuccessful'] 	 	 = 'lozinka nije poništena';

// Activation
$lang['activate_successful'] 		  	     = 'Račun je aktiviran';
$lang['activate_unsuccessful'] 		 	     = 'Aktiviranje računa nije uspjelo';
$lang['deactivate_successful'] 		  	     = 'Račun je deaktiviran';
$lang['deactivate_unsuccessful'] 	  	     = 'De-aktivacija računa noje uspjela';
$lang['activation_email_successful'] 	  	 = 'Email za aktivaciju je poslan';
$lang['activation_email_unsuccessful']   	 = 'Slanje mail za aktivaciju nije uspjelo';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful'] 		  	         = 'Uspješno prijavljeni';
$lang['login_unsuccessful'] 		  	     = 'Prijava nije uspjela';
$lang['login_unsuccessful_not_active'] 		 = 'Račun nije aktivan';
$lang['login_timeout']                       = 'Temporarily Locked Out. Try again later.';
$lang['logout_successful'] 		 	         = 'Uspješno ste odjavljeni';

// Account Changes
$lang['update_successful'] 		 	         = 'Podaci o računu uspješno su a≈æurirani';
$lang['update_unsuccessful'] 		 	     = 'Podaci o računu nisu ažurirani';
$lang['delete_successful'] 		 	         = 'Korisnik je obrisan';
$lang['delete_unsuccessful'] 		 	     = 'Brisanje korisnika nije uspjelo';

// Groups
$lang['group_creation_successful']  = 'Group created Successfully';
$lang['group_already_exists']       = 'Group name already taken';
$lang['group_update_successful']    = 'Group details updated';
$lang['group_delete_successful']    = 'Group deleted';
$lang['group_delete_unsuccessful'] 	= 'Unable to delete group';
$lang['group_delete_notallowed']    = 'Can\'t delete the administrators\' group';
$lang['group_name_required'] 		= 'Group name is a required field';
$lang['group_name_admin_not_alter'] = 'Admin group name can not be changed';

// Activation Email
$lang['email_activation_subject']            = 'Aktivacija računa';
$lang['email_activate_heading']    = 'Activate account for %s';
$lang['email_activate_subheading'] = 'Please click this link to %s.';
$lang['email_activate_link']       = 'Activate Your Account';
// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Potvrda o zaboravljenoj lozinci';
$lang['email_forgot_password_heading']    = 'Reset Password for %s';
$lang['email_forgot_password_subheading'] = 'Please click this link to %s.';
$lang['email_forgot_password_link']       = 'Reset Your Password';
