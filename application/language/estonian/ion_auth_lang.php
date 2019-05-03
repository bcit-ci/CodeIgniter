<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Name:  Ion Auth Lang - Estonian
 *
 * Author: Esko Lehtme
 *         esko@tsoon.com
 *         @eskolehtme
 *
 * Location: http://github.com/benedmunds/ion_auth/
 *
 * Created:  01.09.2011
 *
 * Description:  Estonian language file for Ion Auth messages and errors
 *
 */

// Account Creation
$lang['account_creation_successful']         = 'Konto on loodud';
$lang['account_creation_unsuccessful']       = 'Konto loomine ebaõnnestus';
$lang['account_creation_duplicate_email']    = 'E-posti aadress on juba kasutusel või vigane.';
$lang['account_creation_duplicate_identity'] = 'Kasutajanimi on juba kasutusel või vigane.';

// TODO Please Translate
$lang['account_creation_missing_default_group'] = 'Default group is not set';
$lang['account_creation_invalid_default_group'] = 'Invalid default group name set';


// Password
$lang['password_change_successful']          = 'Salasõna on muudetud.';
$lang['password_change_unsuccessful']        = 'Salasõna muutmine ebaõnnestus.';
$lang['forgot_password_successful']          = 'Sinu e-postile saadeti kiri edasise juhendiga.';
$lang['forgot_password_unsuccessful']        = 'Salasõna muutmine ebaõnnestus.';

// Activation
$lang['activate_successful']                 = 'Konto on aktiveeritud';
$lang['activate_unsuccessful']               = 'Konto aktiveerimine ebaõnnestus.';
$lang['deactivate_successful']               = 'Konto on taas aktiivne';
$lang['deactivate_unsuccessful']             = 'Konto aktiveerimine ebaõnnestus.';
$lang['activation_email_successful']         = 'Sinu e-postile saadeti kiri edasise juhendiga.';
$lang['activation_email_unsuccessful']       = 'Aktiveerimiskirja saatmine ebaõnnestus.';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful']                    = 'Oled sisse logitud';
$lang['login_unsuccessful']                  = 'Sisenemine ebaõnnestus.';
$lang['login_unsuccessful_not_active'] 		 = 'Account is inactive';
$lang['login_timeout']                       = 'Temporarily Locked Out.  Try again later.';
$lang['logout_successful']                   = 'Oled välja logitud';

// Account Changes
$lang['update_successful']                   = 'Sinu andmed on muudetud';
$lang['update_unsuccessful']                 = 'Andmete muutmine ebaõnnestus.';
$lang['delete_successful']                   = 'Kasutaja on eemaldatud';
$lang['delete_unsuccessful']                 = 'Kasutajat eemaldamine ebaõnnestus.';

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
