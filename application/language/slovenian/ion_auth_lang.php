<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - Slovenian
*
* Author: Žiga Drnovšček
* 		  ziga.drnovscek@gmail.com
*
*
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  12.5.2013
*
* Description:  Slovenian language file for Ion Auth example views
*
*/

// Ustvarjanje računa
$lang['account_creation_successful'] 	  	 = 'Račun je bil uspešno ustvarjen';
$lang['account_creation_unsuccessful'] 	 	 = 'Ni mogoče ustvariti računa';
$lang['account_creation_duplicate_email'] 	 = 'Elektronski naslov je neveljaven ali pa že obstaja';
$lang['account_creation_duplicate_identity'] = 'Uporabniško ime je neveljavno ali pa že obstaja';

// TODO Please Translate
$lang['account_creation_missing_default_group'] = 'Default group is not set';
$lang['account_creation_invalid_default_group'] = 'Invalid default group name set';

// Geslo
$lang['password_change_successful'] 	 	 = 'Geslo je bilo uspešno spremenjeno';
$lang['password_change_unsuccessful'] 	  	 = 'Ni mogoče spremeniti gesla';
$lang['forgot_password_successful'] 	 	 = 'Zahteva za ponastavitev gesla je bila uspešno poslana';
$lang['forgot_password_unsuccessful'] 	 	 = 'Gesla ni mogoče ponastaviti';

// Aktivacija
$lang['activate_successful'] 		  	     = 'Račun aktiviran';
$lang['activate_unsuccessful'] 		 	     = 'Ni mogoče aktivirati računa';
$lang['deactivate_successful'] 		  	     = 'Račun deaktiviran';
$lang['deactivate_unsuccessful'] 	  	     = 'Ni mogoče deaktivirati računa';
$lang['activation_email_successful'] 	  	 = 'Aktivacijska pošta uspešno poslana';
$lang['activation_email_unsuccessful']   	 = 'Aktivacijske pošte ni možno poslati';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Prijava / Odjava
$lang['login_successful'] 		  	         = 'Uspešna prijava';
$lang['login_unsuccessful'] 		  	     = 'Neuspešna prijava';
$lang['login_unsuccessful_not_active'] 		 = 'Račun je neaktiven';
$lang['login_timeout']                       = 'Začasno zaklenjen. Poskusite ponovno pozneje.';
$lang['logout_successful'] 		 	         = 'Uspešna odjava';

// Sprememba računa
$lang['update_successful'] 		 	         = 'Informacije računa so bile uspešno posodobljene';
$lang['update_unsuccessful'] 		 	     = 'Informacije računa ni možno posodobljene';
$lang['delete_successful']               = 'Uporabnik izbrisan';
$lang['delete_unsuccessful']           = 'Ni možno izbrisati uporabnika';

// Skupina
$lang['group_creation_successful']  = 'Skupina je bila uspešno ustvarjena';
$lang['group_already_exists']       = 'Ime skupine že obstaja';
$lang['group_update_successful']    = 'Podatki o skupini so bili uspešno posodobljeni';
$lang['group_delete_successful']    = 'Skupina izbrisana';
$lang['group_delete_unsuccessful'] 	= 'Ni možno izbrisati skupine';
$lang['group_delete_notallowed']    = 'Can\'t delete the administrators\' group';
$lang['group_name_required'] 		= 'Ime skupine je obvezno polje';
$lang['group_name_admin_not_alter'] = 'Admin group name can not be changed';

// Activation Email
$lang['email_activation_subject']            = 'Aktivacija računa';
$lang['email_activate_heading']    = 'Activate account for %s';
$lang['email_activate_subheading'] = 'Please click this link to %s.';
$lang['email_activate_link']       = 'Activate Your Account';
// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Pozabljeno geslo';
$lang['email_forgot_password_heading']    = 'Reset Password for %s';
$lang['email_forgot_password_subheading'] = 'Please click this link to %s.';
$lang['email_forgot_password_link']       = 'Reset Your Password';
