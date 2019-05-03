<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Name:  Auth Lang - Chinese Traditional
 *
 * Author: Bo-Yi Wu
 *         appleboy.tw@gmail.com
 *         @taiwan
 *
 * Location: http://github.com/benedmunds/ion_auth/
 *
 * Created:  03.09.2013
 *
 * Description: Chinese language language file for Ion Auth example views
 *
 */

// Errors
$lang['error_csrf'] = '此表單內容資訊沒通過系統安全認證.';

// Login
$lang['login_heading']         = '登入';
$lang['login_subheading']      = '請登入您的電子郵件/帳號和密碼.';
$lang['login_identity_label']  = '電子郵件/帳號:';
$lang['login_password_label']  = '密碼:';
$lang['login_remember_label']  = '記住我:';
$lang['login_submit_btn']      = '登入';
$lang['login_forgot_password'] = '忘記密碼?';

// Index
$lang['index_heading']           = '使用者資訊';
$lang['index_subheading']        = '底下是帳號資訊列表.';
$lang['index_fname_th']          = '名字';
$lang['index_lname_th']          = '姓氏';
$lang['index_email_th']          = '電子郵件';
$lang['index_groups_th']         = '群組';
$lang['index_status_th']         = '狀態';
$lang['index_action_th']         = '動作';
$lang['index_active_link']       = '啟動';
$lang['index_inactive_link']     = '關閉';
$lang['index_create_user_link']  = '建立新帳號';
$lang['index_create_group_link'] = '建立新群組';

// Deactivate User
$lang['deactivate_heading']                  = '關閉帳號';
$lang['deactivate_subheading']               = '您確定關閉此使用者帳號 \'%s\'';
$lang['deactivate_confirm_y_label']          = '是:';
$lang['deactivate_confirm_n_label']          = '否:';
$lang['deactivate_submit_btn']               = '送出';
$lang['deactivate_validation_confirm_label'] = '確認';
$lang['deactivate_validation_user_id_label'] = '帳號 ID';

// Create User
$lang['create_user_heading']                           = '建立帳號';
$lang['create_user_subheading']                        = '請填寫使用者帳號基本資料.';
$lang['create_user_fname_label']                       = '名字:';
$lang['create_user_lname_label']                       = '姓氏:';
$lang['create_user_identity_label']                    = '帳號:';
$lang['create_user_company_label']                     = '公司名稱:';
$lang['create_user_email_label']                       = '電子郵件:';
$lang['create_user_phone_label']                       = '電話:';
$lang['create_user_password_label']                    = '密碼:';
$lang['create_user_password_confirm_label']            = '確認密碼:';
$lang['create_user_submit_btn']                        = '建立帳號';
$lang['create_user_validation_fname_label']            = '名字';
$lang['create_user_validation_lname_label']            = '姓氏';
$lang['create_user_validation_identity_label']         = '帳號';
$lang['create_user_validation_email_label']            = '電子郵件';
$lang['create_user_validation_phone1_label']           = '聯絡電話一';
$lang['create_user_validation_phone2_label']           = '聯絡電話二';
$lang['create_user_validation_phone3_label']           = '聯絡電話三';
$lang['create_user_validation_company_label']          = '公司名稱';
$lang['create_user_validation_password_label']         = '密碼';
$lang['create_user_validation_password_confirm_label'] = '確認密碼';

// Edit User
$lang['edit_user_heading']                           = '修改帳號';
$lang['edit_user_subheading']                        = '請填寫使用者帳號基本資料.';
$lang['edit_user_fname_label']                       = '名字:';
$lang['edit_user_lname_label']                       = '姓氏:';
$lang['edit_user_company_label']                     = '公司名稱:';
$lang['edit_user_email_label']                       = '電子郵件:';
$lang['edit_user_phone_label']                       = '聯絡電話:';
$lang['edit_user_password_label']                    = '密碼: (如果要修改密碼請填寫)';
$lang['edit_user_password_confirm_label']            = '確認密碼: (如果要修改密碼請填寫)';
$lang['edit_user_groups_heading']                    = '使用者群組';
$lang['edit_user_submit_btn']                        = '儲存帳號';
$lang['edit_user_validation_fname_label']            = '名字';
$lang['edit_user_validation_lname_label']            = '姓氏';
$lang['edit_user_validation_email_label']            = '電子郵件';
$lang['edit_user_validation_phone1_label']           = '聯絡電話一';
$lang['edit_user_validation_phone2_label']           = '聯絡電話二';
$lang['edit_user_validation_phone3_label']           = '聯絡電話三';
$lang['edit_user_validation_company_label']          = '公司名稱';
$lang['edit_user_validation_groups_label']           = '群組';
$lang['edit_user_validation_password_label']         = '密碼';
$lang['edit_user_validation_password_confirm_label'] = '確認密碼';

// Create Group
$lang['create_group_title']                  = '建立群組';
$lang['create_group_heading']                = '建立群組';
$lang['create_group_subheading']             = '請填寫群組基本資料.';
$lang['create_group_name_label']             = '群組名稱:';
$lang['create_group_desc_label']             = '群組描述:';
$lang['create_group_submit_btn']             = '建立群組';
$lang['create_group_validation_name_label']  = '群組名稱';
$lang['create_group_validation_desc_label']  = '群組描述';

// Edit Group
$lang['edit_group_title']                  = '修改群組';
$lang['edit_group_saved']                  = '儲存群組';
$lang['edit_group_heading']                = '修改群組';
$lang['edit_group_subheading']             = '請填寫群組基本資料.';
$lang['edit_group_name_label']             = '群組名稱:';
$lang['edit_group_desc_label']             = '群組描述:';
$lang['edit_group_submit_btn']             = '儲存群組';
$lang['edit_group_validation_name_label']  = '群組名稱';
$lang['edit_group_validation_desc_label']  = '群組描述';

// Change Password
$lang['change_password_heading']                               = '修改密碼';
$lang['change_password_old_password_label']                    = '舊密碼:';
$lang['change_password_new_password_label']                    = '新密碼 (至少含 %s 字元長度):';
$lang['change_password_new_password_confirm_label']            = '確認新密碼:';
$lang['change_password_submit_btn']                            = '修改';
$lang['change_password_validation_old_password_label']         = '舊密碼';
$lang['change_password_validation_new_password_label']         = '新密碼';
$lang['change_password_validation_new_password_confirm_label'] = '確認新密碼';

// Forgot Password
$lang['forgot_password_heading']                 = '忘記密碼';
$lang['forgot_password_subheading']              = '請填寫您的%s，以便讓我們寄送電子郵件重新啟用密碼.';
$lang['forgot_password_email_label']             = '%s:';
$lang['forgot_password_submit_btn']              = '送出';
$lang['forgot_password_validation_email_label']  = '電子郵件';
$lang['forgot_password_username_identity_label'] = '帳號';
$lang['forgot_password_email_identity_label']    = '電子郵件';
$lang['forgot_password_email_not_found']         = '找不到此電子郵件相關資訊.';
$lang['forgot_password_identity_not_found']         = 'No record of that username address.';

// Reset Password
$lang['reset_password_heading']                               = '修改密碼';
$lang['reset_password_new_password_label']                    = '新密碼 (至少含 %s 字元長度):';
$lang['reset_password_new_password_confirm_label']            = '確認新密碼:';
$lang['reset_password_submit_btn']                            = '修改';
$lang['reset_password_validation_new_password_label']         = '新密碼';
$lang['reset_password_validation_new_password_confirm_label'] = '確認新密碼';
