<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Hungarian
* 
* Author: Balazs Bosternak
* 		    b.bosternak@gmail.com
* 
* Location: http://github.com/benedmunds/ion_auth/
*          
* Created:  07.19.2015 
* 
* Description:  Hungarian language file for Ion Auth messages and errors
* 
*/

// Account Creation
$lang['account_creation_successful'] 	  	      = 'Felhasználói fiók sikeresen létrehozva';
$lang['account_creation_unsuccessful'] 	 	 	    = 'Nem lehet létrehozni a felhasználói fiókot';
$lang['account_creation_duplicate_email'] 	 	  = 'Az email cím használatban van vagy érvénytelen';
$lang['account_creation_duplicate_identity'] 	  = 'A felhasználó név használatban van vagy érvénytelen';
$lang['account_creation_missing_default_group'] = 'Alapértelmezett csoport nincs megadva';
$lang['account_creation_invalid_default_group'] = 'Érvénytelen alapértelmezett csoport név';

// Password
$lang['password_change_successful'] 	 	 	= 'A jelszó sikeresen megváltoztatva';
$lang['password_change_unsuccessful']     = 'Nem lehet megváltoztatni a jelszót';
$lang['forgot_password_successful'] 	 	 	= 'A jelszó törlő email elküldve';
$lang['forgot_password_unsuccessful'] 	  = 'Nem lehet törölni a jelszót';

// Activation
$lang['activate_successful']					  = 'Felhasználói fiók aktiválva';
$lang['activate_unsuccessful'] 		 	  	= 'Nem lehet a felhasználói fiókot aktiválni';
$lang['deactivate_successful'] 		    	= 'Felhasználói fiók inaktiválva';
$lang['deactivate_unsuccessful'] 	      = 'Nem lehet a felhasználói fiókot inaktiválni';
$lang['activation_email_successful'] 	  = 'Aktivációs email elküldve';
$lang['activation_email_unsuccessful']  = 'Nem lehet elküldeni az aktivációs emailt';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful'] 		  	 			= 'Sikeres bejelentkés';
$lang['login_unsuccessful'] 		  	 		= 'Sikertelen bejelentkezés';
$lang['login_unsuccessful_not_active']  = 'Felhasználói fiók inaktív';
$lang['login_timeout']                  = 'Ideiglenesen zárolva... Próbálja meg később.';
$lang['logout_successful'] 		 	 			  = 'Sikeres kijelentkezés';

// Account Changes
$lang['update_successful'] 		 	 			= 'Felhasználói fiók adatai sikeresen módosítva';
$lang['update_unsuccessful'] 		 	 		= 'Nem lehet a felhasználói fiók adatait módosítani';
$lang['delete_successful'] 		 	 			= 'Felhasználó törölve';
$lang['delete_unsuccessful'] 		 	 		= 'Nem lehet a felhasználót törölni';

// Groups
$lang['group_creation_successful']  			= 'Csoport sikeresen létrehozva';
$lang['group_already_exists']       			= 'A csoport már létezik';
$lang['group_update_successful']    			= 'Csoport adatai sikeresen módosítva';
$lang['group_delete_successful']    			= 'Csoport törölve';
$lang['group_delete_unsuccessful'] 				= 'Nem lehet a csoportot törölni';
$lang['group_delete_notallowed']    			= 'Az adminisztrátorok csoport nem törölhető';
$lang['group_name_required'] 				    	= 'A csoport neve kötelező mező';
$lang['group_name_admin_not_alter'] 			= 'Az admin csoport neve nem változtatható meg';

// Activation Email
$lang['email_activation_subject']         = 'Felhasználói fiók aktiválása';
$lang['email_activate_heading']    				= '%s felhasználói fiókjának aktiválása';
$lang['email_activate_subheading'] 				= 'Kattintson a linkre, hogy %s.';
$lang['email_activate_link']       				= 'Aktiválja felhasználói fiókját';

// Forgot Password Email
$lang['email_forgotten_password_subject']    	= 'Elfelejtett jelszó visszaigazolása';
$lang['email_forgot_password_heading']    		= 'Új jelszó beállítása %s számára';
$lang['email_forgot_password_subheading'] 		= 'Kattintson a linkre az %s érdekében.';
$lang['email_forgot_password_link']       		= 'Új jelszó beállítása';

