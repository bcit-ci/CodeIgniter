<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Bulgarian
*
* Author: Ivan Kolev
* 		  ivan.kolev@gmail.com
*
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  01.22.2013
*
* Description:  Bulgarian language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful'] 	  	 = 'Регистрацията бе създадена успешно';
$lang['account_creation_unsuccessful'] 	 	 = 'Неуспешен опит за създаване на регистрация';
$lang['account_creation_duplicate_email'] 	 = 'Email адреса е вече използван или невалиден';
$lang['account_creation_duplicate_identity'] = 'Потребителското име е вече използвано или невалидно';

// TODO Please Translate
$lang['account_creation_missing_default_group'] = 'Default group is not set';
$lang['account_creation_invalid_default_group'] = 'Invalid default group name set';

// Password
$lang['password_change_successful'] 	 	 = 'Паролата бе сменена успешно';
$lang['password_change_unsuccessful'] 	  	 = 'Неуспешен опит за смяна на паролата';
$lang['forgot_password_successful'] 	 	 = 'Изпратен е Email за нулиране на паролата';
$lang['forgot_password_unsuccessful'] 	 	 = 'Неуспешен опит за нулиране на паролата';

// Activation
$lang['activate_successful'] 		  	     = 'Регистрацията е активирана';
$lang['activate_unsuccessful'] 		 	     = 'Неуспешен опит за активиране на регистрацията';
$lang['deactivate_successful'] 		  	     = 'Регистрацията е деактивирана';
$lang['deactivate_unsuccessful'] 	  	     = 'Неуспешен опит за деактивиране на регистрацията';
$lang['activation_email_successful'] 	  	 = 'Изпратен е Email за активиране на регистрацията';
$lang['activation_email_unsuccessful']   	 = 'Неуспешен опит за изпращане на Email за активация';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful'] 		  	         = 'Успешен вход в системата';
$lang['login_unsuccessful'] 		  	     = 'Неуспешен вход в системата';
$lang['login_unsuccessful_not_active'] 		 = 'Регистрацията не е активирана';
$lang['login_timeout']                       = 'Временно заключен. Моля опитайте по-късно';
$lang['logout_successful'] 		 	         = 'Успешен изход от системата';

// Account Changes
$lang['update_successful'] 		 	         = 'Регистрацията беше актуализирана успешно';
$lang['update_unsuccessful'] 		 	     = 'Неуспешен опит за актуализиране на регистрацията';
$lang['delete_successful']               = 'Потребителя бе изтрит';
$lang['delete_unsuccessful']           = 'Неуспешен опит за изтриване на потребител';

// Groups
$lang['group_creation_successful']  = 'Групата бе създадена успешно';
$lang['group_already_exists']       = 'Името на групата вече е заето';
$lang['group_update_successful']    = 'Детайлите на групата бяха актуализирани';
$lang['group_delete_successful']    = 'Групата бе изтрита';
$lang['group_delete_unsuccessful'] 	= 'Неуспешен опит за изтриване на групата';
//TO DO Please translate
$lang['group_delete_notallowed']    = 'Can\'t delete the administrators\' group';
$lang['group_name_required'] 		= 'Group name is a required field';
$lang['group_name_admin_not_alter'] = 'Admin group name can not be changed';

//TO DO Please translate
// Activation Email
$lang['email_activation_subject']            = 'Активиране на регистрацията';
$lang['email_activate_heading']    = 'Activate account for %s';
$lang['email_activate_subheading'] = 'Please click this link to %s.';
$lang['email_activate_link']       = 'Activate Your Account';
// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Проверка за забравена парола';
$lang['email_forgot_password_heading']    = 'Reset Password for %s';
$lang['email_forgot_password_subheading'] = 'Please click this link to %s.';
$lang['email_forgot_password_link']       = 'Reset Your Password';
