<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Catalan
*
* Author: Wilfrido García Espinosa
* 		    contacto@wilfridogarcia.com
*         @wilfridogarcia
*
* Translation: Oriol Navascuez & duub qnnp
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  05.04.2010
*
* Description:  Catalan language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful']            = 'Compte creat amb èxit';
$lang['account_creation_unsuccessful']          = 'No ha estat possible crear el compte';
$lang['account_creation_duplicate_email']       = 'Email en ús o invàlid';
$lang['account_creation_duplicate_identity']    = 'Nom d&#39;usuari en ús o invàlid';
$lang['account_creation_missing_default_group'] = 'No s&#39;ha establert grup per defecte';
$lang['account_creation_invalid_default_group'] = 'Conjunt de noms de grup per defecte invalid';


// Password
$lang['password_change_successful']				      = 'Contrasenya canviada amb èxit';
$lang['password_change_unsuccessful']			      = 'No ha estat possible canviar la contrasenya';
$lang['forgot_password_successful']				      = 'Nova contrasenya enviada per email';
$lang['forgot_password_unsuccessful']			      = 'No ha estat possible crear una nova contrasenya';

// Activation
$lang['activate_successful']					          = 'Compte activat';
$lang['activate_unsuccessful']			            = 'No ha estat possible activar el compte';
$lang['deactivate_successful']                  = 'Compte desactivat';
$lang['deactivate_unsuccessful']				        = 'No ha estat possible desactivar el compte';
$lang['activation_email_successful']		        = 'Email d&#39;activació enviat';
$lang['activation_email_unsuccessful']			    = 'No ha estat possible enviar l&#39;email d&#39;activació';
$lang['deactivate_current_user_unsuccessful']   = 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful']						            = 'Sessió iniciada amb èxit';
$lang['login_unsuccessful']						          = 'No ha estat possible iniciar sessió';
$lang['login_unsuccessful_not_active'] 		      = 'Compte inactiu';
$lang['login_timeout']                          = 'Temporalment bloquejat. Torna-ho a provar més tard.';
$lang['logout_successful']						          = 'Sessió finalitzada amb èxit';

// Account Changes
$lang['update_successful']						= 'Informació del compte actualitzat amb èxit';
$lang['update_unsuccessful']					= 'No s&#39;ha pogut actualitzar la informació del compte';
$lang['delete_successful']						= 'Usuari eliminat';
$lang['delete_unsuccessful']					= 'No s&#39;ha pogut Eliminar l&#39;usuari';

// Groups
$lang['group_creation_successful']  = 'Grup creat amb èxit';
$lang['group_already_exists']       = 'Nom de grup no disponible';
$lang['group_update_successful']    = 'Actualitzats detalls de grup';
$lang['group_delete_successful']    = 'Grup esborrat';
$lang['group_delete_unsuccessful'] 	= 'No s&#39;ha pogut esborrar el grup';
$lang['group_delete_notallowed']    = 'No es pot eliminar el grup dels administradors';
$lang['group_name_required'] 		    = 'El nom de grup és un camp necessari';
$lang['group_name_admin_not_alter'] = 'El nom del grup Admin no es pot canviar';

// Activation Email
$lang['email_activation_subject']            = 'Activació del compte';
$lang['email_activate_heading']    = 'Activar el compte de %s';
$lang['email_activate_subheading'] = 'Si us plau, cliqueu el link per %s.';
$lang['email_activate_link']       = 'Activa el teu compte';
// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Verificació de contrasenya oblidada';
$lang['email_forgot_password_heading']    = 'Restableix contrasenya a %s';
$lang['email_forgot_password_subheading'] = 'Si us plau, cliqueu el link per %s.';
$lang['email_forgot_password_link']       = 'Restableix la teva contrasenya';
