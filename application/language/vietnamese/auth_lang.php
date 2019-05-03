<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Auth Lang - Vietnamese
*
* Author: Trung Dinh Quang
* 		  trungdq88@gmail.com
*         @trungdq88
*
* Location: http://github.com/benedmunds/ion_auth/
*
* Created:  01.17.2015
*
* Description:  Vietnamese language file for Ion Auth example views
*
*/

// Errors
$lang['error_csrf'] = 'Có lỗi xảy ra trong quá trình đăng nhập.';

// Login
$lang['login_heading']         = 'Đăng nhập';
$lang['login_subheading']      = 'Đăng nhập bằng email.';
$lang['login_identity_label']  = 'Email';
$lang['login_password_label']  = 'Mật khẩu';
$lang['login_remember_label']  = 'Nhớ mật khẩu';
$lang['login_submit_btn']      = 'Đăng nhập';
$lang['login_forgot_password'] = 'Quên mật khẩu?';

// Index
$lang['index_heading']           = 'Tài khoản';
$lang['index_subheading']        = 'Danh sách tài khoản.';
$lang['index_fname_th']          = 'Tên';
$lang['index_lname_th']          = 'Họ';
$lang['index_email_th']          = 'Email';
$lang['index_groups_th']         = 'Nhóm';
$lang['index_status_th']         = 'Trạng thái';
$lang['index_action_th']         = 'Tác vụ';
$lang['index_active_link']       = 'Kích hoạt';
$lang['index_inactive_link']     = 'Khoá';
$lang['index_create_user_link']  = 'Tạo tài khoản mới';
$lang['index_create_group_link'] = 'Tạo nhóm mới';

// Deactivate User
$lang['deactivate_heading']                  = 'Khoá tài khoản';
$lang['deactivate_subheading']               = 'Bạn có chắc chắn muốn khoá tài khoản \'%s\'';
$lang['deactivate_confirm_y_label']          = 'Có:';
$lang['deactivate_confirm_n_label']          = 'Không:';
$lang['deactivate_submit_btn']               = 'Chấp nhận';
$lang['deactivate_validation_confirm_label'] = 'Xác nhận';
$lang['deactivate_validation_user_id_label'] = 'ID Tài khoản';

// Create User
$lang['create_user_heading']                           = 'Tạo tài khoản';
$lang['create_user_subheading']                        = 'Vui lòng nhập các thông tin cần thiết sau.';
$lang['create_user_fname_label']                       = 'Tên:';
$lang['create_user_lname_label']                       = 'Họ:';
$lang['create_user_identity_label']                    = 'Identity:';
$lang['create_user_company_label']                     = 'Công ty:';
$lang['create_user_email_label']                       = 'Email:';
$lang['create_user_phone_label']                       = 'Điện thoại:';
$lang['create_user_password_label']                    = 'Mật khẩu:';
$lang['create_user_password_confirm_label']            = 'Xác nhận mật khẩu:';
$lang['create_user_submit_btn']                        = 'Tạo tài khoản';
$lang['create_user_validation_fname_label']            = 'Tên';
$lang['create_user_validation_lname_label']            = 'Họ';
$lang['create_user_validation_identity_label']         = 'Identity';
$lang['create_user_validation_email_label']            = 'Email';
$lang['create_user_validation_phone1_label']           = 'Số điện thoại (mã vùng)';
$lang['create_user_validation_phone2_label']           = 'Số điện thoại (3 số đầu)';
$lang['create_user_validation_phone3_label']           = 'Số điện thoại (các số còn lại)';
$lang['create_user_validation_company_label']          = 'Tên công ty';
$lang['create_user_validation_password_label']         = 'Mật khẩu';
$lang['create_user_validation_password_confirm_label'] = 'Xác nhận mật khẩu';

// Edit User
$lang['edit_user_heading']                           = 'Sửa thông tin tài khoản';
$lang['edit_user_subheading']                        = 'Vui lòng nhập các thông tin sau.';
$lang['edit_user_fname_label']                       = 'Tên:';
$lang['edit_user_lname_label']                       = 'Họ:';
$lang['edit_user_company_label']                     = 'Tên công ty:';
$lang['edit_user_email_label']                       = 'Email:';
$lang['edit_user_phone_label']                       = 'Số điện thoại:';
$lang['edit_user_password_label']                    = 'Mật khẩu: (nếu có thay đổi)';
$lang['edit_user_password_confirm_label']            = 'Xác nhận mật khẩu: (nếu có thay đổi)';
$lang['edit_user_groups_heading']                    = 'Các nhóm tham gia';
$lang['edit_user_submit_btn']                        = 'Lưu lại';
$lang['edit_user_validation_fname_label']            = 'Tên';
$lang['edit_user_validation_lname_label']            = 'Họ';
$lang['edit_user_validation_email_label']            = 'Email';
$lang['edit_user_validation_phone1_label']           = 'Số điện thoại (mã vùng)';
$lang['edit_user_validation_phone2_label']           = 'Số điện thoại (3 số đầu)';
$lang['edit_user_validation_phone3_label']           = 'Số điện thoại (các số còn lại)';
$lang['edit_user_validation_company_label']          = 'Tên công ty';
$lang['edit_user_validation_groups_label']           = 'Nhóm';
$lang['edit_user_validation_password_label']         = 'Mật khẩu';
$lang['edit_user_validation_password_confirm_label'] = 'Xác nhận mật khẩu';

// Create Group
$lang['create_group_title']                  = 'Tạo nhóm mới';
$lang['create_group_heading']                = 'Tạo nhóm mới';
$lang['create_group_subheading']             = 'Vui lòng nhập các thông tin bên dưới.';
$lang['create_group_name_label']             = 'Tên nhóm:';
$lang['create_group_desc_label']             = 'Mô tả:';
$lang['create_group_submit_btn']             = 'Tạo nhóm';
$lang['create_group_validation_name_label']  = 'Tên nhóm';
$lang['create_group_validation_desc_label']  = 'Mô tả';

// Edit Group
$lang['edit_group_title']                  = 'Sửa thông tin nhóm';
$lang['edit_group_saved']                  = 'Đã lưu';
$lang['edit_group_heading']                = 'Sửa thông tin nhóm';
$lang['edit_group_subheading']             = 'Vui lòng nhập các thông tin bên dưới.';
$lang['edit_group_name_label']             = 'Tên nhóm:';
$lang['edit_group_desc_label']             = 'Mô tả:';
$lang['edit_group_submit_btn']             = 'Lưu lại';
$lang['edit_group_validation_name_label']  = 'Tên nhóm';
$lang['edit_group_validation_desc_label']  = 'Mô tả';

// Change Password
$lang['change_password_heading']                               = 'Đổi mật khẩu';
$lang['change_password_old_password_label']                    = 'Mật khẩu cũ:';
$lang['change_password_new_password_label']                    = 'Mật khẩu mới (ít nhất %s ký tự):';
$lang['change_password_new_password_confirm_label']            = 'Xác nhận mật khẩu mới:';
$lang['change_password_submit_btn']                            = 'Lưu lại';
$lang['change_password_validation_old_password_label']         = 'Mật khẩu cũ';
$lang['change_password_validation_new_password_label']         = 'Mật khẩu mới';
$lang['change_password_validation_new_password_confirm_label'] = 'Xác nhận mật khẩu mới';

// Forgot Password
$lang['forgot_password_heading']                 = 'Quên mật khẩu';
$lang['forgot_password_subheading']              = 'Vui lòng nhập %s để nhận được email khôi phục mật khẩu.';
$lang['forgot_password_email_label']             = '%s:';
$lang['forgot_password_submit_btn']              = 'Xác nhận';
$lang['forgot_password_validation_email_label']  = 'Email';
$lang['forgot_password_username_identity_label'] = 'Tài khoản';
$lang['forgot_password_email_identity_label']    = 'Email';
$lang['forgot_password_email_not_found']         = 'Địa chỉ email không tồn tại.';
$lang['forgot_password_identity_not_found']         = 'No record of that username address.';

// Reset Password
$lang['reset_password_heading']                               = 'Đổi mật khẩu';
$lang['reset_password_new_password_label']                    = 'Mật khẩu mới (ít nhất %s ký tự):';
$lang['reset_password_new_password_confirm_label']            = 'Xác nhận mật khẩu mới:';
$lang['reset_password_submit_btn']                            = 'Lưu lại';
$lang['reset_password_validation_new_password_label']         = 'Mật khẩu mới';
$lang['reset_password_validation_new_password_confirm_label'] = 'Xác nhận mật khẩu mới';
