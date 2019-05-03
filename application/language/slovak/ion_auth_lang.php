<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Slovak
*
* Author: Jakub Vatrt
* 		  vatrtj@gmail.com
*
*
* Created:  11.11.2016
*
* Description:  Slovak language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful'] 	  	 = 'Účet bol úspešne vytvorený';
$lang['account_creation_unsuccessful'] 	 	 = 'Nie je možné vytvoriť účet';
$lang['account_creation_duplicate_email'] 	 = 'E-mail už existuje alebo je neplatný';
$lang['account_creation_duplicate_identity'] = 'Užívateľské meno už existuje alebo je neplatné';

// TODO Please Translate
$lang['account_creation_missing_default_group'] = 'Základná skupina nenastavená';
$lang['account_creation_invalid_default_group'] = 'Nesprávne meno základnej skupiny';

// Password
$lang['password_change_successful'] 	 	 = 'Heslo bolo úspešne zmenené';
$lang['password_change_unsuccessful'] 	  	 = 'Nie je možné zmeniť heslo';
$lang['forgot_password_successful'] 	 	 = 'Heslo bolo odoslané na e-mail';
$lang['forgot_password_unsuccessful'] 	 	 = 'Nie je možné obnoviť heslo';

// Activation
$lang['activate_successful'] 		  	     = 'Účet bol aktivovaný';
$lang['activate_unsuccessful'] 		 	     = 'Nie je možné aktivovať účet';
$lang['deactivate_successful'] 		  	     = 'Účet bol deaktivovaný';
$lang['deactivate_unsuccessful'] 	  	     = 'Nie je možné deaktivovať účet';
$lang['activation_email_successful'] 	  	 = 'Aktivačný e-mail bol odoslaný';
$lang['activation_email_unsuccessful']   	 = 'Nedá sa odoslať aktivačný e-mail';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful'] 		  	         = 'Úspešne prihlásený';
$lang['login_unsuccessful'] 		  	     = 'Nesprávny e-mail alebo heslo';
$lang['login_unsuccessful_not_active'] 		 = 'Účet je neaktívny';
$lang['login_timeout']                       = 'Dočasne uzamknuté z bezpečnostných dôvodov. Skúste neskôr';
$lang['logout_successful'] 		 	         = 'Úspešné odhlásenie';

// Account Changes
$lang['update_successful'] 		 	         = 'Informácie o účte boli úspešne aktualizované';
$lang['update_unsuccessful'] 		 	     = 'Informácie o účte sa nedájú aktualizovať';
$lang['delete_successful'] 		 	         = 'Užívateľ bol zmazaný';
$lang['delete_unsuccessful'] 		 	     = 'Užívateľ sa nedá zmazať ';

// Groups
$lang['group_creation_successful']  = 'Skupina úspešne vytvorená';
$lang['group_already_exists']       = 'Meno skupiny už existuje';
$lang['group_update_successful']    = 'Detaily skupiny upravené';
$lang['group_delete_successful']    = 'Skupina zmazaná';
$lang['group_delete_unsuccessful'] 	= 'Nemôžem zmazať skupinu';
$lang['group_delete_notallowed']    = 'Nemôžem zmazať administrátorskú skupinu';
$lang['group_name_required'] 		= 'Meno skupiny je požadované pole';
$lang['group_name_admin_not_alter'] = 'Administratorská skupina nemôže byť zmenená';

// Activation Email
$lang['email_activation_subject']            = 'Aktivácia účtu';
$lang['email_activate_heading']    = 'Aktivujte účet na %s';
$lang['email_activate_subheading'] = 'Prosím kliknite na tento odkaz pre %s.';
$lang['email_activate_link']       = 'Aktivujte váš účet';
// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Obnovenie hesla kontrola';
$lang['email_forgot_password_heading']    = 'Obnoviť heslo pre %s';
$lang['email_forgot_password_subheading'] = 'Prosím kliknite na tento odkaz pre %s.';
$lang['email_forgot_password_link']       = 'Reset vášho hesla';
