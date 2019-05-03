<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - English
*
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
*
* Author: Jakub Vatrt
*         vatrtj@gmail.com
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  11.11.2016
*
* Description:  English language file for Ion Auth example views
*
*/

// Errors
$lang['error_csrf'] = 'Tento formulár neprešiel bezpečnostnou kontrolou.';

// Login
$lang['login_heading']         = 'Prihlásenie';
$lang['login_subheading'] = 'Prosím prihláste sa nižšie pomocou svojho emailu alebo užívateľským menom a heslom';
$lang['login_identity_label'] = 'E-mail / Užívateľské meno:';
$lang['login_password_label'] = 'Heslo';
$lang['login_remember_label'] = 'Zampamätať:';
$lang['login_submit_btn'] = 'Prihlásiť';
$lang['login_forgot_password'] = 'Zabudli ste heslo?';

// Index
$lang['index_heading'] = 'Používatelia';
$lang['index_subheading'] = 'Nižšie je zoznam používateľov.';
$lang['index_fname_th'] = 'Meno';
$lang['index_lname_th'] = 'Priezvisko';
$lang['index_email_th'] = 'Email';
$lang['index_groups_th'] = 'Skupiny';
$lang['index_status_th'] = 'Stav';
$lang['index_action_th'] = 'Akcia';
$lang['index_active_link'] = 'Aktívne';
$lang['index_inactive_link'] = 'Neaktívne';
$lang['index_create_user_link'] = 'Vytvoriť nového používateľa';
$lang['index_create_group_link'] = 'Vytvoriť novú skupinu';

// Deactivate User
$lang['deactivate_heading'] = 'Deaktivovať používateľa';
$lang['deactivate_subheading'] = 'Ste si istí, že chcete deaktivovať užívateľa \'%s\'';
$lang['deactivate_confirm_y_label'] = 'Áno';
$lang['deactivate_confirm_n_label'] = 'Nie';
$lang['deactivate_submit_btn'] = 'Odoslať';
$lang['deactivate_validation_confirm_label'] = 'potvrdenie';
$lang['deactivate_validation_user_id_label'] = 'ID používateľa';

// Create User
$lang['create_user_heading'] = 'Vytvoriť používateľa';
$lang['create_user_subheading'] = 'Prosím, zadajte informácie o používateľovi nižšie.';
$lang['create_user_fname_label'] = 'Meno:';
$lang['create_user_lname_label'] = 'Priezvisko:';
$lang['create_user_identity_label'] = 'Identita:';
$lang['create_user_company_label'] = 'Názov spoločnosti:';
$lang['create_user_email_label'] = 'E-mail:';
$lang['create_user_phone_label'] = 'Telefón:';
$lang['create_user_password_label'] = 'Heslo';
$lang['create_user_password_confirm_label'] = 'Potvrdiť heslo:';
$lang['create_user_submit_btn'] = 'Vytvoriť používateľa';
$lang['create_user_validation_fname_label'] = 'Meno';
$lang['create_user_validation_lname_label'] = 'Priezvisko';
$lang['create_user_validation_identity_label'] = 'Identita';
$lang['create_user_validation_email_label'] = 'E-mailová adresa';
$lang['create_user_validation_phone_label'] = 'Phone';
$lang['create_user_validation_company_label'] = 'Názov spoločnosti';
$lang['create_user_validation_password_label'] = 'Heslo';
$lang['create_user_validation_password_confirm_label'] = 'Heslo na potvrdenie';

// Edit User
$lang['edit_user_heading'] = 'Upraviť používateľa';
$lang['edit_user_subheading'] = 'Prosím, zadajte informácie o používateľovi nižšie.';
$lang['edit_user_fname_label'] = 'Meno:';
$lang['edit_user_lname_label'] = 'Priezvisko:';
$lang['edit_user_company_label'] = 'Názov spoločnosti:';
$lang['edit_user_email_label'] = 'E-mail:';
$lang['edit_user_phone_label'] = 'Telefón:';
$lang['edit_user_password_label'] = 'Heslo: (ak meníte heslo)';
$lang['edit_user_password_confirm_label'] = 'Potvrdiť heslo: (ak meníte heslo)';
$lang['edit_user_groups_heading'] = 'Člen používateľskej skupiny';
$lang['edit_user_submit_btn'] = 'Uložiť používateľa';
$lang['edit_user_validation_fname_label'] = 'Meno';
$lang['edit_user_validation_lname_label'] = 'Priezvisko';
$lang['edit_user_validation_email_label'] = 'E-mailová adresa';
$lang['edit_user_validation_phone_label'] = 'Telefón';
$lang['edit_user_validation_company_label'] = 'Názov spoločnosti';
$lang['edit_user_validation_groups_label'] = 'Skupiny používateľa';
$lang['edit_user_validation_password_label'] = 'Heslo';
$lang['edit_user_validation_password_confirm_label'] = 'Heslo na potvrdenie';

// Create Group
$lang['create_group_title'] = 'Vytvoriť skupinu';
$lang['create_group_heading'] = 'Vytvoriť skupinu';
$lang['create_group_subheading'] = 'Prosím, zadajte nižšie informácie o skupine.';
$lang['create_group_name_label'] = 'Názov skupiny:';
$lang['create_group_desc_label'] = 'Popis:';
$lang['create_group_submit_btn'] = 'Vytvoriť skupinu';
$lang['create_group_validation_name_label'] = 'Názov skupiny';
$lang['create_group_validation_desc_label'] = 'Popis';

// Edit Group
$lang['edit_group_title'] = 'Upraviť skupinu';
$lang['edit_group_saved'] = 'Skupina uložená';
$lang['edit_group_heading'] = 'Upraviť skupinu';
$lang['edit_group_subheading'] = 'Prosím, zadajte informácie o skupine nižšie.';
$lang['edit_group_name_label'] = 'Názov skupiny:';
$lang['edit_group_desc_label'] = 'Popis:';
$lang['edit_group_submit_btn'] = 'Uložiť skupinu';
$lang['edit_group_validation_name_label'] = 'Názov skupiny';
$lang['edit_group_validation_desc_label'] = 'Popis';

// Change Password
$lang['change_password_heading'] = 'Zmena hesla';
$lang['change_password_old_password_label'] = 'Staré heslo:';
$lang['change_password_new_password_label'] = 'Nové heslo (najmenej %s znakov):';
$lang['change_password_new_password_confirm_label'] = 'Potvrdiť nové heslo:';
$lang['change_password_submit_btn'] = 'Zmeniť';
$lang['change_password_validation_old_password_label'] = 'Staré heslo';
$lang['change_password_validation_new_password_label'] = 'Nové heslo';
$lang['change_password_validation_new_password_confirm_label'] = 'Potvrdiť nové heslo';

// Forgot Password
$lang['forgot_password_heading'] = 'Zabudli ste heslo';
$lang['forgot_password_subheading'] = 'Zadajte prosím vašu %s, takže vám môžeme poslať e-mail pre resetovanie hesla';
$lang['forgot_password_email_label'] = '%s:';
$lang['forgot_password_submit_btn'] = 'Odoslať';
$lang['forgot_password_validation_email_label'] = 'E-mailová adresa';
$lang['forgot_password_username_identity_label'] = 'Používateľské meno';
$lang['forgot_password_email_identity_label'] = 'Email';
$lang['forgot_password_email_not_found'] = 'Žiadny záznam s toutu e-mailovou adresou.';
$lang['forgot_password_identity_not_found']         = 'No record of that username address.';

// Reset Password
$lang['reset_password_heading'] = 'Zmena hesla';
$lang['reset_password_new_password_label'] = 'Nové heslo (najmenej% s znakov):';
$lang['reset_password_new_password_confirm_label'] = 'Potvrdiť nové heslo:';
$lang['reset_password_submit_btn'] = 'Zmeniť';
$lang['reset_password_validation_new_password_label'] = 'Nové heslo';
$lang['reset_password_validation_new_password_confirm_label'] = 'Potvrdiť nové heslo';

// Activation Email
$lang['email_activate_heading'] = 'Aktivovať účet pre %s';
$lang['email activate nadpis'] = 'Prosím kliknite na tento odkaz %s.';
$lang['email_activate_link'] = 'Aktivácia vášho účtu';

// Forgot Password Email
$lang['email_forgot_password_heading'] = 'Vytvoriť nové heslo pre %s';
$lang['email_forgot_password_subheading'] = 'Prosím kliknite na tento odkaz %s.';
$lang['email_forgot_password_link'] = 'Reset hesla';

$lang['email new_password nadpis'] = 'Vaše heslo bolo obnovené: %s';

