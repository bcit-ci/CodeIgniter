<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Ukraine (UTF-8)
*
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
* Translation:  Petrosyan R.
*             for@petrosyan.rv.ua
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.27.2010
*
* Description:  Ukraine language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful']		= 'Обліковий запис успішно створено';
$lang['account_creation_unsuccessful']		= 'Неможливо створити обліковий запис';
$lang['account_creation_duplicate_email']	= 'Електронна пошта використовується або некоректна';
$lang['account_creation_duplicate_identity']    = 'Ім`я користувача існує або некоректне';

// TODO Please Translate
$lang['account_creation_missing_default_group'] = 'Група за умовчанням не встановлена';
$lang['account_creation_invalid_default_group'] = 'Група за умовчанням задана некоректно';

// Password
$lang['password_change_successful']		= 'Пароль успішно змінено';
$lang['password_change_unsuccessful']		= 'Пароль неможливо змінити';
$lang['forgot_password_successful']		= 'Пароль скинутий. На електронну пошту відправлено повідомлення';
$lang['forgot_password_unsuccessful']		= 'Неможливе скидання пароля';

// Activation
$lang['activate_successful']		= 'Обліковий запис активовано';
$lang['activate_unsuccessful']		= 'Не вдалося активувати обліковий запис';
$lang['deactivate_successful']          = 'Обліковий запис деактивовано';
$lang['deactivate_unsuccessful']        = 'Неможливо деактивувати обліковий запис';
$lang['activation_email_successful']    = 'Повідомлення про активацію відправлено';
$lang['activation_email_unsuccessful']  = 'Повідомлення про активацію неможливо відправити';
$lang['deactivate_current_user_unsuccessful']= 'Ви не можете самі деактивувати свій обліковий запис';

// Login / Logout
$lang['login_successful']		= 'Авторизація пройшла успішно';
$lang['login_unsuccessful']		= 'Логін невірний';
$lang['login_unsuccessful_not_active'] 	= 'Обліковий запис не активований';
$lang['login_timeout']			= 'В цілях безпеки можливість входу тимчасово заблокована. Спробуйте зайти пізніше.';
$lang['logout_successful']		= 'Вихід успішний';

// Account Changes
$lang['update_successful']		= 'Обліковий запис успішно оновлено';
$lang['update_unsuccessful']		= 'Неможливо оновити обліковий запис';
$lang['delete_successful']		= 'Обліковий запис видалено';
$lang['delete_unsuccessful']		= 'Неможливо видалити обліковий запис';

// Groups
$lang['group_creation_successful']  = 'Група створена успішно';
$lang['group_already_exists']       = 'Група з таким ім\'ям вже існує';
$lang['group_update_successful']    = 'Дані групи оновлені успішно';
$lang['group_delete_successful']    = 'Група видалена';
$lang['group_delete_unsuccessful']  = 'Не вдалося видалити групу';
$lang['group_delete_notallowed']    = 'Не можна видалити групу адміністраторів';
$lang['group_name_required'] 	    = 'Ім\'я групи обов\'язкове до заповнення';
$lang['group_name_admin_not_alter'] = 'Admin group name can not be changed';

// Activation Email
$lang['email_activation_subject']  = 'Активація облікового запису';
$lang['email_activate_heading']    = 'Активувати акаунт з ім\'ям  %s';
$lang['email_activate_subheading'] = 'Натисніть на посилання %s.';
$lang['email_activate_link']       = 'Активувати ваш акаунт';
// Forgot Password Email
$lang['email_forgotten_password_subject']	= 'Перевірка забутого пароля';
$lang['email_forgot_password_heading']		= 'Скидання пароля для користувача %s';
$lang['email_forgot_password_subheading']	= 'Натисніть на посилання для %s.';
$lang['email_forgot_password_link']		= 'Відновлення пароля';
