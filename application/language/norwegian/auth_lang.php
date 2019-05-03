<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name: Auth Lang - Norwegian
*
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
*
* Author: Daniel Davis
*         @ourmaninjapan
*
* Author: Yngve Høiseth
* 		  yngve.hoiseth@gmail.com
*         @yhoiseth
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:   03.09.2013
* Last-Edit: 16.11.2014
*
* Description: Norwegian language file for Ion Auth example views
*
*/

// Errors
$lang['error_csrf'] = 'Dette skjemaet ble stoppet i sikkerhetskontrollen vår.';

// Login
$lang['login_heading']         = 'Logg inn';
$lang['login_subheading']      = 'Vennligst logg inn med din email/brukernavn og passord nedenfor.';
$lang['login_identity_label']  = 'Email/brukernavn:';
$lang['login_password_label']  = 'Passord:';
$lang['login_remember_label']  = 'Husk meg:';
$lang['login_submit_btn']      = 'Logg inn';
$lang['login_forgot_password'] = 'Glemt passordet?';

// Index
$lang['index_heading']           = 'Brukere';
$lang['index_subheading']        = 'Nedenfor er en liste over brukerne.';
$lang['index_fname_th']          = 'Fornavn';
$lang['index_lname_th']          = 'Etternavn';
$lang['index_email_th']          = 'Email';
$lang['index_groups_th']         = 'Grupper';
$lang['index_status_th']         = 'Status';
$lang['index_action_th']         = 'Handling';
$lang['index_active_link']       = 'Aktive';
$lang['index_inactive_link']     = 'Inaktiv';
$lang['index_create_user_link']  = 'Lag ny bruker';
$lang['index_create_group_link'] = 'Lag ny gruppe';

// Deactivate User
$lang['deactivate_heading']                  = 'Deaktivér bruker';
$lang['deactivate_subheading']               = 'Er du sikker på at du vil deaktivere brukeren \'%s\'';
$lang['deactivate_confirm_y_label']          = 'Ja:';
$lang['deactivate_confirm_n_label']          = 'Nei:';
$lang['deactivate_submit_btn']               = 'Fullfør';
$lang['deactivate_validation_confirm_label'] = 'bekreftelse';
$lang['deactivate_validation_user_id_label'] = 'bruker-ID';

// Create User
$lang['create_user_heading']                           = 'Lag ny bruker';
$lang['create_user_subheading']                        = 'Legg inn informasjon om brukeren nedenfor.';
$lang['create_user_fname_label']                       = 'Fornavn:';
$lang['create_user_lname_label']                       = 'Etternavn:';
$lang['create_user_company_label']                     = 'Firmanavn:';
$lang['create_user_identity_label']                    = 'Identity:';
$lang['create_user_email_label']                       = 'Email:';
$lang['create_user_phone_label']                       = 'Telefon:';
$lang['create_user_password_label']                    = 'Passord:';
$lang['create_user_password_confirm_label']            = 'Bekreft passord:';
$lang['create_user_submit_btn']                        = 'Lag ny bruker';
$lang['create_user_validation_fname_label']            = 'Fornavn';
$lang['create_user_validation_lname_label']            = 'Etternavn';
$lang['create_user_validation_identity_label']         = 'Identity';
$lang['create_user_validation_email_label']            = 'Email';
$lang['create_user_validation_phone1_label']           = 'Første del av telefonnummer';
$lang['create_user_validation_phone2_label']           = 'Andre del av telefonnummer';
$lang['create_user_validation_phone3_label']           = 'Tredje del av telefonnummer';
$lang['create_user_validation_company_label']          = 'Firmanavn';
$lang['create_user_validation_password_label']         = 'Passord';
$lang['create_user_validation_password_confirm_label'] = 'Bekreftelse av passord';

// Edit User
$lang['edit_user_heading']                           = 'Redigér bruker';
$lang['edit_user_subheading']                        = 'Vennligst legg inn informasjon om brukeren nedenfor.';
$lang['edit_user_fname_label']                       = 'Fornavn:';
$lang['edit_user_lname_label']                       = 'Etternavn:';
$lang['edit_user_company_label']                     = 'Firmanavn:';
$lang['edit_user_email_label']                       = 'Email:';
$lang['edit_user_phone_label']                       = 'Telefon:';
$lang['edit_user_password_label']                    = 'Passord: (hvis passordet skal endres)';
$lang['edit_user_password_confirm_label']            = 'Bekreft passord: (hvis passordet skal endres)';
$lang['edit_user_groups_heading']                    = 'Medlem av grupper';
$lang['edit_user_submit_btn']                        = 'Lagre bruker';
$lang['edit_user_validation_fname_label']            = 'Fornavn';
$lang['edit_user_validation_lname_label']            = 'Etternavn';
$lang['edit_user_validation_email_label']            = 'Email';
$lang['edit_user_validation_phone1_label']           = 'Første del av telefonnummer';
$lang['edit_user_validation_phone2_label']           = 'Andre del av telefonnummer';
$lang['edit_user_validation_phone3_label']           = 'Tredje del av telefonnummer';
$lang['edit_user_validation_company_label']          = 'Firmanavn';
$lang['edit_user_validation_groups_label']           = 'Grupper';
$lang['edit_user_validation_password_label']         = 'Passord';
$lang['edit_user_validation_password_confirm_label'] = 'Bekreftelse av passord';

// Create Group
$lang['create_group_title']                 = 'Lag gruppe';
$lang['create_group_heading']               = 'Lag gruppe';
$lang['create_group_subheading']            = 'Legg inn informasjon om gruppen nedenfor.';
$lang['create_group_name_label']            = 'Gruppenavn:';
$lang['create_group_desc_label']            = 'Beskrivelse:';
$lang['create_group_submit_btn']            = 'Lag gruppe';
$lang['create_group_validation_name_label'] = 'Gruppenavn';
$lang['create_group_validation_desc_label'] = 'Beskrivelse';

// Edit Group
$lang['edit_group_title']                 = 'Redigér gruppe';
$lang['edit_group_saved']                 = 'Gruppe lagret';
$lang['edit_group_heading']               = 'Redigér gruppe';
$lang['edit_group_subheading']            = 'Legg inn informasjon om gruppen nedenfor.';
$lang['edit_group_name_label']            = 'Gruppenavn:';
$lang['edit_group_desc_label']            = 'Beskrivelse:';
$lang['edit_group_submit_btn']            = 'Lagre gruppe';
$lang['edit_group_validation_name_label'] = 'Gruppenavn';
$lang['edit_group_validation_desc_label'] = 'Beskrivelse';

// Change Password
$lang['change_password_heading']                               = 'Endre passord';
$lang['change_password_old_password_label']                    = 'Gammelt passord:';
$lang['change_password_new_password_label']                    = 'Nytt passord (minst %s tegn):';
$lang['change_password_new_password_confirm_label']            = 'Bekreft nytt passord:';
$lang['change_password_submit_btn']                            = 'Endre';
$lang['change_password_validation_old_password_label']         = 'Gammelt passord';
$lang['change_password_validation_new_password_label']         = 'Nytt passord';
$lang['change_password_validation_new_password_confirm_label'] = 'Bekreft nytt passord';

// Forgot Password
$lang['forgot_password_heading']                 = 'Glemt passord';
$lang['forgot_password_subheading']              = 'Legg inn din %s så vi kan sende deg en email for å tilbakestille passordet.';
$lang['forgot_password_email_label']             = '%s:';
$lang['forgot_password_submit_btn']              = 'Send inn';
$lang['forgot_password_validation_email_label']  = 'Email';
$lang['forgot_password_username_identity_label'] = 'Brukernavn';
$lang['forgot_password_email_identity_label']    = 'Email';
$lang['forgot_password_email_not_found']         = 'Vi fant ikke emailen du oppgav.';
$lang['forgot_password_identity_not_found']         = 'No record of that username address.';

// Reset Password
$lang['reset_password_heading']                               = 'Endre passord';
$lang['reset_password_new_password_label']                    = 'Nytt passord (minst %s tegn):';
$lang['reset_password_new_password_confirm_label']            = 'Bekreft nytt passord:';
$lang['reset_password_submit_btn']                            = 'Endre';
$lang['reset_password_validation_new_password_label']         = 'Nytt passord';
$lang['reset_password_validation_new_password_confirm_label'] = 'Bekreft nytt passord';

// Activation Email
$lang['email_activate_heading']    = 'Aktivér konto for %s';
$lang['email_activate_subheading'] = 'Klikk denne linken for å %s.';
$lang['email_activate_link']       = 'Aktivér din konto';

// Forgot Password Email
$lang['email_forgot_password_heading']    = 'Tilbakestill passord for %s';
$lang['email_forgot_password_subheading'] = 'Klikk denne linken for å %s.';
$lang['email_forgot_password_link']       = 'Tilbakestill passordet ditt';

