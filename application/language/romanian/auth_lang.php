<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - Romanian
*
* Author: Adrian Voicu
* 		  avenir.ro@gmail.com
*         @avenirer
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  10.09.2013
*
* Description:  Romanian language file for Ion Auth example views
*
*/

// Errors
$lang['error_csrf'] = 'Acest formular nu a trecut de verificările de securitate.';

// Login
$lang['login_heading']         = 'Conectare';
$lang['login_subheading']      = 'Conectează-te cu email-ul/numele de utilizator și parola.';
$lang['login_identity_label']  = 'Email/Nume utilizator:';
$lang['login_password_label']  = 'Parolă:';
$lang['login_remember_label']  = 'Ține-mă minte:';
$lang['login_submit_btn']      = 'Conectare';
$lang['login_forgot_password'] = 'Ați uitat parola?';

// Index
$lang['index_heading']           = 'Utilizatori';
$lang['index_subheading']        = 'Mai jos găsiți o listă cu utilizatorii.';
$lang['index_fname_th']          = 'Prenume';
$lang['index_lname_th']          = 'Nume';
$lang['index_email_th']          = 'Email';
$lang['index_groups_th']         = 'Grupuri';
$lang['index_status_th']         = 'Status';
$lang['index_action_th']         = 'Acțiune';
$lang['index_active_link']       = 'Activ';
$lang['index_inactive_link']     = 'Inactiv';
$lang['index_create_user_link']  = 'Creează un nou utilizator';
$lang['index_create_group_link'] = 'Creează un nou grup';

// Deactivate User
$lang['deactivate_heading']                  = 'Dezactivează utilizator';
$lang['deactivate_subheading']               = 'Sunteți sigur că vreți să dezactivăm utilizatorul \'%s\'';
$lang['deactivate_confirm_y_label']          = 'Da:';
$lang['deactivate_confirm_n_label']          = 'Nu:';
$lang['deactivate_submit_btn']               = 'Aprobă';
$lang['deactivate_validation_confirm_label'] = 'confirmare';
$lang['deactivate_validation_user_id_label'] = 'ID utilizator';

// Create User
$lang['create_user_heading']                           = 'Creează utilizator';
$lang['create_user_subheading']                        = 'Vă rugăm să introduceți informațiile de mai jos.';
$lang['create_user_fname_label']                       = 'Prenume:';
$lang['create_user_lname_label']                       = 'Nume:';
$lang['create_user_identity_label']                    = 'Identitate:';
$lang['create_user_company_label']                     = 'Companie:';
$lang['create_user_email_label']                       = 'Email:';
$lang['create_user_phone_label']                       = 'Telefon:';
$lang['create_user_password_label']                    = 'Parolă:';
$lang['create_user_password_confirm_label']            = 'Confirmă parola:';
$lang['create_user_submit_btn']                        = 'Creează utilizator';
$lang['create_user_validation_fname_label']            = 'Prenume';
$lang['create_user_validation_lname_label']            = 'Nume';
$lang['create_user_validation_identity_label']         = 'Identitate';
$lang['create_user_validation_email_label']            = 'Adresă email';
$lang['create_user_validation_phone_label']            = 'Telefon';
$lang['create_user_validation_company_label']          = 'Companie';
$lang['create_user_validation_password_label']         = 'Parolă';
$lang['create_user_validation_password_confirm_label'] = 'Confirmarea parolei';

// Edit User
$lang['edit_user_heading']                           = 'Editează utilizatorul';
$lang['edit_user_subheading']                        = 'Vă rugăm să introduceți informațiile utilizatorului de mai jos.';
$lang['edit_user_fname_label']                       = 'Prenume:';
$lang['edit_user_lname_label']                       = 'Nume:';
$lang['edit_user_company_label']                     = 'Companie:';
$lang['edit_user_email_label']                       = 'Email:';
$lang['edit_user_phone_label']                       = 'Telefon:';
$lang['edit_user_password_label']                    = 'Parolă: (dacă schimbați parola)';
$lang['edit_user_password_confirm_label']            = 'Confirmă parola: (dacă schimbați parola)';
$lang['edit_user_groups_heading']                    = 'Membru al grupurilor';
$lang['edit_user_submit_btn']                        = 'Salvează utilizator';
$lang['edit_user_validation_fname_label']            = 'Prenume';
$lang['edit_user_validation_lname_label']            = 'Nume';
$lang['edit_user_validation_email_label']            = 'Adresa email';
$lang['edit_user_validation_phone_label']            = 'Telefon';
$lang['edit_user_validation_company_label']          = 'Companie';
$lang['edit_user_validation_groups_label']           = 'Grupuri';
$lang['edit_user_validation_password_label']         = 'Parolă';
$lang['edit_user_validation_password_confirm_label'] = 'Confirmarea parolei';

// Create Group
$lang['create_group_title']                  = 'Creează grup';
$lang['create_group_heading']                = 'Creează grup';
$lang['create_group_subheading']             = 'Vă rugăm să introduceți informațiile grupului mai jos.';
$lang['create_group_name_label']             = 'Numele grupului:';
$lang['create_group_desc_label']             = 'Descriere:';
$lang['create_group_submit_btn']             = 'Creează grupul';
$lang['create_group_validation_name_label']  = 'Numele grupului';
$lang['create_group_validation_desc_label']  = 'Descriere';

// Edit Group
$lang['edit_group_title']                  = 'Editează datele grupului';
$lang['edit_group_saved']                  = 'Grup salvat';
$lang['edit_group_heading']                = 'Editează grupul';
$lang['edit_group_subheading']             = 'Vă rugăm să introduceți informațiile grupului mai jos.';
$lang['edit_group_name_label']             = 'Numele grupului:';
$lang['edit_group_desc_label']             = 'Descriere:';
$lang['edit_group_submit_btn']             = 'Salvează grupul';
$lang['edit_group_validation_name_label']  = 'Numele grupului';
$lang['edit_group_validation_desc_label']  = 'Descriere';

// Change Password
$lang['change_password_heading']                               = 'Schimbă parola';
$lang['change_password_old_password_label']                    = 'Parola veche:';
$lang['change_password_new_password_label']                    = 'Noua parolă (cel puțin %s caractere):';
$lang['change_password_new_password_confirm_label']            = 'Confirmă noua parolă:';
$lang['change_password_submit_btn']                            = 'Schimbă';
$lang['change_password_validation_old_password_label']         = 'Parola veche';
$lang['change_password_validation_new_password_label']         = 'Parola nouă';
$lang['change_password_validation_new_password_confirm_label'] = 'Confirmă noua parola';

// Forgot Password
$lang['forgot_password_heading']                 = 'Parolă uitată';
$lang['forgot_password_subheading']              = 'Vă rugăm să introduceți %s pentru a vă putea trimite un email de resetare a parolei.';
$lang['forgot_password_email_label']             = '%s:';
$lang['forgot_password_submit_btn']              = 'Trimite';
$lang['forgot_password_validation_email_label']  = 'Adresa de email';
$lang['forgot_password_username_identity_label'] = 'Utilizator';
$lang['forgot_password_email_identity_label']    = 'Email';
$lang['forgot_password_email_not_found']         = 'Nu există nicio înregistrare cu acest email.';
$lang['forgot_password_identity_not_found']         = 'No record of that username address.';

// Reset Password
$lang['reset_password_heading']                               = 'Schimbare parolă';
$lang['reset_password_new_password_label']                    = 'Parola nouă (cel puțin %s caractere):';
$lang['reset_password_new_password_confirm_label']            = 'Confirmă noua parolă:';
$lang['reset_password_submit_btn']                            = 'Schimbă';
$lang['reset_password_validation_new_password_label']         = 'Parola nouă';
$lang['reset_password_validation_new_password_confirm_label'] = 'Confirmă noua parolă';
