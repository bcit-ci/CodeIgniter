<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - Polish
*
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
*
* Translation: Piotr Fuz
*         piotr.fuz@gmail.com
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:    03.09.2013
* Translated: 18.05.2017
*
* Description:  Polish language file for Ion Auth example views
*
*/

// Login
$lang['login_heading']         = 'Login';
$lang['login_subheading']      = 'Zaloguj się poniżej używając email/username oraz hasła.';
$lang['login_identity_label']  = 'Nazwa użytkownika:';
$lang['login_password_label']  = 'Hało:';
$lang['login_remember_label']  = 'Pamiętaj mnie:';
$lang['login_submit_btn']      = 'Login';
$lang['login_forgot_password'] = 'Zapomniałeś hasła?';

// Index
$lang['index_heading']           = 'Użytkownicy';
$lang['index_subheading']        = 'Lista użytkowników poniżej.';
$lang['index_fname_th']          = 'Imię';
$lang['index_lname_th']          = 'Nazwisko';
$lang['index_email_th']          = 'Email';
$lang['index_groups_th']         = 'Grupy';
$lang['index_status_th']         = 'Status';
$lang['index_action_th']         = 'Akcja';
$lang['index_active_link']       = 'Aktywny';
$lang['index_inactive_link']     = 'Nieaktywny';
$lang['index_create_user_link']  = 'Utwórz nowego użykownika';
$lang['index_create_group_link'] = 'Utwórz nową grupę';

// Deactivate User
$lang['deactivate_heading']         		 = 'Deaktywuj użytkownika';
$lang['deactivate_subheading']      		 = 'Czy jesteś pewny, że chcesz deaktywować użytkownika \'%s\'';
$lang['deactivate_confirm_y_label'] 		 = 'Tak:';
$lang['deactivate_confirm_n_label'] 		 = 'Nie:';
$lang['deactivate_submit_btn']      		 = 'Wyślij';
$lang['deactivate_validation_confirm_label'] = 'potwierdzenie';
$lang['deactivate_validation_user_id_label'] = 'ID użytkownika';

// Create User
$lang['create_user_heading']                		   = 'Dodaj użytkownika';
$lang['create_user_subheading']             		   = 'Wprowadź poniżej dane użytkownika.';
$lang['create_user_fname_label']            		   = 'Imię:';
$lang['create_user_lname_label']            		   = 'Nazwisko:';
$lang['create_user_identity_label']                    = 'Nazwa użytkownika:';
$lang['create_user_company_label']          		   = 'Nazwa firmy:';
$lang['create_user_email_label']           			   = 'Email:';
$lang['create_user_phone_label']            		   = 'Telefon:';
$lang['create_user_password_label']         		   = 'Hasło:';
$lang['create_user_password_confirm_label'] 		   = 'Potwierdź hasło:';
$lang['create_user_submit_btn']             		   = 'Utwórz użytkownika';
$lang['create_user_validation_fname_label']            = 'Imię';
$lang['create_user_validation_lname_label']            = 'Nazwisko';
$lang['create_user_validation_identity_label']         = 'Nazwa użytkownika';
$lang['create_user_validation_email_label']            = 'Adres e-mail';
$lang['create_user_validation_phone_label']            = 'Telefon';
$lang['create_user_validation_phone1_label']           = 'Telefon - część pierwsza';
$lang['create_user_validation_phone2_label']           = 'Telefon - część druga';
$lang['create_user_validation_phone3_label']           = 'Telefon - część trzecia';
$lang['create_user_validation_company_label']          = 'Nazwa firmy';
$lang['create_user_validation_password_label']         = 'Hasło';
$lang['create_user_validation_password_confirm_label'] = 'Potwierdź hasło';

// Edit User
$lang['edit_user_heading']               		     = 'Edytuj użytkownika';
$lang['edit_user_subheading']             			 = 'Proszę wprowadzić poniżej dane użykownika.';
$lang['edit_user_fname_label']            			 = 'Imię:';
$lang['edit_user_lname_label']            			 = 'Nazwisko:';
$lang['edit_user_company_label']          			 = 'Nazwa firmy:';
$lang['edit_user_email_label']            			 = 'Email:';
$lang['edit_user_phone_label']            			 = 'Telefon:';
$lang['edit_user_password_label']         			 = 'Hasło (jeśli zmieniasz hasło)';
$lang['edit_user_password_confirm_label'] 			 = 'Potwierdź hasło: (jeśli zmieniasz hasło)';
$lang['edit_user_groups_heading']        		     = 'Czlonek grupy';
$lang['edit_user_submit_btn']             			 = 'Zapisz użytkownika';
$lang['edit_user_validation_phone_label']            = 'Telefon';
$lang['edit_user_validation_fname_label']            = 'Imię';
$lang['edit_user_validation_lname_label']            = 'Nazwisko';
$lang['edit_user_validation_email_label']            = 'Adres e-mail';

$lang['edit_user_validation_phone1_label']           = 'Telefon - część pierwsza';
$lang['edit_user_validation_phone2_label']           = 'Telefon - część druga';
$lang['edit_user_validation_phone3_label']           = 'Telefon - część trzecia';
$lang['edit_user_validation_company_label']          = 'Nazwa firmy';
$lang['edit_user_validation_groups_label']           = 'Grupa';
$lang['edit_user_validation_password_label']         = 'Hasło';
$lang['edit_user_validation_password_confirm_label'] = 'Potwierdź hasło';

// Create Group
$lang['create_group_title']                  = 'Utwórz grupę';
$lang['create_group_heading']     			 = 'Utwórz grupę';
$lang['create_group_subheading']  			 = 'Wprowadź poniżej dane dla nowej grupy.';
$lang['create_group_name_label'] 			 = 'Nazwa grupy:';
$lang['create_group_desc_label']  	         = 'Opis:';
$lang['create_group_submit_btn']  			 = 'Utwórz grupę';
$lang['create_group_validation_name_label']  = 'Nazwa grupy';
$lang['create_group_validation_desc_label']  = 'Opis';

// Edit Group
$lang['edit_group_heading']     		   = 'Edytuj grupę';
$lang['edit_group_subheading']  		   = 'Wprowadź poniżej dane grupy.';
$lang['edit_group_name_label'] 			   = 'Nazwa grupy';
$lang['edit_group_desc_label']  		   = 'Opis:';
$lang['edit_group_submit_btn']  		   = 'Zapisz';
$lang['edit_group_validation_name_label']  = 'Nazwa grupy';
$lang['edit_group_validation_desc_label']  = 'Opis';

// Change Password
$lang['change_password_heading']                    		   = 'Zmień hasło';
$lang['change_password_old_password_label']         		   = 'Stare hasło:';
$lang['change_password_new_password_label']         		   = 'Nowe hasło (minimum %s znaków):';
$lang['change_password_new_password_confirm_label'] 		   = 'Potwierdź nowe hasło:';
$lang['change_password_submit_btn']                 		   = 'Zmień';
$lang['change_password_validation_old_password_label']         = 'Stare hasło';
$lang['change_password_validation_new_password_label']         = 'Nowe hasło';
$lang['change_password_validation_new_password_confirm_label'] = 'Potwierdź nowe hasło';

// Forgot Password
$lang['forgot_password_heading']                 = 'Przypomnienie hasła';
$lang['forgot_password_subheading']              = 'Proszę wprowadź swój %s ayśmy mogli wysłać Ci email do zresetowania hasła.';
$lang['forgot_password_email_label']             = '%s:';
$lang['forgot_password_submit_btn']              = 'Wyślij';
$lang['forgot_password_validation_email_label']  = 'Adres email';
$lang['forgot_password_username_identity_label'] = 'Nazwa użytkownika';
$lang['forgot_password_email_identity_label']    = 'Email';
$lang['forgot_password_email_not_found']         = 'Nie znaleziono w bazie użytkownika o tym adresie.';
$lang['forgot_password_identity_not_found']      = 'Nie znaleziono użytkownika o tym adresie email.';

// Reset Password
$lang['reset_password_heading']                    			  = 'Zmiana hasła';
$lang['reset_password_new_password_label']         			  = 'Nowe hasło (minimum %s znaków):';
$lang['reset_password_new_password_confirm_label'] 			  = 'Potwierdź nowe hasło:';
$lang['reset_password_submit_btn']                 			  = 'Zmień';
$lang['reset_password_validation_new_password_label']         = 'Nowe hasło';
$lang['reset_password_validation_new_password_confirm_label'] = 'Potwierdź nowe hasło';

// Activation Email
$lang['email_activate_heading']    = 'Aktywuj konto dla %s';
$lang['email_activate_subheading'] = 'Proszę klilknąć na link aby %s.';
$lang['email_activate_link']       = 'Aktywuj nowe konto';

// Forgot Password Email
$lang['email_forgot_password_heading']    = 'Zresetuj hasło dla %s';
$lang['email_forgot_password_subheading'] = 'Proszę klilknąć na link aby %s.';
$lang['email_forgot_password_link']       = 'Resetuj hasło';

