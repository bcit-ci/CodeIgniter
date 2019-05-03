<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - Filipino
*
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
*
* Author: Daniel Davis
*         @ourmaninjapan
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.09.2013
*
* Deskripsyon:  Filipino language file for Ion Auth example views
*
*/

// Errors
$lang['error_csrf'] = 'Ang form na ito post ay hindi pumasa sa aming mga pagsusuri ng seguridad.';

// Login
$lang['login_heading']         = 'Mag-login';
$lang['login_subheading']      = 'Mangyaring mag-login sa iyong email/username at password sa ibaba.';
$lang['login_identity_label']  = 'Email/Username:';
$lang['login_password_label']  = 'Password:';
$lang['login_remember_label']  = 'Tandaan mo ako:';
$lang['login_submit_btn']      = 'Mag-Login';
$lang['login_forgot_password'] = 'Nakalimutan ang password?';

// Index
$lang['index_heading']           = 'Users';
$lang['index_subheading']        = 'Sa ibaba ay isang listahan ng mga users.';
$lang['index_fname_th']          = 'Pangalan';
$lang['index_lname_th']          = 'Huling pangalan';
$lang['index_email_th']          = 'Email';
$lang['index_groups_th']         = 'Gropo';
$lang['index_status_th']         = 'katayuan';
$lang['index_action_th']         = 'aksyon';
$lang['index_active_link']       = 'aktibo';
$lang['index_inactive_link']     = 'Hindi aktibo';
$lang['index_create_user_link']  = 'Lumikha ng isang bagong user';
$lang['index_create_group_link'] = 'Lumikha ng isang bagong grupo';

// Deactivate User
$lang['deactivate_heading']                  = 'Huwag paganahin ang User';
$lang['deactivate_subheading']               = 'Sigurado ka bang gusto mong i-deactivate ang user \'%s\'';
$lang['deactivate_confirm_y_label']          = 'Oo:';
$lang['deactivate_confirm_n_label']          = 'Hindi:';
$lang['deactivate_submit_btn']               = 'Ipasa';
$lang['deactivate_validation_confirm_label'] = 'pagpapatibay';
$lang['deactivate_validation_user_id_label'] = 'user ID';

// Create User
$lang['create_user_heading']                           = 'Lumikha User';
$lang['create_user_subheading']                        = 'Mangyaring ipasok ang impormasyon ng user sa ibaba.';
$lang['create_user_fname_label']                       = 'Pangalan:';
$lang['create_user_lname_label']                       = 'Huling pangalan:';
$lang['create_user_company_label']                     = 'Pangalan ng Kumpanya:';
$lang['create_user_identity_label']                    = 'Identity:';
$lang['create_user_email_label']                       = 'Email:';
$lang['create_user_phone_label']                       = 'Telepono:';
$lang['create_user_password_label']                    = 'Password:';
$lang['create_user_password_confirm_label']            = 'kumparahin ang Password:';
$lang['create_user_submit_btn']                        = 'Lumikha User';
$lang['create_user_validation_fname_label']            = 'Pangalan';
$lang['create_user_validation_lname_label']            = 'Huling pangalan';
$lang['create_user_validation_identity_label']         = 'Identity';
$lang['create_user_validation_email_label']            = 'Email Address';
$lang['create_user_validation_phone_label']            = 'Telepono';
$lang['create_user_validation_company_label']          = 'Pangalan ng Kumpanya';
$lang['create_user_validation_password_label']         = 'Password';
$lang['create_user_validation_password_confirm_label'] = 'Password Confirmation';

// Edit User
$lang['edit_user_heading']                           = 'Edit User';
$lang['edit_user_subheading']                        = 'Please enter the user\'s information below.';
$lang['edit_user_fname_label']                       = 'Pangalan:';
$lang['edit_user_lname_label']                       = 'Huling pangalan:';
$lang['edit_user_company_label']                     = 'Pangalan ng Kumpanya:';
$lang['edit_user_email_label']                       = 'Email:';
$lang['edit_user_phone_label']                       = 'Telepono:';
$lang['edit_user_password_label']                    = 'Password: (kung ang pagbabago password)';
$lang['edit_user_password_confirm_label']            = 'Kumpirmahin ang Password: (kung pagbabago ng password)';
$lang['edit_user_groups_heading']                    = 'Miyembro ng grupo';
$lang['edit_user_submit_btn']                        = 'I-save ang User';
$lang['edit_user_validation_fname_label']            = 'Pangalan';
$lang['edit_user_validation_lname_label']            = 'Huling pangalan';
$lang['edit_user_validation_email_label']            = 'Email Address';
$lang['edit_user_validation_phone_label']            = 'Telepono';
$lang['edit_user_validation_company_label']          = 'Pangalan ng Kumpanya';
$lang['edit_user_validation_groups_label']           = 'Grupo';
$lang['edit_user_validation_password_label']         = 'Password';
$lang['edit_user_validation_password_confirm_label'] = 'Password Confirmation';

// Gumawa ng Grupo
$lang['create_group_title']                  = 'Gumawa ng Grupo';
$lang['create_group_heading']                = 'Gumawa ng Grupo';
$lang['create_group_subheading']             = 'Mangyaring ipasok ang impormasyon ng grupo sa ibaba.';
$lang['create_group_name_label']             = 'Pangalan ng Grupo:';
$lang['create_group_desc_label']             = 'Deskripsyon:';
$lang['create_group_submit_btn']             = 'Gumawa ng Grupo';
$lang['create_group_validation_name_label']  = 'Pangalan ng Grupo';
$lang['create_group_validation_desc_label']  = 'Deskripsyon';

// Baguhin ang Grupo
$lang['edit_group_title']                  = 'Baguhin ang Grupo';
$lang['edit_group_saved']                  = 'Group Nai-save na';
$lang['edit_group_heading']                = 'Baguhin ang Grupo';
$lang['edit_group_subheading']             = 'Mangyaring ipasok ang impormasyon ng grupo sa ibaba.';
$lang['edit_group_name_label']             = 'Pangalan ng Grupo:';
$lang['edit_group_desc_label']             = 'Deskripsyon:';
$lang['edit_group_submit_btn']             = 'I-save ang Group';
$lang['edit_group_validation_name_label']  = 'Pangalan ng Grupo';
$lang['edit_group_validation_desc_label']  = 'Deskripsyon';

// Palitan ang Password
$lang['change_password_heading']                               = 'Palitan ang Password';
$lang['change_password_old_password_label']                    = 'Lumang Password:';
$lang['change_password_new_password_label']                    = 'Bagong Password (at least %s characters long):';
$lang['change_password_new_password_confirm_label']            = 'kumparahin ang Bagong Password:';
$lang['change_password_submit_btn']                            = 'Palitan';
$lang['change_password_validation_old_password_label']         = 'Lumang Password';
$lang['change_password_validation_new_password_label']         = 'Bagong Password';
$lang['change_password_validation_new_password_confirm_label'] = 'kumparahin ang Bagong Password';

// Forgot Password
$lang['forgot_password_heading']                 = 'Forgot Password';
$lang['forgot_password_subheading']              = 'Pakipasok ang iyong %s upang maaari naming ipasa sa iyong email ang mensahe upang i-reset ang iyong password.';
$lang['forgot_password_email_label']             = '%s:';
$lang['forgot_password_submit_btn']              = 'Submit';
$lang['forgot_password_validation_email_label']  = 'Email Address';
$lang['forgot_password_identity_label'] = 'Identity';
$lang['forgot_password_email_identity_label']    = 'Email';
$lang['forgot_password_email_not_found']         = 'Walang record ng email address.';
$lang['forgot_password_identity_not_found']         = 'Walang record ng username.';

// Reset Password
$lang['reset_password_heading']                               = 'Palitan ang Password';
$lang['reset_password_new_password_label']                    = 'Bagong Password (at least %s characters long):';
$lang['reset_password_new_password_confirm_label']            = 'kumparahin ang Bagong Password:';
$lang['reset_password_submit_btn']                            = 'Palitan';
$lang['reset_password_validation_new_password_label']         = 'Bagong Password';
$lang['reset_password_validation_new_password_confirm_label'] = 'kumparahin ang Bagong Password';
