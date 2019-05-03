<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Filipino
*
* Author: Ben Edmunds
*         ben.edmunds@gmail.com
*         @benedmunds
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.14.2010
*
* Description:  Filipino language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful']            = 'Matagumpay na nagawa ang account';
$lang['account_creation_unsuccessful']          = 'Hindi magawang i-Lumikha ng Account';
$lang['account_creation_duplicate_email']       = 'Email ay Nagamit na o Hindi wastong';
$lang['account_creation_duplicate_identity']    = 'Pagkakakilanlan ay Nagamit na o Hindi wastong';
$lang['account_creation_missing_default_group'] = 'Default na grupo ay hindi nakatakda';
$lang['account_creation_invalid_default_group'] = 'Hindi wasto ang default na ngalan ng grupo';


// Password
$lang['password_change_successful']          = 'Password Matagumpay Binago';
$lang['password_change_unsuccessful']        = 'Hindi ma-Baguhin ang Password';
$lang['forgot_password_successful']          = 'Password Reset ay na-send na sa Email';
$lang['forgot_password_unsuccessful']        = 'Hindi ma-reset ang Password';

// Activation
$lang['activate_successful']                 = 'Account napagana na';
$lang['activate_unsuccessful']               = 'Hindi ma-activate Account';
$lang['deactivate_successful']               = 'Account De-Activated';
$lang['deactivate_unsuccessful']             = 'Hindi ma-De-Activate Account';
$lang['activation_email_successful']         = 'Activation Email Sent. Mangyaring suriin ang iyong inbox o spam';
$lang['activation_email_unsuccessful']       = 'Hindi magawang Magpadala Activation Email';
$lang['deactivate_current_user_unsuccessful']= 'Hindi mo Maaaring De-activate ang iyong sarili.';

// Login / Logout
$lang['login_successful']                    = 'Tagumpay na Naka-Login';
$lang['login_unsuccessful']                  = 'Maling Login';
$lang['login_unsuccessful_not_active']       = 'Account ay hindi aktibo';
$lang['login_timeout']                       = 'Pansamantalang Naka-lock Out. Subukan ulit mamaya.';
$lang['logout_successful']                   = 'Matagumpay na Naka-log Out';

// Account Changes
$lang['update_successful']                   = 'Impormasyon ng Account Matagumpay Na-Bago';
$lang['update_unsuccessful']                 = 'Hindi ma-update ang Impormasyon ng Account';
$lang['delete_successful']                   = 'User Natanggal na';
$lang['delete_unsuccessful']                 = 'Hindi magawang alisin User';

// Groups
$lang['group_creation_successful']           = 'Matagumpay na Nalikha ang Grupo';
$lang['group_already_exists']                = 'Ang pangalan ng grupo nagamit na';
$lang['group_update_successful']             = 'Detalye sa Grupo Na-Bago na';
$lang['group_delete_successful']             = 'Ang Grupo Na-Tanggal na';
$lang['group_delete_unsuccessful']           = 'Hindi matanggal ang grupo';
$lang['group_delete_notallowed']             = 'Hindi Maaaring tanggalin ang Grupo Administrator';
$lang['group_name_required']                 = 'Ang Ngalan sa Grupo ay Kailangan';
$lang['group_name_admin_not_alter']          = 'Hndi Maaaring Palitan ang Ngalan sa Grupo';

// Activation Email
$lang['email_activation_subject']            = 'Account Activation';
$lang['email_activate_heading']              = 'I-activate account para sa %s';
$lang['email_activate_subheading']           = 'Mangyaring i-click ang link na ito  %s.';
$lang['email_activate_link']                 = 'I-activate ang Iyong Account';

// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Nakalimutan mo ba ang Verification Password';
$lang['email_forgot_password_heading']       = 'I-reset ang Password para sa %s';
$lang['email_forgot_password_subheading']    = 'Mangyaring i-click ang link na ito  %s.';
$lang['email_forgot_password_link']          = 'I-reset ang Iyong Password';

