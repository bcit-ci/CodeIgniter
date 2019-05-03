<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - Italian
*
* Author: Ben Edmunds
*         ben.edmunds@gmail.com
*         @benedmunds
*
* Author: Daniel Davis
*         @ourmaninjapan
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.09.2013
*
* Description:  Italian language file for Ion Auth example views
*
*/

// Errors
$lang['error_csrf'] = 'Il form non ha superato i controlli di sicurezza';

// Login
$lang['login_heading']         = 'Accedi';
$lang['login_subheading']      = 'Si prega di accedere tramite email/username e password';
$lang['login_identity_label']  = 'Email/Username:';
$lang['login_password_label']  = 'Password:';
$lang['login_remember_label']  = 'Ricordami:';
$lang['login_submit_btn']      = 'Accedi';
$lang['login_forgot_password'] = 'Dimenticata la password?';

// Index
$lang['index_heading']           = 'Utenti';
$lang['index_subheading']        = 'Qua sotto troverete una lista degli utenti.';
$lang['index_fname_th']          = 'Nome';
$lang['index_lname_th']          = 'Cognome';
$lang['index_email_th']          = 'Email';
$lang['index_groups_th']         = 'Gruppi';
$lang['index_status_th']         = 'Stato';
$lang['index_action_th']         = 'Azione';
$lang['index_active_link']       = 'Attivo';
$lang['index_inactive_link']     = 'Disattivo';
$lang['index_create_user_link']  = 'Crea un nuovo utente';
$lang['index_create_group_link'] = 'Crea un nuovo gruppo';

// Deactivate User
$lang['deactivate_heading']                  = 'Disattiva Utente';
$lang['deactivate_subheading']               = 'Sei sicuro di voler attivare l\'utente \'%s\'';
$lang['deactivate_confirm_y_label']          = 'S&igrave;:';
$lang['deactivate_confirm_n_label']          = 'No:';
$lang['deactivate_submit_btn']               = 'Invia';
$lang['deactivate_validation_confirm_label'] = 'conferma';
$lang['deactivate_validation_user_id_label'] = 'ID utente';

// Create User
$lang['create_user_heading']                           = 'Crea l\'utente';
$lang['create_user_subheading']                        = 'Inserisci le informazioni sull\'utente qua sotto.';
$lang['create_user_fname_label']                       = 'Nome:';
$lang['create_user_lname_label']                       = 'Cognome:';
$lang['create_user_identity_label']                    = 'Identity:';
$lang['create_user_company_label']                     = 'Azienda:';
$lang['create_user_email_label']                       = 'Email:';
$lang['create_user_phone_label']                       = 'Telefono:';
$lang['create_user_password_label']                    = 'Password:';
$lang['create_user_password_confirm_label']            = 'Conferma Password:';
$lang['create_user_submit_btn']                        = 'Crea Utente';
$lang['create_user_validation_fname_label']            = 'Nome';
$lang['create_user_validation_lname_label']            = 'Cognome';
$lang['create_user_validation_identity_label']         = 'Identity';
$lang['create_user_validation_email_label']            = 'Indirizzo Email';
$lang['create_user_validation_phone1_label']           = 'Prima parte del Telefono';
$lang['create_user_validation_phone2_label']           = 'Seconda parte del Telefono';
$lang['create_user_validation_phone3_label']           = 'Terza parte del Telefono';
$lang['create_user_validation_company_label']          = 'Azienda';
$lang['create_user_validation_password_label']         = 'Password';
$lang['create_user_validation_password_confirm_label'] = 'Conferma Password';

// Edit User
$lang['edit_user_heading']                           = 'Modifica Utente';
$lang['edit_user_subheading']                        = 'Inserisci le informazioni sull\'utente qua sotto.';
$lang['edit_user_fname_label']                       = 'Nome:';
$lang['edit_user_lname_label']                       = 'Cognome:';
$lang['edit_user_company_label']                     = 'Azienda:';
$lang['edit_user_email_label']                       = 'Email:';
$lang['edit_user_phone_label']                       = 'Telefono:';
$lang['edit_user_password_label']                    = 'Password: (se la si vuole cambiare)';
$lang['edit_user_password_confirm_label']            = 'Conferma Password: (se la si vuole cambiare)';
$lang['edit_user_groups_heading']                    = 'Appartenente ai gruppi';
$lang['edit_user_submit_btn']                        = 'Salva Utente';
$lang['edit_user_validation_fname_label']            = 'Nome';
$lang['edit_user_validation_lname_label']            = 'Cognome';
$lang['edit_user_validation_email_label']            = 'Indirizzo Email';
$lang['edit_user_validation_phone1_label']           = 'Prima parte del Telefono';
$lang['edit_user_validation_phone2_label']           = 'Seconda parte del Telefono';
$lang['edit_user_validation_phone3_label']           = 'Terza parte del Telefono';
$lang['edit_user_validation_company_label']          = 'Azienda';
$lang['edit_user_validation_groups_label']           = 'Gruppi';
$lang['edit_user_validation_password_label']         = 'Password';
$lang['edit_user_validation_password_confirm_label'] = 'Conferma Password';

// Create Group
$lang['create_group_title']                  = 'Crea Gruppo';
$lang['create_group_heading']                = 'Crea Gruppo';
$lang['create_group_subheading']             = 'Inserisci le informazioni del gruppo qua sotto.';
$lang['create_group_name_label']             = 'Nome Gruppo:';
$lang['create_group_desc_label']             = 'Descrizione:';
$lang['create_group_submit_btn']             = 'Crea Gruppo';
$lang['create_group_validation_name_label']  = 'Nome Gruppo';
$lang['create_group_validation_desc_label']  = 'Descrizione';

// Edit Group
$lang['edit_group_title']                  = 'Modifica Gruppo';
$lang['edit_group_saved']                  = 'Gruppo Salvato';
$lang['edit_group_heading']                = 'Modifica Gruppo';
$lang['edit_group_subheading']             = 'Inserisci le informazioni del gruppo qua sotto.';
$lang['edit_group_name_label']             = 'Nome Gruppo:';
$lang['edit_group_desc_label']             = 'Descrizione:';
$lang['edit_group_submit_btn']             = 'Salva Gruppo';
$lang['edit_group_validation_name_label']  = 'Nome Gruppo';
$lang['edit_group_validation_desc_label']  = 'Descrizione';

// Change Password
$lang['change_password_heading']                               = 'Cambia Password';
$lang['change_password_old_password_label']                    = 'Vecchia Password:';
$lang['change_password_new_password_label']                    = 'Nuova Password (lunga almeno %s caratteri):';
$lang['change_password_new_password_confirm_label']            = 'Conferma Nuova Password:';
$lang['change_password_submit_btn']                            = 'Cambia';
$lang['change_password_validation_old_password_label']         = 'Vecchia Password';
$lang['change_password_validation_new_password_label']         = 'Nuova Password';
$lang['change_password_validation_new_password_confirm_label'] = 'Conferma Nuova Password';

// Forgot Password
$lang['forgot_password_heading']                 = 'Password Dimenticata';
$lang['forgot_password_subheading']              = 'Inserire la propria %s, vi verr&agrave; inviata una email per reimpostare la vostra password.';
$lang['forgot_password_email_label']             = '%s:';
$lang['forgot_password_submit_btn']              = 'Invia';
$lang['forgot_password_validation_email_label']  = 'Indirizzo Email';
$lang['forgot_password_username_identity_label'] = 'Username';
$lang['forgot_password_email_identity_label']    = 'Email';
$lang['forgot_password_email_not_found']         = 'Indirizzo email non presente nel database.';
$lang['forgot_password_identity_not_found']         = 'Nessuna registrazione per il nome utente.';

// Reset Password
$lang['reset_password_heading']                               = 'Cambia Password';
$lang['reset_password_new_password_label']                    = 'Nuova Password (lunga almeno %s caratteri):';
$lang['reset_password_new_password_confirm_label']            = 'Conferma Nuova Password:';
$lang['reset_password_submit_btn']                            = 'Cambia';
$lang['reset_password_validation_new_password_label']         = 'Nuova Password';
$lang['reset_password_validation_new_password_confirm_label'] = 'Conferma Nuova Password';

// Activation Email
$lang['email_activate_heading']    = 'Attivare l\'account per %s';
$lang['email_activate_subheading'] = 'Cliccare il seguente link per %s.';
$lang['email_activate_link']       = 'Attiva il tuo Account';

// Forgot Password Email
$lang['email_forgot_password_heading']    = 'Reimposta la Password per %s';
$lang['email_forgot_password_subheading'] = 'Cliccare il seguente link per %s.';
$lang['email_forgot_password_link']       = 'Reimposta la tua Password';


