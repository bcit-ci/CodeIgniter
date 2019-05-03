<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - English
*
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
*
* Author: Daniel Davis
*         @ourmaninjapan
*
* Translation: primjeri
*		info@primjeri.com
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.09.2013
*
* Description:  Croatian language file for Ion Auth example views
*
*/

// Errors
$lang['error_csrf'] = 'Obrazac ne prolazi provjere.';

// Login
$lang['login_heading']         = 'Prijava';
$lang['login_subheading']      = 'Prijavite se koristeći Vaš email/korisničko ime i lozinku.';
$lang['login_identity_label']  = 'Email/Korisničko ime:';
$lang['login_password_label']  = 'Lozinka:';
$lang['login_remember_label']  = 'Zapamti me:';
$lang['login_submit_btn']      = 'Prijava';
$lang['login_forgot_password'] = 'Zaboravili ste lozinku?';

// Index
$lang['index_heading']           = 'Korisnici';
$lang['index_subheading']        = 'Lista korisnika.';
$lang['index_fname_th']          = 'Ime';
$lang['index_lname_th']          = 'Prezime';
$lang['index_email_th']          = 'Email';
$lang['index_groups_th']         = 'Grupa';
$lang['index_status_th']         = 'Status';
$lang['index_action_th']         = 'Akcija';
$lang['index_active_link']       = 'Aktivan';
$lang['index_inactive_link']     = 'Neaktivan';
$lang['index_create_user_link']  = 'Kreiraj novog korisnika';
$lang['index_create_group_link'] = 'Kreiraj novu grupu';

// Deactivate User
$lang['deactivate_heading']                  = 'Deaktiviraj korisnika';
$lang['deactivate_subheading']               = 'Da li ste sigurni da želite deaktivirati korisnika \'%s\'';
$lang['deactivate_confirm_y_label']          = 'Da:';
$lang['deactivate_confirm_n_label']          = 'Ne:';
$lang['deactivate_submit_btn']               = 'Pošalji';
$lang['deactivate_validation_confirm_label'] = 'potvrda';
$lang['deactivate_validation_user_id_label'] = 'korisnički ID';

// Create User
$lang['create_user_heading']                           = 'Kreiraj korisnika';
$lang['create_user_subheading']                        = 'Ispuni podatke o korisniku ispod.';
$lang['create_user_fname_label']                       = 'Ime:';
$lang['create_user_lname_label']                       = 'Prezime:';
$lang['create_user_identity_label']                    = 'Identity:';
$lang['create_user_company_label']                     = 'Kompanija:';
$lang['create_user_email_label']                       = 'Email:';
$lang['create_user_phone_label']                       = 'Telefon:';
$lang['create_user_password_label']                    = 'Lozinka:';
$lang['create_user_password_confirm_label']            = 'Potvrda lozinke:';
$lang['create_user_submit_btn']                        = 'Kreiraj korisnika';
$lang['create_user_validation_fname_label']            = 'Ime';
$lang['create_user_validation_lname_label']            = 'Prezime';
$lang['create_user_validation_identity_label']         = 'Identity';
$lang['create_user_validation_email_label']            = 'Email';
$lang['create_user_validation_phone_label']            = 'Telefon';
$lang['create_user_validation_company_label']          = 'Kompanija';
$lang['create_user_validation_password_label']         = 'Lozinka';
$lang['create_user_validation_password_confirm_label'] = 'Potvrda lozine';

// Edit User
$lang['edit_user_heading']                           = 'Ažuriraj korisnika';
$lang['edit_user_subheading']                        = 'Ispuni podatke o korisniku ispod.';
$lang['edit_user_fname_label']                       = 'Ime:';
$lang['edit_user_lname_label']                       = 'Prezime:';
$lang['edit_user_company_label']                     = 'Kompanija:';
$lang['edit_user_email_label']                       = 'Email:';
$lang['edit_user_phone_label']                       = 'Telefon:';
$lang['edit_user_password_label']                    = 'Lozinka: (za novu lozinku)';
$lang['edit_user_password_confirm_label']            = 'Potvrda lozinke: (za novu lozinku)';
$lang['edit_user_groups_heading']                    = 'Član grupa';
$lang['edit_user_submit_btn']                        = 'Spremi korisnika';
$lang['edit_user_validation_fname_label']            = 'Ime';
$lang['edit_user_validation_lname_label']            = 'Prezime';
$lang['edit_user_validation_email_label']            = 'Email';
$lang['edit_user_validation_phone_label']            = 'Telefon';
$lang['edit_user_validation_company_label']          = 'Kompanija';
$lang['edit_user_validation_groups_label']           = 'Grupa';
$lang['edit_user_validation_password_label']         = 'Lozinka';
$lang['edit_user_validation_password_confirm_label'] = 'Potvrda lozinke';

// Create Group
$lang['create_group_title']                  = 'Kreiraj grupu';
$lang['create_group_heading']                = 'Kreiraj grupu';
$lang['create_group_subheading']             = 'Upišite podatke o grupi ispod.';
$lang['create_group_name_label']             = 'Naziv:';
$lang['create_group_desc_label']             = 'Opis:';
$lang['create_group_submit_btn']             = 'Kreiraj grupu';
$lang['create_group_validation_name_label']  = 'Naziv';
$lang['create_group_validation_desc_label']  = 'Opis';

// Edit Group
$lang['edit_group_title']                  = 'Ažuriraj grupu';
$lang['edit_group_saved']                  = 'Grupa spremljena';
$lang['edit_group_heading']                = 'Ažuriraj grupu';
$lang['edit_group_subheading']             = 'Upišite podatke o grupi ispod.';
$lang['edit_group_name_label']             = 'Naziv:';
$lang['edit_group_desc_label']             = 'Opis:';
$lang['edit_group_submit_btn']             = 'Spremi grupu';
$lang['edit_group_validation_name_label']  = 'Naziv';
$lang['edit_group_validation_desc_label']  = 'Opis';

// Change Password
$lang['change_password_heading']                               = 'Promjeni lozinku';
$lang['change_password_old_password_label']                    = 'Stara lozinka:';
$lang['change_password_new_password_label']                    = 'Nova lozinka (najmanje %s znakova):';
$lang['change_password_new_password_confirm_label']            = 'Potvrda nove lozinke:';
$lang['change_password_submit_btn']                            = 'Promjeni';
$lang['change_password_validation_old_password_label']         = 'Stara lozinka';
$lang['change_password_validation_new_password_label']         = 'Nova lozinka';
$lang['change_password_validation_new_password_confirm_label'] = 'Potvrda nove lozinke';

// Forgot Password
$lang['forgot_password_heading']                 = 'Zaboravljena lozinka';
$lang['forgot_password_subheading']              = 'Upišite %s nakon čega ćete dobiti email za poništavanje Vaše lozinke.';
$lang['forgot_password_email_label']             = '%s:';
$lang['forgot_password_submit_btn']              = 'Pošalji';
$lang['forgot_password_validation_email_label']  = 'Email';
$lang['forgot_password_username_identity_label'] = 'Korisničko ime';
$lang['forgot_password_email_identity_label']    = 'Email';
$lang['forgot_password_email_not_found']         = 'No record of that email address.';
$lang['forgot_password_identity_not_found']         = 'No record of that username address.';

// Reset Password
$lang['reset_password_heading']                               = 'Promjena lozinke';
$lang['reset_password_new_password_label']                    = 'Nova lozinka (najmanje %s znakova):';
$lang['reset_password_new_password_confirm_label']            = 'Potvrdi novu lozinku:';
$lang['reset_password_submit_btn']                            = 'Promjeni';
$lang['reset_password_validation_new_password_label']         = 'Nova lozinka';
$lang['reset_password_validation_new_password_confirm_label'] = 'Potvrdi novu lozinku';

// Activation Email
$lang['email_activate_heading']    = 'Aktivirajte račun za %s';
$lang['email_activate_subheading'] = 'Kliknite sljedeći link %s.';
$lang['email_activate_link']       = 'Aktivirajte Vaš račun';

// Forgot Password Email
$lang['email_forgot_password_heading']    = 'Poništi lozinku za %s';
$lang['email_forgot_password_subheading'] = 'Klikni ovaj link za %s.';
$lang['email_forgot_password_link']       = 'Poništi lozinku';


