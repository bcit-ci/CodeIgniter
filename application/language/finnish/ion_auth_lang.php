<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Finnish
*
* Author: Jarno Fabritius
*         jarno.fabritius@meisso.com
*         @meisso_jarno
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  01.03.2011
*
* Description:  Finnish language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful']             = 'Tili luotiin onnistuneesti!';
$lang['account_creation_unsuccessful']           = 'Tilin luonti epäonnistui';
$lang['account_creation_duplicate_email']        = 'Sähköpostiosoite on virheellinen tai se on jo käytössä';
$lang['account_creation_duplicate_identity']     = 'Tunnus on virheellinen tai se on jo käytössä';

// TODO Please Translate
$lang['account_creation_missing_default_group'] = 'Default group is not set';
$lang['account_creation_invalid_default_group'] = 'Invalid default group name set';

// Password
$lang['password_change_successful']              = 'Salasana vaihdettu!';
$lang['password_change_unsuccessful']            = 'Salasanan vaihto epäonnistui';
$lang['forgot_password_successful']              = 'Salasanan resetointiohjeet lähetettiin sähköpostiin';
$lang['forgot_password_unsuccessful']            = 'Salasanan resetointi epäonnistui';

// Activation
$lang['activate_successful']                     = 'Tili aktivoitu!';
$lang['activate_unsuccessful']                   = 'Tilin aktivointi epäonnistui';
$lang['deactivate_successful']                   = 'Tili suljettu';
$lang['deactivate_unsuccessful']                 = 'Tilin sulkeminen epäonnistui';
$lang['activation_email_successful']             = 'Aktivointiviesti lähetetty';
$lang['activation_email_unsuccessful']           = 'Aktivointiviestiä ei voitu lähettää';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful']                        = 'Olet nyt kirjautunut sisään!';
$lang['login_unsuccessful']                      = 'Kirjautuminen epäonnistui';
$lang['login_unsuccessful_not_active']           = 'Tiliä ei aktivoitu';
$lang['login_timeout']                           = 'Väliaikaisesti suljettu. Yritä uudelleen myöhemmin.';
$lang['logout_successful']                       = 'Olet nyt kirjautunut ulos';

// Account Changes
$lang['update_successful']                       = 'Tilin tiedot päivitetty!';
$lang['update_unsuccessful']                     = 'Tietojen päivitys epäonnistui';
$lang['delete_successful']                       = 'Tili poistettu';
$lang['delete_unsuccessful']                     = 'Tilin poisto epäonnistui';

// Groups
$lang['group_creation_successful']               = 'Ryhmä luotiin onnistuneesti!';
$lang['group_already_exists']                    = 'Ryhmän nimi jo käytössä';
$lang['group_update_successful']                 = 'Ryhmän tiedot päivitetty!';
$lang['group_delete_successful']                 = 'Ryhmä poistettu';
$lang['group_delete_unsuccessful']               = 'Ryhmän poisto epäonnistui';
$lang['group_delete_notallowed']    = 'Can\'t delete the administrators\' group';
$lang['group_name_required']                     = 'Ryhmän nimi tarvitaan';
$lang['group_name_admin_not_alter'] = 'Admin group name can not be changed';

// Activation Email
$lang['email_activation_subject']            = 'Tilin aktivointi';
$lang['email_activate_heading']    = 'Activate account for %s';
$lang['email_activate_subheading'] = 'Please click this link to %s.';
$lang['email_activate_link']       = 'Activate Your Account';
// Forgot Password Email
$lang['email_forgotten_password_subject']        = 'Unohtuneen salasanan palautus';
$lang['email_forgot_password_heading']    = 'Reset Password for %s';
$lang['email_forgot_password_subheading'] = 'Please click this link to %s.';
$lang['email_forgot_password_link']       = 'Reset Your Password';

