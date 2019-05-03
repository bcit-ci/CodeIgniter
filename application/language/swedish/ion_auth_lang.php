<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Swedish
*
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.14.2010
*
* Description:  Swedish language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful']         = 'Kontot har nu skapats';
$lang['account_creation_unsuccessful']       = 'Det gick inte att skapa kontot';
$lang['account_creation_duplicate_email']    = 'E-postadressen är ogiltig eller används redan';
$lang['account_creation_duplicate_identity'] = 'Användarnamnet är ogiltigt eller används redan';

// TODO Please Translate
$lang['account_creation_missing_default_group'] = 'Standard grupp är inte satt';
$lang['account_creation_invalid_default_group'] = 'Ogiltlig standard grupp namn satt';


// Password
$lang['password_change_successful']      = 'Lösenordet har nu ändrats';
$lang['password_change_unsuccessful']    = 'Det gick inte att ändra lösenordet';
$lang['forgot_password_successful']      = 'E-postadressen för återställning av lösenord har nu skickats';
$lang['forgot_password_unsuccessful']    = 'Det gick inte att återställa lösenordet';

// Activation
$lang['activate_successful']              = 'Kontot aktiverades';
$lang['activate_unsuccessful']            = 'Det gick inte att aktivera kontot';
$lang['deactivate_successful']            = 'Kontot inaktiverades';
$lang['deactivate_unsuccessful']          = 'Det gick inte att inaktivera kontot';
$lang['activation_email_successful']      = 'En aktveringslänk har skickats till din e-post';
$lang['activation_email_unsuccessful']    = 'E-post med aktiveringslänk kunde inte skickas';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful']       = 'Du är nu inloggad';
$lang['login_unsuccessful']     = 'Inloggningen misslyckades';
$lang['login_unsuccessful_not_active'] 		 = 'Account is inactive';
$lang['login_timeout']                       = 'Temporarily Locked Out.  Try again later.';
$lang['logout_successful']      = 'Du är nu utloggad';

// Account Changes
$lang['update_successful']      = 'Kontouppgifterna uppdaterades';
$lang['update_unsuccessful']    = 'Det gick inte att uppdatera kontouppgifterna';
$lang['delete_successful']      = 'Användaren är borttagen';
$lang['delete_unsuccessful']    = 'Det gick inte att ta bort användaren';

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
$lang['email_activation_subject']   = 'Kontoaktivering';
$lang['email_activate_heading']     = 'Kontoaktivering för %s';
$lang['email_activate_subheading']  = 'Klicka denna länk för att %s.';
$lang['email_activate_link']        = 'aktivera ditt konto';

// Forgot Password Email
$lang['email_forgotten_password_subject'] = 'Glömt lösenordsverifikation';
$lang['email_forgot_password_heading']    = 'Glömt lösenord för %s';
$lang['email_forgot_password_subheading'] = 'Klicka denna länk för att %s.';
$lang['email_forgot_password_link']       = 'återställa ditt lösenord';

