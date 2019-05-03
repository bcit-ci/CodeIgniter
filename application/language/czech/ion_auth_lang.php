<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Czech
*
* Author: Kristián Feldsam
* 		  kristian@feldsam.cz
*
*
* Created:  11.05.2012
*
* Description:  Czech language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful'] 	  	 = 'Účet byl úspěšně vytvořen';
$lang['account_creation_unsuccessful'] 	 	 = 'Nelze vytvořit účet';
$lang['account_creation_duplicate_email'] 	 = 'E-mail již existuje nebo je neplatný';
$lang['account_creation_duplicate_identity'] = 'Uživatelské jméno již existuje nebo je neplatný';

// TODO Please Translate
$lang['account_creation_missing_default_group'] = 'Default group is not set';
$lang['account_creation_invalid_default_group'] = 'Invalid default group name set';

// Password
$lang['password_change_successful'] 	 	 = 'Heslo bylo úspěšně změněno';
$lang['password_change_unsuccessful'] 	  	 = 'Nelze změnit heslo';
$lang['forgot_password_successful'] 	 	 = 'Heslo bylo odeslané na e-mail';
$lang['forgot_password_unsuccessful'] 	 	 = 'Nelze obnovit heslo';

// Activation
$lang['activate_successful'] 		  	     = 'Účet byl aktivován';
$lang['activate_unsuccessful'] 		 	     = 'Nelze aktivovat účet';
$lang['deactivate_successful'] 		  	     = 'Účet byl deaktivován';
$lang['deactivate_unsuccessful'] 	  	     = 'Nelze deaktivován účet';
$lang['activation_email_successful'] 	  	 = 'Aktivační e-mail byl odeslán';
$lang['activation_email_unsuccessful']   	 = 'Nelze odeslat aktivační e-mail';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful'] 		  	         = 'Úspěšně přihlášen';
$lang['login_unsuccessful'] 		  	     = 'Nesprávný e-mail nebo heslo';
$lang['login_unsuccessful_not_active'] 		 = 'Účet je neaktivní';
$lang['login_timeout']                       = 'Temporarily Locked Out. Try again later.';
$lang['logout_successful'] 		 	         = 'Úspěšné odhlášení';

// Account Changes
$lang['update_successful'] 		 	         = 'Informace o účtu byla úspěšně aktualizována';
$lang['update_unsuccessful'] 		 	     = 'Nelze aktualizovat informace o účtu';
$lang['delete_successful'] 		 	         = 'Uživatel byl smazán';
$lang['delete_unsuccessful'] 		 	     = 'Nelze smazat uživatele';

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
$lang['email_activation_subject']            = 'Account Activation';
$lang['email_activate_heading']    = 'Activate account for %s';
$lang['email_activate_subheading'] = 'Please click this link to %s.';
$lang['email_activate_link']       = 'Activate Your Account';
// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Forgotten Password Verification';
$lang['email_forgot_password_heading']    = 'Reset Password for %s';
$lang['email_forgot_password_subheading'] = 'Please click this link to %s.';
$lang['email_forgot_password_link']       = 'Reset Your Password';
