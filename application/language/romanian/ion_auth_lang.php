<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Romanian
*
* Author: Adrian Voicu
* 		  avenir.ro@gmail.com
*         @benedmunds
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  10.09.2013
*
* Description:  Romanian language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful'] 	  	 	= 'Cont creat cu succes';
$lang['account_creation_unsuccessful'] 	 	 	= 'Nu am reușit să creez contul';
$lang['account_creation_duplicate_email'] 	 	= 'Email deja folosit sau invalid';
$lang['account_creation_duplicate_identity'] 	= 'Numele de utilizator este deja folosit sau este invalid';
$lang['account_creation_missing_default_group'] = 'Grupul prestabilit nu a fost setat';
$lang['account_creation_invalid_default_group'] = 'Ați setat un nume greșit pentru grupul prestabilit';

// Password
$lang['password_change_successful'] 	 	 = 'Parolă schimbată cu succes';
$lang['password_change_unsuccessful'] 	  	 = 'Nu am reușit să schimb parola';
$lang['forgot_password_successful'] 	 	 = 'Emailul de resetare a parolei a fost trimis';
$lang['forgot_password_unsuccessful'] 	 	 = 'Nu am reușit să resetez parola';

// Activation
$lang['activate_successful'] 		  	     = 'Cont activat';
$lang['activate_unsuccessful'] 		 	     = 'Nu am reușit să activez contul';
$lang['deactivate_successful'] 		  	     = 'Cont dezactivat';
$lang['deactivate_unsuccessful'] 	  	     = 'Nu am reușit să dezactivez contul';
$lang['activation_email_successful'] 	  	 = 'Mailul de activare a fost trimis';
$lang['activation_email_unsuccessful']   	 = 'Nu am reușit să trimit mailul de activare';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful'] 		  	         = 'Conectarea a reușit';
$lang['login_unsuccessful'] 		  	     = 'Date de logare incorecte';
$lang['login_unsuccessful_not_active'] 		 = 'Contul este dezactivat';
$lang['login_timeout']                       = 'Ați fost temporar scos din sesiune. Încercați mai tarziu.';
$lang['logout_successful'] 		 	         = 'Deconectarea a reușit';

// Account Changes
$lang['update_successful'] 		 	         = 'Informațiile contului au fost actualizate cu succes';
$lang['update_unsuccessful'] 		 	     = 'Nu am reușit să actualizez informațiile contului';
$lang['delete_successful'] 		 	         = 'Utilizator șters';
$lang['delete_unsuccessful'] 		 	     = 'Nu am reușit să șterg utilizatorul';

// Groups
$lang['group_creation_successful']  		= 'Grup creat cu succes';
$lang['group_already_exists']       		= 'Numele de grup a fost deja utilizat';
$lang['group_update_successful']    		= 'Detaliile grupului au fost actualizate';
$lang['group_delete_successful']    		= 'Grup șters cu succes';
$lang['group_delete_unsuccessful'] 			= 'Nu am putut șterge grupul';
$lang['group_delete_notallowed']    		= 'Nu pot șterge grupul administratorilor';
$lang['group_name_required'] 				= 'Este necesar un nume pentru grup';
$lang['group_name_admin_not_alter'] 		= 'Numele grupului administratorilor nu poate fi schimbat';

// Activation Email
$lang['email_activation_subject']           = 'Activarea contului';
$lang['email_activate_heading']    			= 'Activarea contului pentru %s';
$lang['email_activate_subheading'] 			= 'Dați clic pe această adresă pentru %s.';
$lang['email_activate_link']       			= 'Activarea contul';

// Forgot Password Email
$lang['email_forgotten_password_subject']   = 'Verificarea parolei uitate';
$lang['email_forgot_password_heading']    	= 'Resetarea parolei pentru %s';
$lang['email_forgot_password_subheading'] 	= 'Dați clic pe această adresă pentru %s.';
$lang['email_forgot_password_link']       	= 'Resetarea parolei';

