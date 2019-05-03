<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - Lithuanian
*
* Translator: Donatas Glodenis
* 		  dgvirtual@akl.lt
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  10.05.2016
*
* Description:  Lithuanian language file for Ion Auth example views
*
*/

// Errors
$lang['error_csrf'] = 'Šis formos įrašas nepraėjo mūsų saugumo patikrų.';

// Login
$lang['login_heading']         = 'Prisijungimas';
$lang['login_subheading']      = 'Prašome prisijungti įrašant el. pašto adresą arba prisijungimo vardą ir slaptažodį.';
$lang['login_identity_label']  = 'El. pašto adresas / vartotojo vardas:';
$lang['login_password_label']  = 'Slaptažodis:';
$lang['login_remember_label']  = 'Atsiminti mane:';
$lang['login_submit_btn']      = 'Prisijungti';
$lang['login_forgot_password'] = 'Pamiršote slaptažodį?';

// Index
$lang['index_heading']           = 'Vartotojai';
$lang['index_subheading']        = 'Žemiau pateikiamas vartotojų sąrašas.';
$lang['index_fname_th']          = 'Vardas';
$lang['index_lname_th']          = 'Pavardė';
$lang['index_email_th']          = 'El. pašto adresas';
$lang['index_groups_th']         = 'Grupės';
$lang['index_status_th']         = 'Būsena';
$lang['index_action_th']         = 'Veiksmas';
$lang['index_active_link']       = 'Aktyvus';
$lang['index_inactive_link']     = 'Neaktyvus';
$lang['index_create_user_link']  = 'Sukurti naują vartotoją';
$lang['index_create_group_link'] = 'Sukurti naują grupę';

// Deactivate User
$lang['deactivate_heading']                  = 'Išjungti vartotoją';
$lang['deactivate_subheading']               = 'Ar tikrai norite išjungti vartotoją \'%s\'';
$lang['deactivate_confirm_y_label']          = 'Taip:';
$lang['deactivate_confirm_n_label']          = 'Ne:';
$lang['deactivate_submit_btn']               = 'Pateikti';
$lang['deactivate_validation_confirm_label'] = 'patvirtinimas';
$lang['deactivate_validation_user_id_label'] = 'vartotojo ID';

// Create User
$lang['create_user_heading']                           = 'Sukurti vartotoją';
$lang['create_user_subheading']                        = 'Prašome įrašyti vartotojo informaciją.';
$lang['create_user_fname_label']                       = 'Vardas:';
$lang['create_user_lname_label']                       = 'Pavardė:';
$lang['create_user_identity_label']                    = 'Tapatybė:';
$lang['create_user_company_label']                     = 'Įmonė:';
$lang['create_user_email_label']                       = 'El. p. adresas:';
$lang['create_user_phone_label']                       = 'Telefonas:';
$lang['create_user_password_label']                    = 'Slaptažodis:';
$lang['create_user_password_confirm_label']            = 'Patvirtinti slaptažodį:';
$lang['create_user_submit_btn']                        = 'Sukurti vartotoją';
$lang['create_user_validation_fname_label']            = 'Vardas';
$lang['create_user_validation_lname_label']            = 'Pavardė';
$lang['create_user_validation_identity_label']         = 'Tapatybė';
$lang['create_user_validation_email_label']            = 'El. p. adresas:';
$lang['create_user_validation_phone1_label']           = 'Pirmoji telefono numerio dalis';
$lang['create_user_validation_phone2_label']           = 'Antroji telefono numerio dalis';
$lang['create_user_validation_phone3_label']           = 'Trečioji telefono numerio dalis';
$lang['create_user_validation_company_label']          = 'Įmonės pavadinimas';
$lang['create_user_validation_password_label']         = 'Slaptažodis';
$lang['create_user_validation_password_confirm_label'] = 'Pakartokite slaptažodį';

// Edit User
$lang['edit_user_heading']                           = 'Taisyti vartotojo duomenis';
$lang['edit_user_subheading']                        = 'Prašome įrašyti vartotojo informaciją.';
$lang['edit_user_fname_label']                       = 'Vardas:';
$lang['edit_user_lname_label']                       = 'Pavardė:';
$lang['edit_user_company_label']                     = 'Įmonės pavadinimas:';
$lang['edit_user_email_label']                       = 'El. p. adresas:';
$lang['edit_user_phone_label']                       = 'Phone:';
$lang['edit_user_password_label']                    = 'Slaptažodis: (if changing password)';
$lang['edit_user_password_confirm_label']            = 'Confirm Slaptažodis: (if changing password)';
$lang['edit_user_groups_heading']                    = 'Member of groups';
$lang['edit_user_submit_btn']                        = 'Save User';
$lang['edit_user_validation_fname_label']            = 'Vardas';
$lang['edit_user_validation_lname_label']            = 'Pavardė';
$lang['edit_user_validation_email_label']            = 'El. p. adresas';
$lang['edit_user_validation_phone1_label']           = 'Pirmoji telefono numerio dalis';
$lang['edit_user_validation_phone2_label']           = 'Antroji telefono numerio dalis';
$lang['edit_user_validation_phone3_label']           = 'Trečioji telefono numerio dalis';
$lang['edit_user_validation_company_label']          = 'Įmonės pavadinimas';
$lang['edit_user_validation_groups_label']           = 'Grupės';
$lang['edit_user_validation_password_label']         = 'Slaptažodis';
$lang['edit_user_validation_password_confirm_label'] = 'Pakartotinai įveskite slaptažodį';

// Create Group
$lang['create_group_title']                  = 'Sukurti grupę';
$lang['create_group_heading']                = 'Sukurti grupę';
$lang['create_group_subheading']             = 'Prašome įrašyti grupės informaciją.';
$lang['create_group_name_label']             = 'Grupės pavadinimas:';
$lang['create_group_desc_label']             = 'Aprašymas:';
$lang['create_group_submit_btn']             = 'Sukurti grupę';
$lang['create_group_validation_name_label']  = 'Grupės pavadinimas';
$lang['create_group_validation_desc_label']  = 'Aprašymas';

// Edit Group
$lang['edit_group_title']                  = 'Keisti grupę';
$lang['edit_group_saved']                  = 'Grupė įrašyta';
$lang['edit_group_heading']                = 'Keisti grupę';
$lang['edit_group_subheading']             = 'Prašome įrašyti grupės informaciją.';
$lang['edit_group_name_label']             = 'Grupės pavadinimas:';
$lang['edit_group_desc_label']             = 'Aprašymas:';
$lang['edit_group_submit_btn']             = 'Įrašyti grupę';
$lang['edit_group_validation_name_label']  = 'Grupės pavadinimas';
$lang['edit_group_validation_desc_label']  = 'Aprašymas';

// Change Slaptažodis
$lang['change_password_heading']                               = 'Pakeisti slaptažodį';
$lang['change_password_old_password_label']                    = 'Senas slaptažodis:';
$lang['change_password_new_password_label']                    = 'Naujas slaptažodis (mažiausiai %s simbolių):';
$lang['change_password_new_password_confirm_label']            = 'Patvirtinti naują slaptažodį:';
$lang['change_password_submit_btn']                            = 'Pakeisti';
$lang['change_password_validation_old_password_label']         = 'Senas slaptažodis';
$lang['change_password_validation_new_password_label']         = 'Naujas slaptažodis';
$lang['change_password_validation_new_password_confirm_label'] = 'Patvirtinti naują slaptažodį';

// Forgot password
$lang['forgot_password_heading']                 = 'Pamiršus slaptažodį';
$lang['forgot_password_subheading']              = 'Prašome įrašyti savo %s kad galėtume išsiųsti Jums el. laišką slaptažodžio atkūrimui.';
$lang['forgot_password_email_label']             = '%s:';
$lang['forgot_password_submit_btn']              = 'Pateikti';
$lang['forgot_password_validation_email_label']  = 'El. p. adresas';
$lang['forgot_password_username_identity_label'] = 'Vartotojo vardas';
$lang['forgot_password_email_identity_label']    = 'El. p. adresas';
$lang['forgot_password_email_not_found']         = 'Duomenų bazėje tokio el. pašto adreso nėra.';
$lang['forgot_password_identity_not_found']         = 'No record of that username address.';

// Reset password
$lang['reset_password_heading']                               = 'Pakeisti slaptažodį';
$lang['reset_password_new_password_label']                    = 'Naujas slaptažodis (mažiausiai %s simboliai/-ių):';
$lang['reset_password_new_password_confirm_label']            = 'Patvirtinti naują slaptažodį:';
$lang['reset_password_submit_btn']                            = 'Keisti';
$lang['reset_password_validation_new_password_label']         = 'Naujas slaptažodis';
$lang['reset_password_validation_new_password_confirm_label'] = 'Patvirtinti naują slaptažodį';

// Activation Email
$lang['email_activate_heading']    = 'Aktyvuoti %s paskyrą';
$lang['email_activate_subheading'] = 'Norėdami %s turite paspausti šią nuorodą.';
$lang['email_activate_link']       = 'Aktyvuoti paskyrą';

// Forgot Slaptažodis Email
$lang['email_forgot_password_heading']    = 'Iš naujo sugeneruoti %s slaptažodį';
$lang['email_forgot_password_subheading'] = 'Norėdami %s turite paspausti šią nuorodą.';
$lang['email_forgot_password_link']       = 'Sugeneruoti slaptažodį iš naujo';

// New Slaptažodis Email

