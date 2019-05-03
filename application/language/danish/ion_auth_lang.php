<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Danish
*
* Author:
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:
*
* Description:  Danish language file for Ion Auth messages and errors
*
*/
// Account Creation
$lang['account_creation_successful'] 			= 'Konto oprettet';
$lang['account_creation_unsuccessful'] 			= 'Det var ikke muligt at oprette kontoen';
$lang['account_creation_duplicate_email'] 		= 'Email allerede i brug eller ugyldig';
$lang['account_creation_duplicate_identity'] 	= 'Brugernavn allerede i brug eller ugyldigt';
$lang['account_creation_missing_default_group'] = 'Default group is not set';
$lang['account_creation_invalid_default_group'] = 'Invalid default group name set';
// Password
$lang['password_change_successful'] 				= 'Kodeordet er ændret';
$lang['password_change_unsuccessful'] 			= 'Det var ikke muligt at ændre kodeordet';
$lang['forgot_password_successful'] 				= 'Email vedrørende nulstilling af kodeord er afsendt';
$lang['forgot_password_unsuccessful'] 			= 'Det var ikke muligt at nulstille kodeordet';
// Activation
$lang['activate_successful'] 					= 'Konto aktiveret';
$lang['activate_unsuccessful'] 					= 'Det var ikke muligt at aktivere kontoen';
$lang['deactivate_successful'] 					= 'Konto deaktiveret';
$lang['deactivate_unsuccessful'] 				= 'Det var ikke muligt at deaktivere kontoen';
$lang['activation_email_successful'] 			= 'Email vedrørende aktivering af konto er afsendt';
$lang['activation_email_unsuccessful'] 			= 'Det var ikke muligt at sende email vedrørende aktivering af konto';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';
// Login / Logout
$lang['login_successful'] 						= 'Logged ind';
$lang['login_unsuccessful'] 						= 'Ugyldigt login';
$lang['login_unsuccessful_not_active'] 			= 'Kontoen er inaktiv';
$lang['login_timeout']                       = 'Temporarily Locked Out.  Try again later.';
$lang['logout_successful'] 						= 'Logged ud';
// Account Changes
$lang['update_successful'] 						= 'Kontoen er opdateret';
$lang['update_unsuccessful'] 					= 'Det var ikke muligt at opdatere kontoen';
$lang['delete_successful'] 						= 'Bruger slettet';
$lang['delete_unsuccessful'] 					= 'Det var ikke muligt at slette bruger';
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
$lang['email_activation_subject']            = 'Konto aktivering';
$lang['email_activate_heading']    = 'Activate account for %s';
$lang['email_activate_subheading'] = 'Please click this link to %s.';
$lang['email_activate_link']       = 'Activate Your Account';
// Forgot Password Email
$lang['email_forgotten_password_subject']    	= 'Verifikation af glemt adgangskode';
$lang['email_forgot_password_heading']    = 'Reset Password for %s';
$lang['email_forgot_password_subheading'] = 'Please click this link to %s.';
$lang['email_forgot_password_link']       = 'Reset Your Password';
