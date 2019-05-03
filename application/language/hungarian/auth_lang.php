<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - Hungarian
*
* Author: Balazs Bosternak
* 	b.bosternak@gmail.com
*         @bosternakbalazs

*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  07.19.2015
*
* Description:  English language file for Ion Auth example views
*
*/

// Errors
$lang['error_csrf'] = 'This form post did not pass our security checks.';

// Login
$lang['login_heading']         = 'Bejelentkezés';
$lang['login_subheading']      = 'Az alábbi űrlapon jelentkezzen be e-mail címével/felhasználónevével és jelszavával.';
$lang['login_identity_label']  = 'E-mail/Felhasználónév:';
$lang['login_password_label']  = 'Jelszó:';
$lang['login_remember_label']  = 'Emlékezz rám:';
$lang['login_submit_btn']      = 'Belépés';
$lang['login_forgot_password'] = 'Elfelejtette jelszavát?';

// Index
$lang['index_heading']           = 'Felhasználók';
$lang['index_subheading']        = 'Lejjebb található a felhasználók listája.';
$lang['index_fname_th']          = 'Keresztnév';
$lang['index_lname_th']          = 'Vezetéknév';
$lang['index_email_th']          = 'E-mail';
$lang['index_groups_th']         = 'Csoportok';
$lang['index_status_th']         = 'Státusz';
$lang['index_action_th']         = 'Operáció';
$lang['index_active_link']       = 'Aktív';
$lang['index_inactive_link']     = 'Inaktív';
$lang['index_create_user_link']  = 'Új felhasználó létrehozása';
$lang['index_create_group_link'] = 'Új csoport létrehozása';

// Deactivate User
$lang['deactivate_heading']                  = 'Felhasználó deaktiválása';
$lang['deactivate_subheading']               = 'Biztos benne hogy deaktiválni akarja a felhasználót? \'%s\'';
$lang['deactivate_confirm_y_label']          = 'Igen:';
$lang['deactivate_confirm_n_label']          = 'Nem:';
$lang['deactivate_submit_btn']               = 'Elküld';
$lang['deactivate_validation_confirm_label'] = 'visszaigazolás';
$lang['deactivate_validation_user_id_label'] = 'felhasználói ID';

// Create User
$lang['create_user_heading']                           = 'Felhasználó létrehozása';
$lang['create_user_subheading']                        = 'Kérem adja meg a felhasználó adatait az alábbi űrlapon.';
$lang['create_user_fname_label']                       = 'Keresztnév:';
$lang['create_user_lname_label']                       = 'Vezetéknév:';
$lang['create_user_identity_label']                    = 'Felhasználónév:';
$lang['create_user_company_label']                     = 'Cég neve:';
$lang['create_user_email_label']                       = 'E-mail:';
$lang['create_user_phone_label']                       = 'Telefonszám:';
$lang['create_user_password_label']                    = 'Jelszó:';
$lang['create_user_password_confirm_label']            = 'Jelszó megerősítése:';
$lang['create_user_submit_btn']                        = 'Felhasználó létrehozása';
$lang['create_user_validation_fname_label']            = 'Keresztnév';
$lang['create_user_validation_lname_label']            = 'Vezetéknév';
$lang['create_user_validation_identity_label']         = 'Felhasználónév';
$lang['create_user_validation_email_label']            = 'E-mail cím';
$lang['create_user_validation_phone_label']            = 'Telefonszám';
$lang['create_user_validation_company_label']          = 'Cég neve';
$lang['create_user_validation_password_label']         = 'Jelszó';
$lang['create_user_validation_password_confirm_label'] = 'Jelszó megerősítése';

// Edit User
$lang['edit_user_heading']                           = 'Felhasználó szerkesztése';
$lang['edit_user_subheading']                        = 'Kérem adja meg a felhasználó adatait az alábbi űrlapon.';
$lang['edit_user_fname_label']                       = 'Keresztnév:';
$lang['edit_user_lname_label']                       = 'Vezetéknév:';
$lang['edit_user_company_label']                     = 'Cég neve:';
$lang['edit_user_email_label']                       = 'E-mail:';
$lang['edit_user_phone_label']                       = 'Telefonszám:';
$lang['edit_user_password_label']                    = 'Jelszó: (ha változik)';
$lang['edit_user_password_confirm_label']            = 'Jelszó megerősítése: (ha változik)';
$lang['edit_user_groups_heading']                    = 'Csoportok';
$lang['edit_user_submit_btn']                        = 'Felhasználó mentése';
$lang['edit_user_validation_fname_label']            = 'Keresztnév';
$lang['edit_user_validation_lname_label']            = 'Vezetéknév';
$lang['edit_user_validation_email_label']            = 'E-mail cím';
$lang['edit_user_validation_phone_label']            = 'Telefonszám';
$lang['edit_user_validation_company_label']          = 'Cég neve';
$lang['edit_user_validation_groups_label']           = 'Csoportok';
$lang['edit_user_validation_password_label']         = 'Jelszó';
$lang['edit_user_validation_password_confirm_label'] = 'Jelszó megerősítése';

// Create Group
$lang['create_group_title']                  = 'Csoport létrehozása';
$lang['create_group_heading']                = 'Csoport létrehozása';
$lang['create_group_subheading']             = 'Kérem adja meg a csoport adatait az alábbi űrlapon.';
$lang['create_group_name_label']             = 'Csoport neve:';
$lang['create_group_desc_label']             = 'Leírás:';
$lang['create_group_submit_btn']             = 'Csoport létrehozása';
$lang['create_group_validation_name_label']  = 'Csoport neve';
$lang['create_group_validation_desc_label']  = 'Leírás';

// Edit Group
$lang['edit_group_title']                  = 'Csoport szerkesztése';
$lang['edit_group_saved']                  = 'Csoport mentve';
$lang['edit_group_heading']                = 'Csoport szerkesztése';
$lang['edit_group_subheading']             = 'Kérem adja meg a csoport adatait az alábbi űrlapon.';
$lang['edit_group_name_label']             = 'Csoport neve:';
$lang['edit_group_desc_label']             = 'Leírás:';
$lang['edit_group_submit_btn']             = 'Csoport mentése';
$lang['edit_group_validation_name_label']  = 'Csoport neve';
$lang['edit_group_validation_desc_label']  = 'Leírás';

// Change Password
$lang['change_password_heading']                               = 'Jelszó változtatása';
$lang['change_password_old_password_label']                    = 'Régi jelszó:';
$lang['change_password_new_password_label']                    = 'Új jelszó (legalább %s karakter hosszúságú):';
$lang['change_password_new_password_confirm_label']            = 'Új jelszó megerősítése:';
$lang['change_password_submit_btn']                            = 'Változtat';
$lang['change_password_validation_old_password_label']         = 'Régi jelszó';
$lang['change_password_validation_new_password_label']         = 'Új jelszó';
$lang['change_password_validation_new_password_confirm_label'] = 'Új jelszó megerősítése';

// Forgot Password
$lang['forgot_password_heading']                 = 'Elfelejtett jelszó';
$lang['forgot_password_subheading']              = 'Kérem adja meg a(z) %sét, hogy egy e-mailt küldhessünk a jelszó beállítására.';
$lang['forgot_password_email_label']             = '%s:';
$lang['forgot_password_submit_btn']              = 'Elküld';
$lang['forgot_password_validation_email_label']  = 'E-mail cím';
$lang['forgot_password_username_identity_label'] = 'Felhasználónév';
$lang['forgot_password_email_identity_label']    = 'E-mail';
$lang['forgot_password_email_not_found']         = 'Nem található ez az e-mail cím.';
$lang['forgot_password_identity_not_found']         = 'No record of that username address.';

// Reset Password
$lang['reset_password_heading']                               = 'Jelszó változtatása';
$lang['reset_password_new_password_label']                    = 'Új jelszó (legalább %s karakter hosszúságú):';
$lang['reset_password_new_password_confirm_label']            = 'Új jelszó megerősítése:';
$lang['reset_password_submit_btn']                            = 'Változtat';
$lang['reset_password_validation_new_password_label']         = 'Új jelszó';
$lang['reset_password_validation_new_password_confirm_label'] = 'Új jelszó megerősítése';
