<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Polish
*
* Author: Bart Majewski
* 		  hello@bartoszmajewski.pl
*         @bart_majewski
 * Updates: Slawomir Jasinski
 * 			slav123@gmail.com
 * 			@slavomirj
 *
 *			vertisan
 *			vertisan@vrs-factory.pl
 *			@vertisan
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.23.2010
* Updated:  13.03.2016
*
* Description:  Polish language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful'] 		 = 'Konto zostało pomyślnie założone';
$lang['account_creation_unsuccessful'] 		 = 'Nie można utworzyć konta';
$lang['account_creation_duplicate_email'] 	 = 'Podany adres Email jest nieprawidłowy lub został już użyty';
$lang['account_creation_duplicate_identity'] = 'Podana nazwa użytkownika jest nieprawidłowa lub została już użyta';

// TODO Please Translate
$lang['account_creation_missing_default_group'] = 'Domyślna grupa nie jest ustawiona';
$lang['account_creation_invalid_default_group'] = 'Nieprawidłowa nazwa domyślnej grupy';


// Password
$lang['password_change_successful'] 		 = 'Hasło zostało pomyślnie zmienione';
$lang['password_change_unsuccessful'] 		 = 'Nie można zmienić hasła';
$lang['forgot_password_successful'] 		 = 'Nowe hasło zostało wysłane';
$lang['forgot_password_unsuccessful'] 		 = 'Nie można zresetować hasła';

// Activation
$lang['activate_successful'] 			     = 'Konto zostało aktywowane';
$lang['activate_unsuccessful'] 				 = 'Nie można aktywować konta';
$lang['deactivate_successful'] 				 = 'Konto zostało deaktywowane';
$lang['deactivate_unsuccessful'] 			 = 'Nie można deaktywować konta';
$lang['activation_email_successful'] 		 = 'Na twój adres E-mail został wysłany link aktywacyjny';
$lang['activation_email_unsuccessful'] 		 = 'Nie można wysłać linku aktywacyjnego';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful'] 					 = 'Użytkownik został pomyślnie zalogowany';
$lang['login_unsuccessful'] 			     = 'Nieprawidłowy login';
$lang['login_unsuccessful_not_active'] 		 = 'Konto nie jest aktywne';
$lang['login_timeout']                       = 'Konto tymczasowo zablokowane. Spróbuj ponownie później';
$lang['logout_successful'] 					 = 'Użytkownik został pomyślnie wylogowany';

// Account Changes
$lang['update_successful'] 					 = 'Konto zostało pomyślnie uaktualnione';
$lang['update_unsuccessful'] 				 = 'Nie można uaktualnić konta';
$lang['delete_successful'] 					 = 'Użytkownik został skasowany';
$lang['delete_unsuccessful'] 				 = 'Nie można skasować użytkownika';

// Groups
$lang['group_creation_successful']  = 'Grupa została utworzona pomyślnie';
$lang['group_already_exists']       = 'Podana grupa już istnieje!';
$lang['group_update_successful']    = 'Grupa została zaktualizowana';
$lang['group_delete_successful']    = 'Grupa została usunięta';
$lang['group_delete_unsuccessful'] 	= 'Unable to delete group';
$lang['group_delete_notallowed']    = 'Nie można usunąć grupy administracyjnej';
$lang['group_name_required'] 		= 'Nazwa grupy jest wymagana';
$lang['group_name_admin_not_alter'] = 'Nazwa grupy administracyjnej nie może zostać zmieniona!';

// Activation Email
$lang['email_activation_subject']  = 'Aktywacja Konta';
$lang['email_activate_heading']    = 'Aktywuj konto dla %s';
$lang['email_activate_subheading'] = 'Przejdź do tego adresu aby aktywować swoje konto %s.';
$lang['email_activate_link']       = 'Aktywacja konta';
// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Resetowanie hasła';
$lang['email_forgot_password_heading']    	 = 'Zresetuj hasło dla %s';
$lang['email_forgot_password_subheading'] 	 = 'Przejdź do tego adresu aby zresetować swoje hasło %s.';
$lang['email_forgot_password_link']       	 = 'Resetowanie hasła';
