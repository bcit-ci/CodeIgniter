<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - English
*
* Author: Ben Edmunds
* 		    ben.edmunds@gmail.com
*         @benedmunds
*
* Author: Daniel Davis
*         @ourmaninjapan
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.09.2013
*
* Description:  English language file for Ion Auth example views
*
*/

// Errors
$lang['error_csrf'] = 'Aquest formulari no ha passat els controls de seguretat.';

// Login
$lang['login_heading']         = 'Inicia sessió';
$lang['login_subheading']      = 'Si us plau, entra amb el teu correu-e/nom d\'usuari i contrasenya a continuació.';
$lang['login_identity_label']  = 'Correu-e/Nom d\'usuari:';
$lang['login_password_label']  = 'Contrasenya:';
$lang['login_remember_label']  = 'Recorda\'m:';
$lang['login_submit_btn']      = 'Entra';
$lang['login_forgot_password'] = 'Has oblidat la teva contrasenya?';

// Index
$lang['index_heading']           = 'Usuaris';
$lang['index_subheading']        = 'A continuació es mostra una llista dels usuaris.';
$lang['index_fname_th']          = 'Nom';
$lang['index_lname_th']          = 'Cognom';
$lang['index_email_th']          = 'Correu-e';
$lang['index_groups_th']         = 'Grups';
$lang['index_status_th']         = 'Estat';
$lang['index_action_th']         = 'Acció';
$lang['index_active_link']       = 'Actiu';
$lang['index_inactive_link']     = 'Inactiu';
$lang['index_create_user_link']  = 'Crea un nou usuari';
$lang['index_create_group_link'] = 'Crea un nou grup';

// Deactivate User
$lang['deactivate_heading']                  = 'Desactivar usuari';
$lang['deactivate_subheading']               = 'Estàs segur que vols desactivar l\'usuari \'%s\'';
$lang['deactivate_confirm_y_label']          = 'Sí:';
$lang['deactivate_confirm_n_label']          = 'No:';
$lang['deactivate_submit_btn']               = 'Envia';
$lang['deactivate_validation_confirm_label'] = 'confirmació';
$lang['deactivate_validation_user_id_label'] = 'ID d\'usuari';

// Create User
$lang['create_user_heading']                           = 'Crea Usuari';
$lang['create_user_subheading']                        = 'Si us plau, introdueix la informació dels usuaris a continuació.';
$lang['create_user_fname_label']                       = 'Nom:';
$lang['create_user_lname_label']                       = 'Cognom:';
$lang['create_user_company_label']                     = 'Nom de l\'empresa:';
$lang['create_user_identity_label']                    = 'Identitat:';
$lang['create_user_email_label']                       = 'Correu-e:';
$lang['create_user_phone_label']                       = 'Telèfon:';
$lang['create_user_password_label']                    = 'Contrasenya:';
$lang['create_user_password_confirm_label']            = 'Confirma Contrasenya:';
$lang['create_user_submit_btn']                        = 'Crea Usuari';
$lang['create_user_validation_fname_label']            = 'Nom';
$lang['create_user_validation_lname_label']            = 'Cognom';
$lang['create_user_validation_identity_label']         = 'Identitat';
$lang['create_user_validation_email_label']            = 'Adreça de correu-e';
$lang['create_user_validation_phone_label']            = 'Telèfon';
$lang['create_user_validation_company_label']          = 'Nom de l\'empresa';
$lang['create_user_validation_password_label']         = 'Contrasenya';
$lang['create_user_validation_password_confirm_label'] = 'Confirma Contrasenya';

// Edit User
$lang['edit_user_heading']                           = 'Editar Usuari';
$lang['edit_user_subheading']                        = 'Si us plau, introdueix la informació dels usuaris a continuació.';
$lang['edit_user_fname_label']                       = 'Nom:';
$lang['edit_user_lname_label']                       = 'Cognom:';
$lang['edit_user_company_label']                     = 'Nom de l\'empresa:';
$lang['edit_user_email_label']                       = 'Correu-e:';
$lang['edit_user_phone_label']                       = 'Telèfon:';
$lang['edit_user_password_label']                    = 'Contrasenya: (si es canvia la contrasenya)';
$lang['edit_user_password_confirm_label']            = 'Confirm Contrasenya: (si es canvia la contrasenya)';
$lang['edit_user_groups_heading']                    = 'Membre dels grups';
$lang['edit_user_submit_btn']                        = 'Desar Usuari';
$lang['edit_user_validation_fname_label']            = 'Nom';
$lang['edit_user_validation_lname_label']            = 'Cognom';
$lang['edit_user_validation_email_label']            = 'Adreça de Correu-e';
$lang['edit_user_validation_phone_label']            = 'Telèfon';
$lang['edit_user_validation_company_label']          = 'Nom de l\'empresa';
$lang['edit_user_validation_groups_label']           = 'Grups';
$lang['edit_user_validation_password_label']         = 'Contrasenya';
$lang['edit_user_validation_password_confirm_label'] = 'Confirmació de la Contrasenya';

// Create Group
$lang['create_group_title']                  = 'Crea Grup';
$lang['create_group_heading']                = 'Crea Grup';
$lang['create_group_subheading']             = 'Si us plau, introdueix la informació del grup a continuació.';
$lang['create_group_name_label']             = 'Nom del Grup:';
$lang['create_group_desc_label']             = 'Descripció:';
$lang['create_group_submit_btn']             = 'Crea Grup';
$lang['create_group_validation_name_label']  = 'Nom del Grup';
$lang['create_group_validation_desc_label']  = 'Descripció';

// Edit Group
$lang['edit_group_title']                  = 'Edita Grup';
$lang['edit_group_saved']                  = 'Grup Desat';
$lang['edit_group_heading']                = 'Edita Grup';
$lang['edit_group_subheading']             = 'Si us plau, introdueix la informació del grup a continuació.';
$lang['edit_group_name_label']             = 'Nom del Grup:';
$lang['edit_group_desc_label']             = 'Descripció:';
$lang['edit_group_submit_btn']             = 'Desa Grup';
$lang['edit_group_validation_name_label']  = 'Nom del Grup';
$lang['edit_group_validation_desc_label']  = 'Descripció';

// Change Password
$lang['change_password_heading']                               = 'Canvi de Contrasenya';
$lang['change_password_old_password_label']                    = 'Contrasenya antiga:';
$lang['change_password_new_password_label']                    = 'Contrasenya nova (mínim %s caràcters):';
$lang['change_password_new_password_confirm_label']            = 'Confirmar Contrasenya nova:';
$lang['change_password_submit_btn']                            = 'Canvia';
$lang['change_password_validation_old_password_label']         = 'Contrasenya antiga';
$lang['change_password_validation_new_password_label']         = 'Contrasenya nova';
$lang['change_password_validation_new_password_confirm_label'] = 'Confirmar Contrasenya nova';

// Forgot Password
$lang['forgot_password_heading']                = 'Contrasenya oblidada';
$lang['forgot_password_subheading']             = 'Introdueix el teu %s perquè puguem enviar un correu electrònic per restablir la contrasenya.';
$lang['forgot_password_email_label']            = '%s:';
$lang['forgot_password_submit_btn']             = 'Envia';
$lang['forgot_password_validation_email_label'] = 'Adreça de Correu-e';
$lang['forgot_password_identity_label']         = 'Usuari';
$lang['forgot_password_email_identity_label']   = 'Correu-e';
$lang['forgot_password_email_not_found']        = 'No hi ha registre d\'aquesta adreça de correu electrònic.';
$lang['forgot_password_identity_not_found']         = 'No record of that username address.';

// Reset Password
$lang['reset_password_heading']                               = 'Canvia Contrasenya';
$lang['reset_password_new_password_label']                    = 'Contrasenya nova (mínim %s caràcters):';
$lang['reset_password_new_password_confirm_label']            = 'Confirmar Contrasenya nova:';
$lang['reset_password_submit_btn']                            = 'Canvia';
$lang['reset_password_validation_new_password_label']         = 'Contrasenya nova';
$lang['reset_password_validation_new_password_confirm_label'] = 'Confirmar nova Contrasenya';
