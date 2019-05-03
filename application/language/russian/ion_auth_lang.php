<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Russian (UTF-8)
*
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
* Translation:  Petrosyan R.
*             for@petrosyan.rv.ua
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.26.2010
*
* Description:  Russian language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful'] 	  	 = 'Учетная запись успешно создана';
$lang['account_creation_unsuccessful'] 	 	 = 'Невозможно создать учетную запись';
$lang['account_creation_duplicate_email'] 	 = 'Электронная почта используется или некорректна';
$lang['account_creation_duplicate_username'] 	 = 'Имя пользователя существует или некорректно';
$lang['account_creation_missing_default_group'] = 'Группа по умолчанию не установлена';
$lang['account_creation_invalid_default_group'] = 'Группа по умолчанию задана некорректно';

// Password
$lang['password_change_successful'] 	 	 = 'Пароль успешно изменен';
$lang['password_change_unsuccessful'] 	  	 = 'Пароль невозможно изменить';
$lang['forgot_password_successful'] 	 	 = 'Пароль сброшен. На электронную почту отправлено сообщение';
$lang['forgot_password_unsuccessful'] 	 	 = 'Невозможен сброс пароля';

// Activation
$lang['activate_successful'] 		  	 = 'Учетная запись активирована';
$lang['activate_unsuccessful'] 		 	 = 'Не удалось активировать учетную запись';
$lang['deactivate_successful'] 		  	 = 'Учетная запись деактивирована';
$lang['deactivate_unsuccessful'] 	  	 = 'Невозможно деактивировать учетную запись';
$lang['activation_email_successful'] 	  	 = 'Сообщение об активации отправлено';
$lang['activation_email_unsuccessful']   	 = 'Сообщение об активации невозможно отправить';
$lang['deactivate_current_user_unsuccessful']= 'Вы не можете сами деактивировать свою учетную запись';

// Login / Logout
$lang['login_successful'] 		  	 = 'Авторизация прошла успешно';
$lang['login_unsuccessful'] 		  	 = 'Логин/пароль не верен';
$lang['login_unsuccessful_not_active'] 		 = 'Акаунт не активен';
$lang['login_timeout']                       = 'В целях безопасности возможность входа временно заблокирована. Попробуйте зайти позже.';
$lang['logout_successful'] 		 	 = 'Выход успешный';

// Account Changes
$lang['update_successful'] 		 	 = 'Учетная запись успешно обновлена';
$lang['update_unsuccessful'] 		 	 = 'Невозможно обновить учетную запись';
$lang['delete_successful'] 		 	 = 'Учетная запись удалена';
$lang['delete_unsuccessful'] 		 	 = 'Невозможно удалить учетную запись';

// Groups
$lang['group_creation_successful']  = 'Группа создана успешно';
$lang['group_already_exists']       = 'Группа с таким именем уже существует';
$lang['group_update_successful']    = 'Данные группы обновлены успешно';
$lang['group_delete_successful']    = 'Группа удалена';
$lang['group_delete_unsuccessful'] 	= 'Не удалось удалить группу';
$lang['group_delete_notallowed']    = 'Нельзя удалить группу администраторов';
$lang['group_name_required'] 		= 'Имя группы обязательно к заполнению';
// Activation Email
$lang['email_activation_subject']            = 'Активация учетной записи';
$lang['email_activate_heading']    = 'Активировать акаунт с именем  %s';
$lang['email_activate_subheading'] = 'Нажмите на ссылку %s.';
$lang['email_activate_link']       = 'Активировать ваш акаунт';
// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Проверка забытого пароля';
$lang['email_forgot_password_heading']    = 'Сброс пароля для пользователя %s';
$lang['email_forgot_password_subheading'] = 'Нажмите на ссылку для %s.';
$lang['email_forgot_password_link']       = 'Восстановления пароля';
