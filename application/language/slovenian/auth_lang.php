<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - Slovenian
*
* Author: Žiga Drnovšček
* 		  ziga.drnovscek@gmail.com
*         
*
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  12.5.2013
*
* Description:  Slovenian language file for Ion Auth example views
*
*/

// Napaka
$lang['error_csrf'] = 'Slednji obrazec ni ustrezal našim varnostnim zahtevam.';

// Prijava
$lang['login_heading']         = 'Prijava';
$lang['login_subheading']      = 'Prosimo, spodaj se prijavite z vašim e-naslovom/uporabniškim imenom in geslom';
$lang['login_identity_label']  = 'E-naslov/Uporabniško ime:';
$lang['login_password_label']  = 'Geslo:';
$lang['login_remember_label']  = 'Zapomni si me:';
$lang['login_submit_btn']      = 'Prijava';
$lang['login_forgot_password'] = 'Pozabljeno geslo?';

// Index
$lang['index_heading']           = 'Uporabniki';
$lang['index_subheading']        = 'Spodaj je lista uporabnikov.';
$lang['index_fname_th']          = 'Ime';
$lang['index_lname_th']          = 'Priimek';
$lang['index_email_th']          = 'E-naslov';
$lang['index_groups_th']         = 'Skupine';
$lang['index_status_th']         = 'Status';
$lang['index_action_th']         = 'Akcija';
$lang['index_active_link']       = 'Aktiven';
$lang['index_inactive_link']     = 'Neaktiven';
$lang['index_create_user_link']  = 'Ustvari novega uporabnika';
$lang['index_create_group_link'] = 'Ustvari novo skupino';

// Deaktiviraj uporabnika
$lang['deactivate_heading']                  = 'Deaktiviraj uporabnika';
$lang['deactivate_subheading']               = 'Ali ste prepričani, da želite deaktivirati uporabnika \'%s\'';
$lang['deactivate_confirm_y_label']          = 'Da:';
$lang['deactivate_confirm_n_label']          = 'Ne:';
$lang['deactivate_submit_btn']               = 'Pošlji';
$lang['deactivate_validation_confirm_label'] = 'potrditev';
$lang['deactivate_validation_user_id_label'] = 'uporabniški ID';

// Ustvari uporabnika
$lang['create_user_heading']                           = 'Ustvari uporabnika';
$lang['create_user_subheading']                        = 'Prosimo, vnesite podatke o uporabniku.';
$lang['create_user_fname_label']                       = 'Ime:';
$lang['create_user_lname_label']                       = 'Priimek:';
$lang['create_user_identity_label']                    = 'Identity:';
$lang['create_user_company_label']                     = 'Ime podjetja:';
$lang['create_user_email_label']                       = 'E-naslov:';
$lang['create_user_phone_label']                       = 'Telefon:';
$lang['create_user_password_label']                    = 'Geslo:';
$lang['create_user_password_confirm_label']            = 'Potrdite geslo:';
$lang['create_user_submit_btn']                        = 'Ustvari uporabnika';
$lang['create_user_validation_fname_label']            = 'Ime';
$lang['create_user_validation_lname_label']            = 'Priimek';
$lang['create_user_validation_identity_label']         = 'Identity';
$lang['create_user_validation_email_label']            = 'E-naslov';
$lang['create_user_validation_phone_label']           = 'Telefon';
$lang['create_user_validation_company_label']          = 'Podjetje';
$lang['create_user_validation_password_label']         = 'Geslo';
$lang['create_user_validation_password_confirm_label'] = 'Potrditev gesla';

// Spremeni uporabnika
$lang['edit_user_heading']                           = 'Spremeni uporabnika';
$lang['edit_user_subheading']                        = 'Prosimo, spodaj vnesite podatke o uporabniku.';
$lang['edit_user_fname_label']                       = 'Ime:';
$lang['edit_user_lname_label']                       = 'Priimek:';
$lang['edit_user_company_label']                     = 'Podjetje:';
$lang['edit_user_email_label']                       = 'E-naslov:';
$lang['edit_user_phone_label']                       = 'Telefon:';
$lang['edit_user_password_label']                    = 'Geslo: (če spreminjate geslo)';
$lang['edit_user_password_confirm_label']            = 'Potrdi geslo: (če spreminjate geslo)';
$lang['edit_user_groups_heading']                    = 'Član skupin';
$lang['edit_user_submit_btn']                        = 'Shrani uporabnika';
$lang['edit_user_validation_fname_label']            = 'Ime';
$lang['edit_user_validation_lname_label']            = 'Priimek';
$lang['edit_user_validation_email_label']            = 'E-naslov';
$lang['edit_user_validation_phone_label']            = 'Telefon';
$lang['edit_user_validation_company_label']          = 'Podjetje';
$lang['edit_user_validation_groups_label']           = 'Skupine';
$lang['edit_user_validation_password_label']         = 'Geslo';
$lang['edit_user_validation_password_confirm_label'] = 'Potrditev gesla';

// Ustvari skupino
$lang['create_group_title']                  = 'Ustvari skupino';
$lang['create_group_heading']                = 'Ustvari skupino';
$lang['create_group_subheading']             = 'Prosmo, vnesite podatke o skupini.';
$lang['create_group_name_label']             = 'Ime skupine:';
$lang['create_group_desc_label']             = 'Opis:';
$lang['create_group_submit_btn']             = 'Ustvari skupino';
$lang['create_group_validation_name_label']  = 'Ime skupine';
$lang['create_group_validation_desc_label']  = 'Opis';

// Spremeni skupino
$lang['edit_group_title']                  = 'Spremeni skupino';
$lang['edit_group_saved']                  = 'Skupina shranjena';
$lang['edit_group_heading']                = 'Spremeni skupino';
$lang['edit_group_subheading']             = 'Prosmo, vnesite podatke o skupini.';
$lang['edit_group_name_label']             = 'Ime skupine:';
$lang['edit_group_desc_label']             = 'Opis:';
$lang['edit_group_submit_btn']             = 'Shrani skupino';
$lang['edit_group_validation_name_label']  = 'Ime skupine';
$lang['edit_group_validation_desc_label']  = 'Opis';

// Spremeni geslo
$lang['change_password_heading']                               = 'Spremeni geslo';
$lang['change_password_old_password_label']                    = 'Staro geslo:';
$lang['change_password_new_password_label']                    = 'Novo geslo (vsaj %s znakov dolgo):';
$lang['change_password_new_password_confirm_label']            = 'Potrdi novo geslo:';
$lang['change_password_submit_btn']                            = 'Spremeni';
$lang['change_password_validation_old_password_label']         = 'Staro geslo';
$lang['change_password_validation_new_password_label']         = 'Novo geslo';
$lang['change_password_validation_new_password_confirm_label'] = 'Potrdi novo geslo';

// Pozabljeno geslo
$lang['forgot_password_heading']                 = 'Pozabljeno geslo';
$lang['forgot_password_subheading']              = 'Prosimo vnesite %s, da vam lahko pošljemo e-sporočilo za ponastavitev gesla.';
$lang['forgot_password_email_label']             = '%s:';
$lang['forgot_password_submit_btn']              = 'Pošlji';
$lang['forgot_password_validation_email_label']  = 'Elektronski naslov';
$lang['forgot_password_username_identity_label'] = 'Uporabniško ime';
$lang['forgot_password_email_identity_label']    = 'E-naslov';
$lang['forgot_password_email_not_found']         = 'No record of that email address.';
$lang['forgot_password_identity_not_found']         = 'No record of that username address.';

// Ponastavi geslo
$lang['reset_password_heading']                               = 'Spremeni geslo';
$lang['reset_password_new_password_label']                    = 'Novo geslo (vsaj %s znakov dolgo):';
$lang['reset_password_new_password_confirm_label']            = 'Potrdi novo geslo:';
$lang['reset_password_submit_btn']                            = 'Spremeni';
$lang['reset_password_validation_new_password_label']         = 'Novo geslo';
$lang['reset_password_validation_new_password_confirm_label'] = 'Potrdi novo geslo';

// Aktivacijsko sporočilo
$lang['email_activate_heading']    = 'Aktivirajte računa za %s';
$lang['email_activate_subheading'] = 'Prosimo, sledite povezavi do %s.';
$lang['email_activate_link']       = 'Aktivirajte vaš račun';

// Pozabljeno geslo sporočilo
$lang['email_forgot_password_heading']    = 'Ponastavite geslo za %s';
$lang['email_forgot_password_subheading'] = 'Prosimo, sledite povezavi do %s.';
$lang['email_forgot_password_link']       = 'Ponastavite geslo';

// Novo geslo sporočilo

