<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Vietnamese
*
* Author: Trung Dinh Quang
* 		  trungdq88@gmail.com
*         @trungdq88
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  01.17.2015
*
* Description:  Vietnamese language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful'] 	  	 = 'Đã khởi tạo tài khoản thành công';
$lang['account_creation_unsuccessful'] 	 	 = 'Không thể tạo tài khoản vào lúc này';
$lang['account_creation_duplicate_email'] 	 = 'Địa chỉ email không hợp lệ hoặc đã được sử dụng';
$lang['account_creation_duplicate_identity'] = 'Tên tài khoản không hợp lệ hoặc đã được sử dụng';

// Password
$lang['password_change_successful'] 	 	 = 'Đã thay đổi mật khẩu thành công';
$lang['password_change_unsuccessful'] 	  	 = 'Không thể thay đổi mật khẩu vào lúc này';
$lang['forgot_password_successful'] 	 	 = 'Email khôi phục mật khẩu đã được gửi đi';
$lang['forgot_password_unsuccessful'] 	 	 = 'Không thể khôi phục mật khẩu vào lúc này';

// Activation
$lang['activate_successful'] 		  	     = 'Tài khoản đã được kích hoạt';
$lang['activate_unsuccessful'] 		 	     = 'Không thể kích hoạt tài khoản vào lúc này';
$lang['deactivate_successful'] 		  	     = 'Đã khoá tài khoản thành công';
$lang['deactivate_unsuccessful'] 	  	     = 'Không thể bất khoá tài khoản vào lúc này';
$lang['activation_email_successful'] 	  	 = 'Đã gửi mail kích hoạt thành công';
$lang['activation_email_unsuccessful']   	 = 'Không thể gửi mail kích hoạt vào lúc này';
$lang['deactivate_current_user_unsuccessful']= 'You cannot De-Activate your self.';

// Login / Logout
$lang['login_successful'] 		  	         = 'Đăng nhập thành công';
$lang['login_unsuccessful'] 		  	     = 'Tài khoản hoặc mật khẩu không đúng';
$lang['login_unsuccessful_not_active'] 		 = 'Tài khoản này đã bị khoá';
$lang['login_timeout']                       = 'Tài khoản này đã tạm thời bị khoá, vui lòng thử lại sau';
$lang['logout_successful'] 		 	         = 'Đăng xuất thành công';

// Account Changes
$lang['update_successful'] 		 	         = 'Thông tin tài khoản đã được thay đổi thành công';
$lang['update_unsuccessful'] 		 	     = 'Không thể thay đổi thông tin tài khoản vào lúc này';
$lang['delete_successful']               = 'Đã xoá tài khoản';
$lang['delete_unsuccessful']           = 'Không thể xoá tài khoản vào lúc này';

// Groups
$lang['group_creation_successful']  = 'Đã tạo nhóm mới thành công';
$lang['group_already_exists']       = 'Tên nhóm bị trùng';
$lang['group_update_successful']    = 'Đã cập nhật thông tin nhóm thành công';
$lang['group_delete_successful']    = 'Đã xoá nhóm';
$lang['group_delete_unsuccessful'] 	= 'Không thể xoá nhóm vào lúc này';
$lang['group_delete_notallowed']    = 'Can\'t delete the administrators\' group';
$lang['group_name_required'] 		= 'Vui lòng nhập tên nhóm';
$lang['group_name_admin_not_alter'] = 'Admin group name can not be changed';

// Activation Email
$lang['email_activation_subject']            = 'Kích hoạt tài khoản';
$lang['email_activate_heading']    = 'Kích hoạt tài khoản của %s';
$lang['email_activate_subheading'] = 'Vui lòng click vào link này để %s.';
$lang['email_activate_link']       = 'Kích hoạt tài khoản';

// Forgot Password Email
$lang['email_forgotten_password_subject']    = 'Xác nhận quên mật khẩu';
$lang['email_forgot_password_heading']    = 'Khôi phục mật khẩu cho %s';
$lang['email_forgot_password_subheading'] = 'Vui lòng click vào link này để %s.';
$lang['email_forgot_password_link']       = 'Khôi phục mật khẩu của bạn';

