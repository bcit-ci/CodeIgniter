<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:         Ion Auth Lang - German
*
* Author:       Ben Edmunds
* 		          ben.edmunds@gmail.com
*               @benedmunds
* Translation:  Bernd Hückstädt (akademie@joytopia.net), Benjamin Neu (benny@duxu.de), Max Vogl mail@max-vogl.de
*
*
*
* Location:     http://github.com/benedmunds/ion_auth/
*
* Created:  04.02.2010
* Last-Edit: 23.04.2016
*
* Description:      German language file for Ion Auth messages and errors
* Beschreibung:     Deutsche Sprach-Datei für Ion Auth System- und Fehlermeldungen
*
*/

// Account Creation
$lang['account_creation_successful'] 	  	   = 'Das Benutzerkonto wurde erfolgreich erstellt';
$lang['account_creation_unsuccessful'] 	     = 'Das Benutzerkonto konnte nicht erstellt werden';
$lang['account_creation_duplicate_email']    = 'Die Email Adresse ist ungültig oder wird bereits verwendet';
$lang['account_creation_duplicate_identity'] = 'Der Benutzername ist ungültig oder wird bereits verwendet';

// TODO Please Translate
$lang['account_creation_missing_default_group'] = 'Standard Gruppe ist nicht gesetzt';
$lang['account_creation_invalid_default_group'] = 'Ungültiger Standard Gruppenname';


// Password
$lang['password_change_successful'] 	= 'Das Passwort wurde erfolgreich geändert';
$lang['password_change_unsuccessful'] = 'Das Passwort konnte nicht geändert werden';
$lang['forgot_password_successful'] 	= 'Es wurde eine Email zum Zurücksetzen des Passwortes versandt';
$lang['forgot_password_unsuccessful'] = 'Das Passwort konnte nicht zurückgesetzt werden';

// Activation
$lang['activate_successful'] 		  	   = 'Das Benutzerkonto wurde aktiviert';
$lang['activate_unsuccessful'] 		 	   = 'Das Benutzerkonto konnte nicht aktiviert werden';
$lang['deactivate_successful'] 		  	 = 'Das Benutzerkonto wurde deaktiviert';
$lang['deactivate_unsuccessful'] 	  	 = 'Das Benutzerkonto konnte nicht deaktiviert werden';
$lang['activation_email_successful'] 	 = 'Es wurde eine Email zum Aktivieren des Benutzerkontos versandt';
$lang['activation_email_unsuccessful'] = 'Die Aktivierungsmail konnte nicht versandt werden';
$lang['deactivate_current_user_unsuccessful']= 'Du kannst dich nicht selbst deaktivieren.';

// Login / Logout
$lang['login_successful'] 		  	     = 'Login erfolgreich';
$lang['login_unsuccessful'] 		       = 'Login fehlgeschlagen';
$lang['login_unsuccessful_not_active'] = 'Der Account ist deaktiviert';
$lang['login_timeout']                 = 'Vorübergehend gesperrt. Versuchen Sie es später noch einmal.';
$lang['logout_successful'] 		 	       = 'Logout erfolgreich';

// Account Changes
$lang['update_successful'] 	 = 'Die Konto-Informationen wurden erfolgreich geändert';
$lang['update_unsuccessful'] = 'Die Konto-Informationen konnten nicht geändert werden';
$lang['delete_successful'] 	 = 'Das Benutzerkonto wurde gelöscht';
$lang['delete_unsuccessful'] = 'Das Benutzerkonto konnte nicht gelöscht werden';

// Groups
$lang['group_creation_successful']  = 'Gruppe wurde erfolgreich erstellt';
$lang['group_already_exists']       = 'Gruppenname bereits vergeben';
$lang['group_update_successful']    = 'Gruppendetails aktualisiert';
$lang['group_delete_successful']    = 'Gruppe gelöscht';
$lang['group_delete_unsuccessful'] 	= 'Gruppe konnte nicht gelöscht werden';
$lang['group_delete_notallowed']    = 'Sie können die Administrator Gruppe nicht löschen';
$lang['group_name_required'] 		    = '"Gruppenname" ist ein Pflichtfeld';
$lang['group_name_admin_not_alter'] = 'Admin Gruppenname kann nicht geändert werden';

// Activation Email
$lang['email_activation_subject']  = 'Aktivierung des Kontos';
$lang['email_activate_heading']    = 'Konto aktivieren für %s';
$lang['email_activate_subheading'] = 'Bitte klicken Sie auf diesen Link, um %s.';
$lang['email_activate_link']       = 'Aktivieren Sie Ihr Benutzerkonto';

// Forgot Password Email
$lang['email_forgotten_password_subject'] = 'Vergessenes Kennwort Verifikation';
$lang['email_forgot_password_heading']    = 'Kennwort zurücksetzen für %s';
$lang['email_forgot_password_subheading'] = 'Bitte klicken Sie auf diesen Link, um %s.';
$lang['email_forgot_password_link']       = 'Ihr Kennwort zurückzusetzen';
