<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - English
*
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
*
* Author: Rachasak Ragkamnerd
* 		  id513128@gmail.com
*         @itpcc
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  03.14.2010
* modify :  10.11.2014
*
* Description:  Thai language file for Ion Auth messages and errors based from English version
*
*/

// Account Creation
$lang['account_creation_successful'] 	  	 = 'สร้างบัญชีสำเร็จ';
$lang['account_creation_unsuccessful'] 	 	 = 'ไม่สามารถสร้างบัญชีได้';
$lang['account_creation_duplicate_email'] 	 = 'อีเมล์นี้ถูกใช้ไปแล้วหรือรูปแบบไม่ถูกต้อง';
$lang['account_creation_duplicate_identity'] = 'ชื่อผู้ใช้นี้ถูกใช้ไปแล้วหรือรูปแบบไม่ถูกต้อง';
$lang['account_creation_missing_default_group'] = 'กลุ่มปริยายยังไม่ถูกตั้ง';
$lang['account_creation_invalid_default_group'] = 'ชื่อกลุ่มปริยายตั้งไม่ถูกต้อง';


// Password
$lang['password_change_successful'] 	 	 = 'เปลี่ยนรหัสผ่านสำเร็จ';
$lang['password_change_unsuccessful'] 	  	 = 'ไม่สามารถเปลี่ยนรหัสผ่านได้';
$lang['forgot_password_successful'] 	 	 = 'อีเมล์ล้างรหัสผ่านถูกส่งไปแล้ว';
$lang['forgot_password_unsuccessful'] 	 	 = 'ไม่สามารถล้างรหัสผ่านได้';

// Activation
$lang['activate_successful'] 		  	     = 'บัญชีเปิดใช้แล้ว';
$lang['activate_unsuccessful'] 		 	     = 'ไม่สามารถเปิดใช้บัญชีได้';
$lang['deactivate_successful'] 		  	     = 'บัญชีถูกปิดการใช้งานแล้ว';
$lang['deactivate_unsuccessful'] 	  	     = 'ไม่สามารถปิดการใช้งานบัญชี';
$lang['activation_email_successful'] 	  	 = 'ส่งอีเมล์เปิดใช้งานแล้ว';
$lang['activation_email_unsuccessful']   	 = 'ไม่สามารถส่งอีเมล์เปิดใช้งานรหัสผ่านได้';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful'] 		  	         = 'เข้าสู่ระบบสำเร็จ';
$lang['login_unsuccessful'] 		  	     = 'เข้าสู่ระบบไม่ถูกต้อง';
$lang['login_unsuccessful_not_active'] 		 = 'บัญชีนี้ยังไม่เปิดใช้งาน';
$lang['login_timeout']                       = 'การเข้าสู่ระบบถูกระงับชั่วคราว กรุณาลองใหม่ในภายหลัง.';
$lang['logout_successful'] 		 	         = 'ออกจากระบบสำเร็จ';

// Accounts Changes
$lang['update_successful'] 		 	         = 'แก้ไขข้อมูลบัญชีสำเร็จ';
$lang['update_unsuccessful'] 		 	     = 'ไม่สามารถแก้ไขข้อมูลบัญชี';
$lang['delete_successful']               = 'ผู้ใช้ถูกลบแล้ว';
$lang['delete_unsuccessful']           = 'ไม่สามารถลบผู้ใช้ได้';

// Groups
$lang['group_creation_successful']  = 'สร้างกลุ่มสำเร็จ';
$lang['group_already_exists']       = 'ชื่อกลุ่มถูกใช้ไปแล้ว';
$lang['group_update_successful']    = 'แก้ไขรายละเอียดกลุ่มแล้ว';
$lang['group_delete_successful']    = 'กลุ่มถูกลบแล้ว';
$lang['group_delete_unsuccessful'] 	= 'ไม่สามารถลบกลุ่มได้';
$lang['group_delete_notallowed']    = 'Can\'t delete the administrators\' group';
$lang['group_name_required'] 		= 'ต้องใส่ชื่อกลุ่ม';
$lang['group_name_admin_not_alter'] = 'Admin group name can not be changed';

// Activation Email
$lang['email_activation_subject']            = 'การเปิดใช้บัญชี';
$lang['email_activate_heading']    = 'เปิดใช้บัญชี %s';
$lang['email_activate_subheading'] = 'กรุณาคลิกลิงค์นี้เพื่อ%s';
$lang['email_activate_link']       = 'เปิดใช้Your บัญชี';

// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'การยืนยันลืมรหัสผ่าน';
$lang['email_forgot_password_heading']    = 'ล้างรหัสผ่านสำหรับ%s';
$lang['email_forgot_password_subheading'] = 'กรุณาคลิกลิงค์นี้เพื่อ%s';
$lang['email_forgot_password_link']       = 'ล้างรหัสผ่าน';

