<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Italian
*
* Author: Ben Edmunds
*         ben.edmunds@gmail.com
*         @benedmunds
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  07.08.2010
*
* Description:  Italian language file for Ion Auth messages and errors
* translation:   Antonio Frignani (www.thinkers.it)
*/

// Account Creation
$lang['account_creation_successful'] 	  	 = 'Account creato con successo.';
$lang['account_creation_unsuccessful'] 	 	 = 'Impossibile creare l\'account.';
$lang['account_creation_duplicate_email'] 	 = 'Email gi&agrave; in uso o non valida.';
$lang['account_creation_duplicate_identity'] 	 = 'Nome utente gi&agrave; in uso o non valido.';
$lang['account_creation_missing_default_group'] = 'Gruppo predefinito non impostato';
$lang['account_creation_invalid_default_group'] = 'Nome del gruppo predefinito non valido';


// Password
$lang['password_change_successful'] 	 	 = 'Password modificata con successo.';
$lang['password_change_unsuccessful'] 	  	 = 'Impossibile modificare la password.';
$lang['forgot_password_successful'] 	 	 = 'Email di reset della password inviata.';
$lang['forgot_password_unsuccessful'] 	 	 = 'Impossibile resettare la password.';

// Activation
$lang['activate_successful'] 		  	 = 'Account attivato.';
$lang['activate_unsuccessful'] 		 	 = 'Impossibile attivare l\'account.';
$lang['deactivate_successful'] 		  	 = 'Account disattivato.';
$lang['deactivate_unsuccessful'] 	  	 = 'Impossibile disattivare l\'account.';
$lang['activation_email_successful'] 	  	 = 'Email di attivazione inviata.';
$lang['activation_email_unsuccessful']   	 = 'Impossibile inviare l\'email di attivazione.';
$lang['deactivate_current_user_unsuccessful']= 'Non puoi disattivare te stesso.';

// Login / Logout
$lang['login_successful'] 		  	 = 'Accesso effettuato con successo.';
$lang['login_unsuccessful'] 		  	 = 'Accesso non corretto.';
$lang['login_unsuccessful_not_active'] 		 = 'Account non attivo.';
$lang['login_timeout']                       = 'Temporaneamente bloccato. Riprovare pi&ugrave; tardi.';
$lang['logout_successful'] 		 	 = 'Disconnessione effettuata con successo.';

// Account Changes
$lang['update_successful'] 		 	 = 'Informazioni dell\'account aggiornate con successo.';
$lang['update_unsuccessful'] 		 	 = 'Impossibile aggiornare le informazioni dell\'account.';
$lang['delete_successful'] 		 	 = 'Utente eliminato.';
$lang['delete_unsuccessful'] 		 	 = 'Impossibile eliminare l\'utente.';

// Groups
$lang['group_creation_successful']  = 'Gruppo creato con successo';
$lang['group_already_exists']       = 'Nome gruppo gi&agrave; assegnato';
$lang['group_update_successful']    = 'Dettagli gruppo aggiornati';
$lang['group_delete_successful']    = 'Gruppo cancellato';
$lang['group_delete_unsuccessful'] 	= 'Impossibile cancellare il gruppo';
$lang['group_delete_notallowed']    = 'Impossibile eliminare il gruppo amministratori';
$lang['group_name_required'] 		= 'Il nome gruppo &egrave; un campo obbligatorio';
$lang['group_name_admin_not_alter'] = 'Il nome del gruppo amministratori non pu&ograve; essere modificato';

// Activation Email
$lang['email_activation_subject']            = 'Attivazione Account';
$lang['email_activate_heading']    = 'Attiva account per %s';
$lang['email_activate_subheading'] = 'Si prega di cliccare su questo collegamento per %s.';
$lang['email_activate_link']       = 'Attiva il tuo Account';
// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Verifica il cambio password dimenticata';
$lang['email_forgot_password_heading']    = 'Reimposta Password per %s';
$lang['email_forgot_password_subheading'] = 'Si prega di cliccare su questo collegamento per %s.';
$lang['email_forgot_password_link']       = 'Reimposta la tua Password';
