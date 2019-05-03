<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Lithuanian (UTF-8)
*
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
* Translation:  Radas7
*             radas7@gmail.com
*               Donatas Glodenis
*             dgvirtual@akl.lt
*
* Created:  2012-03-04
* Updated:  2016-05-13
*
* Description:  Lithuanian language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful'] 	  	 = 'Vartotojas sėkmingai sukurtas';
$lang['account_creation_unsuccessful'] 	 	 = 'Neįmanoma sukurti vartotojo';
$lang['account_creation_duplicate_email'] 	 = 'El, pašto adresas jau yra arba neteisingas';
$lang['account_creation_duplicate_identity'] 	 = 'Prisijungimo vardas jau yra arba nekorektiškas';

// TODO Please Translate
$lang['account_creation_missing_default_group'] = 'Nenustatyta numatytoji grupė';
$lang['account_creation_invalid_default_group'] = 'Nustatytas neteisingas numatytosios grupės pavadinimas';

// Password
$lang['password_change_successful'] 	 	 = 'Slaptažodis sukurtas';
$lang['password_change_unsuccessful'] 	  	 = 'Negalima paeisti slaptažodžio';
$lang['forgot_password_successful'] 	 	 = 'Slaptažodis keičiamas. Instrukcijos išsiųstos paštu.';
$lang['forgot_password_unsuccessful'] 	 	 = 'Neįmanoma pakeisti slaptažodžio';

// Activation
$lang['activate_successful'] 		  	 = 'Vartotojas aktyvuotas';
$lang['activate_unsuccessful'] 		 	 = 'Nepavyko aktyvuoti';
$lang['deactivate_successful'] 		  	 = 'Deaktyvuota';
$lang['deactivate_unsuccessful'] 	  	 = 'Neįmanoma deaktyvuoti';
$lang['activation_email_successful'] 	  	 = 'Išsiųstas pranešimas į el. paštą';
$lang['activation_email_unsuccessful']   	 = 'Neįmanoma išsiųsti';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful'] 		  	 = 'Sėkminga autorizacija';
$lang['login_unsuccessful'] 		  	 = 'Klaidingas prisijungimas';
$lang['login_unsuccessful_not_active'] 		 = 'Paskyra yra neaktyvi';
$lang['login_timeout']                       = 'Laikinai užrakinta. Pabandykite iš naujo vėliau.';
$lang['logout_successful'] 		 	 = 'Atsijungta sėkminga';

// Account Changes
$lang['update_successful'] 		 	 = 'Vartotojo duomenys sėkmingai pakeisti';
$lang['update_unsuccessful'] 		 	 = 'Neįmanoma pakeisti vartotojo duoemnų';
$lang['delete_successful'] 		 	 = 'Vartotojas pašalintas';
$lang['delete_unsuccessful'] 		 	 = 'Neįmanoma pašalinti vartotojo';

// Groups
$lang['group_creation_successful']  = 'Grupė sėkmingai sukurta';
$lang['group_already_exists']       = 'Grupės vardas jau naudojamas';
$lang['group_update_successful']    = 'Grupės detalės atnaujintos';
$lang['group_delete_successful']    = 'Grupė ištrinta';
$lang['group_delete_unsuccessful'] 	= 'Nepavyksta ištrinti grupės';
$lang['group_delete_notallowed']    = 'Administratorių grupės ištrinti negalima';
$lang['group_name_required'] 		= 'Grupės vardą užpildyti būtina';
$lang['group_name_admin_not_alter'] = 'Admin grupė negali būti pakeista';

// Activation Email
$lang['email_activation_subject']            = 'Paskyros aktyvavimas';
$lang['email_activate_heading']    = 'Aktyvuoti %s paskyrą';
$lang['email_activate_subheading'] = 'Prašome spragtelėti %s nuorodą.';
$lang['email_activate_link']       = 'Aktyvuokite savo paskyrą';
// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Pamiršto slaptažodžio patvirtinimas';
$lang['email_forgot_password_heading']    = 'Iš naujo generuoti %s slaptažodį';
$lang['email_forgot_password_subheading'] = 'Prašome paspausti nuorodą norėdami %s.';
$lang['email_forgot_password_link']       = 'Perkrauti slaptažodį';
